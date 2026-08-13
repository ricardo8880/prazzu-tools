import { mkdirSync, writeFileSync } from 'node:fs';
import path from 'node:path';

const reportDirectory = path.resolve('storage/app/e2e/artifacts');
mkdirSync(reportDirectory, { recursive: true });
writeFileSync(
    path.join(reportDirectory, 'execution.txt'),
    `Execução E2E iniciada em ${new Date().toISOString()}\n`,
    'utf8',
);

console.log('[E2E] Relatório textual inicializado.');
