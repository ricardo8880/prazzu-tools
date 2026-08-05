import { execFileSync } from 'node:child_process';

export default async function globalSetup(): Promise<void> {
    execFileSync('php', ['scripts/e2e-tool-catalog.php', 'export'], {
        cwd: process.cwd(),
        stdio: 'inherit',
    });
}
