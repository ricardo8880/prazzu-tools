<?php

namespace App\Console\Commands;

use App\Core\Quality\Services\ArchitectureInspector;
use Illuminate\Console\Command;

final class CheckToolArchitectureCommand extends Command
{
    protected $signature = 'tools:check-architecture';

    protected $description = 'Valida contratos e limites arquiteturais de todos os módulos de ferramentas';

    public function handle(ArchitectureInspector $inspector): int
    {
        $violations = $inspector->inspect();

        if ($violations === []) {
            $this->info('Arquitetura das ferramentas validada sem violações.');

            return self::SUCCESS;
        }

        $this->error(count($violations).' violação(ões) arquitetural(is) encontrada(s):');

        foreach ($violations as $violation) {
            $this->line(' - '.$violation->format());
        }

        return self::FAILURE;
    }
}
