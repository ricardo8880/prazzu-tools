<?php

namespace App\Console\Commands;

use App\Core\Tools\History\Models\ToolRun;
use Illuminate\Console\Command;

final class PurgeExpiredToolRunsCommand extends Command
{
    protected $signature = 'tools:purge-history {--chunk=500 : Quantidade removida por operação}';

    protected $description = 'Remove históricos de ferramentas cujo prazo de retenção expirou.';

    public function handle(): int
    {
        $chunk = filter_var($this->option('chunk'), FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 5000],
        ]);

        if ($chunk === false) {
            $this->error('A opção --chunk deve estar entre 1 e 5000.');

            return self::INVALID;
        }

        $removed = 0;

        do {
            $ids = ToolRun::query()
                ->where('expires_at', '<=', now())
                ->orderBy('expires_at')
                ->limit($chunk)
                ->pluck('id');

            $count = $ids->isEmpty()
                ? 0
                : ToolRun::query()->whereIn('id', $ids)->delete();

            $removed += $count;
        } while ($count === $chunk);

        $this->info("{$removed} histórico(s) expirado(s) removido(s).");

        return self::SUCCESS;
    }
}
