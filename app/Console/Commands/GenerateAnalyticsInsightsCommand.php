<?php

namespace App\Console\Commands;

use App\Core\Analytics\Application\Services\AnalyticsInsightGenerator;
use App\Core\Analytics\Domain\ValueObjects\AnalyticsPeriod;
use Illuminate\Console\Command;

final class GenerateAnalyticsInsightsCommand extends Command
{
    protected $signature = 'analytics:generate-insights {--days=7 : Quantidade de dias analisados}';

    protected $description = 'Analisa os eventos e atualiza insights, alertas e oportunidades do Analytics.';

    public function handle(AnalyticsInsightGenerator $generator): int
    {
        $days = max(1, min(366, (int) $this->option('days')));
        $count = $generator->generate(AnalyticsPeriod::lastDays($days));
        $this->info("{$count} insight(s) analisado(s).");

        return self::SUCCESS;
    }
}
