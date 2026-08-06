import { mkdirSync, writeFileSync } from 'node:fs';
import path from 'node:path';
import { execFileSync } from 'node:child_process';

export default async function globalSetup(): Promise<void> {
    const logPath = path.resolve(process.cwd(), process.env.E2E_LOG_PATH ?? 'storage/app/e2e/logs/e2e.jsonl');
    mkdirSync(path.dirname(logPath), { recursive: true });
    writeFileSync(logPath, '');

    for (const script of ['e2e-tool-catalog.php', 'e2e-tool-scenarios.php', 'e2e-access.php']) {
        execFileSync('php', [`scripts/${script}`, 'export'], {
            cwd: process.cwd(),
            stdio: 'inherit',
        });
    }
}
