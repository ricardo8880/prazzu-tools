<?php

declare(strict_types=1);

use Illuminate\Contracts\Console\Kernel;

require dirname(__DIR__).'/vendor/autoload.php';

$app = require dirname(__DIR__).'/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$command = $argv[1] ?? 'help';
$target = dirname(__DIR__).'/storage/app/e2e/runtime/access-profiles.json';

if ($command === 'export') {
    if (! app()->environment('e2e') || ! config('e2e_environment.enabled')) {
        fwrite(STDERR, "[E2E] Perfis de acesso só podem ser exportados no ambiente e2e.\n");
        exit(1);
    }

    $profiles = config('e2e_environment.profiles', []);
    $payload = [
        'schema_version' => '1.0.0',
        'profiles' => $profiles,
        'protected_paths' => [
            'account' => '/minha-conta',
            'administrator' => '/admin',
            'history' => '/ferramentas/calculo-ferias/historico',
        ],
    ];

    if (! is_dir(dirname($target))) {
        mkdir(dirname($target), 0775, true);
    }

    file_put_contents($target, json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR).PHP_EOL);
    fwrite(STDOUT, "[E2E] Perfis e fluxos transversais exportados.\n");
    exit(0);
}

if ($command === 'check') {
    if (! is_file($target)) {
        fwrite(STDERR, "[E2E] Manifesto de acesso ausente. Execute export.\n");
        exit(1);
    }

    $payload = json_decode((string) file_get_contents($target), true, flags: JSON_THROW_ON_ERROR);
    foreach (['free', 'plus', 'administrator'] as $profile) {
        if (empty($payload['profiles'][$profile]['email']) || empty($payload['profiles'][$profile]['password'])) {
            fwrite(STDERR, "[E2E] Perfil {$profile} incompleto.\n");
            exit(1);
        }
    }

    fwrite(STDOUT, "[E2E] Manifesto de acesso validado.\n");
    exit(0);
}

fwrite(STDOUT, "Uso: php scripts/e2e-access.php export|check\n");
