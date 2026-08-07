import { spawnSync } from 'node:child_process';

const rawSlug = process.argv[2]?.trim();

if (!rawSlug) {
    console.error('\n[E2E TOOL] Informe o slug da ferramenta.');
    console.error('[E2E TOOL] Exemplo: npm run e2e:test:tool gerador-de-contratos\n');
    process.exit(1);
}

if (!/^[a-z0-9]+(?:-[a-z0-9]+)*$/.test(rawSlug)) {
    console.error(`\n[E2E TOOL] Slug inválido: "${rawSlug}".`);
    console.error('[E2E TOOL] Use o slug da URL, por exemplo: gerador-de-contratos\n');
    process.exit(1);
}

console.log(`\n[E2E TOOL] Teste direcionado: ${rawSlug}`);
console.log(`[E2E TOOL] Página: /ferramentas/${rawSlug}\n`);

const playwrightCli = './node_modules/@playwright/test/cli.js';
const result = spawnSync(process.execPath, [
    playwrightCli,
    'test',
    'tests/Browser/playwright/tool-actions.spec.ts',
    '--project=chromium-desktop',
    '--workers=1',
    '--retries=0',
    '--reporter=line',
], {
    cwd: process.cwd(),
    env: {
        ...process.env,
        E2E_TOOL_SLUG: rawSlug,
    },
    stdio: 'inherit',
});

if (result.error) {
    console.error(`\n[E2E TOOL] Não foi possível iniciar o Playwright: ${result.error.message}`);
    process.exit(1);
}

process.exit(result.status ?? 1);
