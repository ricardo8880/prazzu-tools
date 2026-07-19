import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { existsSync, readdirSync } from 'node:fs';
import { join, sep } from 'node:path';

function toolAssetEntries(root = 'app/Tools') {
    if (!existsSync(root)) {
        return [];
    }

    const entries = [];

    for (const module of readdirSync(root, { withFileTypes: true })) {
        if (!module.isDirectory()) {
            continue;
        }

        for (const asset of ['Resources/js/index.js']) {
            const path = join(root, module.name, asset);

            if (existsSync(path)) {
                entries.push(path.split(sep).join('/'));
            }
        }
    }

    return entries;
}

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/admin/blog-editor.js',
                ...toolAssetEntries(),
            ],
            refresh: [
                'resources/views/**',
                'resources/lang/**',
                'routes/**',
                'app/Livewire/**',
                'app/View/Components/**',
                'app/Tools/**/Resources/views/**',
                'app/Tools/**/Routes/**',
            ],
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
