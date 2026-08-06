<?php

declare(strict_types=1);

/**
 * Executa as suítes E2E completa e exploratória mesmo quando uma delas falha.
 * O código final será diferente de zero se qualquer suíte encontrar falhas.
 */
function runCommand(string $command, string $label): int
{
    fwrite(STDOUT, PHP_EOL."[E2E] Iniciando {$label}...".PHP_EOL);

    passthru($command, $exitCode);

    $status = $exitCode === 0 ? 'concluída com sucesso' : "concluída com código {$exitCode}";
    fwrite(STDOUT, PHP_EOL."[E2E] {$label} {$status}.".PHP_EOL);

    return $exitCode;
}

$npm = PHP_OS_FAMILY === 'Windows' ? 'npm.cmd' : 'npm';

$completeExitCode = runCommand("{$npm} run e2e:test:complete", 'suíte determinística completa');
$exploratoryExitCode = runCommand("{$npm} run e2e:test:exploratory", 'suíte exploratória controlada');

$reportPath = 'txt_e2e_testes.txt';
fwrite(STDOUT, PHP_EOL."[E2E] Relatório consolidado: {$reportPath}".PHP_EOL);

if ($completeExitCode !== 0 || $exploratoryExitCode !== 0) {
    fwrite(
        STDERR,
        PHP_EOL.sprintf(
            '[E2E] Execução finalizada com falhas: completa=%d, exploratória=%d.%s',
            $completeExitCode,
            $exploratoryExitCode,
            PHP_EOL,
        ),
    );

    exit(1);
}

fwrite(STDOUT, PHP_EOL."[E2E] Todas as suítes foram concluídas com sucesso.".PHP_EOL);
