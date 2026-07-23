<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Core\Analytics\Application\Services\HistoricalAnalyticsDeduplicator;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Throwable;

final class RepairAnalyticsHistoryCommand extends Command
{
    protected $signature = 'analytics:repair-history
        {--apply : Remove as duplicatas confirmadas; sem esta opção apenas simula}
        {--from= : Data inicial inclusiva em formato YYYY-MM-DD}
        {--to= : Data final inclusiva em formato YYYY-MM-DD}
        {--chunk=1000 : Quantidade de eventos processados por lote}';

    protected $description = 'Localiza e, opcionalmente, remove duplicatas históricas conservadoras do analytics.';

    public function handle(HistoricalAnalyticsDeduplicator $deduplicator): int
    {
        try {
            $from = $this->dateOption('from', false);
            $to = $this->dateOption('to', true);
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::INVALID;
        }

        $apply = (bool) $this->option('apply');
        $result = $deduplicator->run(
            apply: $apply,
            from: $from,
            to: $to,
            chunkSize: (int) $this->option('chunk'),
        );

        $this->components->info(sprintf(
            '%s: %d evento(s) analisado(s), %d duplicata(s) confirmada(s), %d removida(s).',
            $apply ? 'Reparação concluída' : 'Simulação concluída',
            $result['scanned'],
            $result['duplicates'],
            $result['deleted'],
        ));

        if (! $apply && $result['duplicates'] > 0) {
            $this->components->warn('Nenhum dado foi alterado. Execute novamente com --apply após revisar o resultado.');
        }

        return self::SUCCESS;
    }

    private function dateOption(string $name, bool $endOfDay): ?CarbonImmutable
    {
        $value = $this->option($name);
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        $date = CarbonImmutable::createFromFormat('!Y-m-d', trim($value));
        if ($date === false) {
            throw new \InvalidArgumentException(sprintf('A opção --%s deve usar o formato YYYY-MM-DD.', $name));
        }

        return $endOfDay ? $date->endOfDay() : $date->startOfDay();
    }
}
