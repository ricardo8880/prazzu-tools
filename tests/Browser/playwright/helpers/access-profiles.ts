import { readFileSync } from 'node:fs';
import { resolve } from 'node:path';

export type AccessProfileName = 'free' | 'plus' | 'administrator';

type AccessProfile = {
    name: string;
    email: string;
    password: string;
    role: 'user' | 'administrator';
    subscription_plan: 'free' | 'plus';
};

type AccessManifest = {
    schema_version: '1.0.0';
    profiles: Record<AccessProfileName, AccessProfile>;
    protected_paths: Record<'account' | 'administrator' | 'history', string>;
};

export function loadAccessManifest(): AccessManifest {
    const filename = resolve(process.cwd(), 'storage/app/e2e/runtime/access-profiles.json');
    const manifest = JSON.parse(readFileSync(filename, 'utf8')) as AccessManifest;

    if (manifest.schema_version !== '1.0.0') {
        throw new Error(`Versão desconhecida do manifesto de acesso: ${manifest.schema_version}`);
    }

    return manifest;
}

export function authStatePath(profile: AccessProfileName): string {
    return resolve(process.cwd(), `storage/app/e2e/runtime/auth/${profile}.json`);
}
