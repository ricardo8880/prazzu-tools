import { expect, type Download, type Page, type TestInfo } from '@playwright/test';
import { mkdirSync, readFileSync } from 'node:fs';
import { basename, extname, resolve } from 'node:path';

export type DownloadExpectation = {
    id: string;
    test_id: string;
    format: 'pdf' | 'xlsx' | 'csv' | 'docx' | 'zip';
    extension: string;
    minimum_bytes: number;
    filename_contains?: string | null;
    mime_type?: string | null;
    required_entries: string[];
};

type DownloadValidation = {
    id: string;
    filename: string;
    path: string;
    size_bytes: number;
    format: string;
    mime_type: string | null;
    zip_entries: string[];
    checks: string[];
};

function normalizedMime(value: string | undefined): string | null {
    return value?.split(';', 1)[0]?.trim().toLowerCase() || null;
}

function zipEntries(buffer: Buffer): string[] {
    const entries: string[] = [];
    for (let offset = 0; offset <= buffer.length - 46; offset += 1) {
        if (buffer.readUInt32LE(offset) !== 0x02014b50) continue;
        const nameLength = buffer.readUInt16LE(offset + 28);
        const extraLength = buffer.readUInt16LE(offset + 30);
        const commentLength = buffer.readUInt16LE(offset + 32);
        const start = offset + 46;
        const end = start + nameLength;
        if (end > buffer.length) break;
        entries.push(buffer.subarray(start, end).toString('utf8'));
        offset = end + extraLength + commentLength - 1;
    }
    return entries;
}

function assertNotHtml(buffer: Buffer, filename: string): void {
    const beginning = buffer.subarray(0, Math.min(buffer.length, 1024)).toString('utf8').trimStart().toLowerCase();
    expect(beginning, `${filename} contém HTML em vez do arquivo esperado.`).not.toMatch(/^(<!doctype html|<html|<head|<body)/);
}

function validateBuffer(buffer: Buffer, filename: string, expectation: DownloadExpectation): string[] {
    const checks: string[] = [];
    expect(buffer.length, `${filename} está abaixo do tamanho mínimo.`).toBeGreaterThanOrEqual(expectation.minimum_bytes);
    checks.push(`minimum-bytes:${expectation.minimum_bytes}`);

    const extension = extname(filename).replace('.', '').toLowerCase();
    expect(extension, `Extensão inesperada em ${filename}.`).toBe(expectation.extension.toLowerCase());
    checks.push(`extension:${extension}`);

    assertNotHtml(buffer, filename);
    checks.push('not-html');

    if (expectation.format === 'pdf') {
        expect(buffer.subarray(0, 5).toString('ascii')).toBe('%PDF-');
        expect(buffer.subarray(Math.max(0, buffer.length - 2048)).toString('latin1')).toContain('%%EOF');
        checks.push('pdf-header', 'pdf-eof');
    }

    if (['xlsx', 'docx', 'zip'].includes(expectation.format)) {
        expect(buffer.readUInt32LE(0)).toBe(0x04034b50);
        const entries = zipEntries(buffer);
        expect(entries.length, `${filename} não possui diretório central ZIP legível.`).toBeGreaterThan(0);
        for (const required of expectation.required_entries) {
            expect(entries, `${filename} não contém a entrada obrigatória ${required}.`).toContain(required);
        }
        if (expectation.format === 'xlsx') {
            expect(entries).toContain('[Content_Types].xml');
            expect(entries).toContain('xl/workbook.xml');
        }
        if (expectation.format === 'docx') {
            expect(entries).toContain('[Content_Types].xml');
            expect(entries).toContain('word/document.xml');
        }
        checks.push('zip-signature', `zip-entries:${entries.length}`);
    }

    if (expectation.format === 'csv') {
        const text = buffer.toString('utf8').replace(/^\uFEFF/, '');
        expect(text.trim().length).toBeGreaterThan(0);
        expect(text.split(/\r?\n/).length).toBeGreaterThanOrEqual(2);
        checks.push('csv-non-empty', 'csv-has-header-and-row');
    }

    return checks;
}

export async function executeAndValidateDownload(
    page: Page,
    expectation: DownloadExpectation,
    testInfo: TestInfo,
): Promise<DownloadValidation> {
    const responsePromise = page.waitForResponse(response => {
        const disposition = response.headers()['content-disposition'] ?? '';
        return disposition.toLowerCase().includes('attachment');
    });
    const downloadPromise = page.waitForEvent('download');

    await page.getByTestId(expectation.test_id).click();

    const [download, response] = await Promise.all([downloadPromise, responsePromise]);
    const suggestedFilename = download.suggestedFilename();
    const safeFilename = `${expectation.id}-${basename(suggestedFilename).replace(/[^a-zA-Z0-9._-]+/g, '-')}`;
    const directory = resolve(process.cwd(), 'storage/app/e2e/artifacts/downloads', testInfo.project.name);
    mkdirSync(directory, { recursive: true });
    const output = resolve(directory, safeFilename);
    await download.saveAs(output);

    const failure = await download.failure();
    expect(failure, `O navegador informou falha no download ${expectation.id}.`).toBeNull();

    const buffer = readFileSync(output);
    const checks = validateBuffer(buffer, suggestedFilename, expectation);

    if (expectation.filename_contains) {
        expect(suggestedFilename.toLowerCase()).toContain(expectation.filename_contains.toLowerCase());
        checks.push(`filename-contains:${expectation.filename_contains}`);
    }

    const mimeType = normalizedMime(response.headers()['content-type']);
    if (expectation.mime_type) {
        expect(mimeType, `MIME type divergente no download ${expectation.id}.`).toBe(expectation.mime_type.toLowerCase());
        checks.push(`mime:${mimeType}`);
    }

    const entries = ['xlsx', 'docx', 'zip'].includes(expectation.format) ? zipEntries(buffer) : [];
    await testInfo.attach(`download-${expectation.id}`, { path: output });

    const validation: DownloadValidation = {
        id: expectation.id,
        filename: suggestedFilename,
        path: output,
        size_bytes: buffer.length,
        format: expectation.format,
        mime_type: mimeType,
        zip_entries: entries,
        checks,
    };
    await testInfo.attach(`download-${expectation.id}-validation`, {
        contentType: 'application/json',
        body: Buffer.from(JSON.stringify(validation, null, 2)),
    });

    return validation;
}
