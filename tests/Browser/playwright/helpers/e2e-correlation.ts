import { randomUUID } from 'node:crypto';
import { existsSync, readFileSync } from 'node:fs';
import path from 'node:path';
import type { Page, TestInfo } from '@playwright/test';

const logPath = path.resolve(process.cwd(), process.env.E2E_LOG_PATH ?? 'storage/app/e2e/logs/e2e.jsonl');
const suiteRunId = process.env.E2E_RUN_ID ?? `run-${new Date().toISOString().replace(/[^0-9]/g, '').slice(0, 14)}-${randomUUID().slice(0, 8)}`;

export type E2ECorrelation = {
    runId: string;
    scenarioId: string;
};

export async function applyE2ECorrelation(page: Page, scenarioId: string): Promise<E2ECorrelation> {
    const correlation = {
        runId: suiteRunId,
        scenarioId: normalizeId(scenarioId),
    };

    await page.setExtraHTTPHeaders({
        'X-E2E-Run-Id': correlation.runId,
        'X-E2E-Scenario-Id': correlation.scenarioId,
    });

    return correlation;
}

export async function attachCorrelatedServerLogs(testInfo: TestInfo, correlation: E2ECorrelation): Promise<void> {
    const records = readCorrelatedRecords(correlation);
    await testInfo.attach('laravel-correlated-logs', {
        contentType: 'application/x-ndjson',
        body: Buffer.from(records.map(record => JSON.stringify(record)).join('\n')),
    });
}

function readCorrelatedRecords(correlation: E2ECorrelation): unknown[] {
    if (! existsSync(logPath)) {
        return [];
    }

    return readFileSync(logPath, 'utf8')
        .split(/\r?\n/)
        .filter(Boolean)
        .flatMap(line => {
            try {
                const record = JSON.parse(line) as Record<string, unknown>;
                const context = (record.context ?? {}) as Record<string, unknown>;
                return context.e2e_run_id === correlation.runId && context.e2e_scenario_id === correlation.scenarioId
                    ? [record]
                    : [];
            } catch {
                return [];
            }
        });
}

function normalizeId(value: string): string {
    const normalized = value.toLowerCase().replace(/[^a-z0-9._:-]+/g, '-').replace(/^[.-]+|[.-]+$/g, '');
    return (normalized || `scenario-${randomUUID()}`).slice(0, 120);
}
