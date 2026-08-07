import { mkdirSync, readFileSync, writeFileSync } from 'node:fs';
import path from 'node:path';
import { execFileSync } from 'node:child_process';

function loadE2EEnvironment(root: string): NodeJS.ProcessEnv {
    const envPath = path.resolve(root, '.env.e2e');
    const environment: NodeJS.ProcessEnv = { ...process.env };

    for (const rawLine of readFileSync(envPath, 'utf8').split(/\r?\n/)) {
        const line = rawLine.trim();

        if (line === '' || line.startsWith('#')) {
            continue;
        }

        const separator = line.indexOf('=');
        if (separator === -1) {
            continue;
        }

        const key = line.slice(0, separator).trim();
        let value = line.slice(separator + 1).trim();

        if (
            value.length >= 2
            && ((value.startsWith('"') && value.endsWith('"'))
                || (value.startsWith("'") && value.endsWith("'")))
        ) {
            value = value.slice(1, -1);
        }

        environment[key] = value;
    }

    return environment;
}

export default async function globalSetup(): Promise<void> {
    const root = process.cwd();
    const environment = loadE2EEnvironment(root);
    const logPath = path.resolve(root, environment.E2E_LOG_PATH ?? 'storage/app/e2e/logs/e2e.jsonl');

    mkdirSync(path.dirname(logPath), { recursive: true });
    writeFileSync(logPath, '');

    execFileSync('php', ['scripts/e2e-tool-scenarios.php', 'export'], {
        cwd: root,
        env: environment,
        stdio: 'inherit',
    });
}
