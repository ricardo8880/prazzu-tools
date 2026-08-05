import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

export type SmokeTool = {
    id: number;
    key: string;
    name: string;
    module: string;
    slug: string;
    path: string;
    risk: 'critical' | 'high' | 'moderate' | 'low';
    surfaces: string[];
    download_formats: string[];
    test_ids: {
        page: string;
        form: string;
    };
};

type ToolCatalogManifest = {
    schema_version: string;
    tool_count: number;
    tools: SmokeTool[];
};

export function loadToolCatalog(): ToolCatalogManifest {
    const path = resolve(process.cwd(), 'storage/app/e2e/runtime/tool-catalog.json');
    const manifest = JSON.parse(readFileSync(path, 'utf8')) as ToolCatalogManifest;

    if (manifest.schema_version !== '1.0.0') {
        throw new Error(`Versão desconhecida do manifesto E2E: ${manifest.schema_version}`);
    }
    if (manifest.tool_count !== manifest.tools.length) {
        throw new Error('A contagem declarada no manifesto E2E diverge da lista de ferramentas.');
    }
    if (new Set(manifest.tools.map(tool => tool.slug)).size !== manifest.tools.length) {
        throw new Error('O manifesto E2E contém slugs duplicados.');
    }

    return manifest;
}
