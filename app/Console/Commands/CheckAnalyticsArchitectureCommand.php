<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Core\Analytics\Domain\Services\AnalyticsEventNameResolver;
use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

final class CheckAnalyticsArchitectureCommand extends Command
{
    protected $signature = 'analytics:check';

    protected $description = 'Valida o catálogo, os funis e o uso dos eventos do Core Analytics.';

    public function __construct(private readonly AnalyticsEventNameResolver $eventNames)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $violations = [
            ...$this->configurationViolations(),
            ...$this->legacyLiteralViolations(),
        ];

        if ($violations === []) {
            $this->info('Analytics está de acordo com o catálogo oficial de eventos.');

            return self::SUCCESS;
        }

        foreach ($violations as $violation) {
            $this->error($violation);
        }

        $this->newLine();
        $this->error(count($violations).' violação(ões) de Analytics encontrada(s).');

        return self::FAILURE;
    }

    /** @return list<string> */
    private function configurationViolations(): array
    {
        $violations = [];

        foreach ($this->configuredEvents() as $location => $eventName) {
            if (! $this->eventNames->isKnown($eventName)) {
                $violations[] = "Evento desconhecido em {$location}: {$eventName}";

                continue;
            }

            if (! $this->eventNames->isCanonical($eventName)) {
                $canonical = $this->eventNames->canonical($eventName);
                $violations[] = "Alias legado em {$location}: {$eventName}; use {$canonical}";
            }
        }

        return $violations;
    }

    /** @return array<string, string> */
    private function configuredEvents(): array
    {
        $events = [];

        $this->collectConfiguredEvents(config('analytics.dashboard', []), 'analytics.dashboard', $events);
        $this->collectConfiguredEvents(config('analytics.acquisition.funnel_steps', []), 'analytics.acquisition.funnel_steps', $events);
        $this->collectConfiguredEvents(config('analytics.funnels.standard', []), 'analytics.funnels.standard', $events);

        return $events;
    }

    /** @param array<string, mixed> $values
     *  @param array<string, string> $events
     */
    private function collectConfiguredEvents(array $values, string $path, array &$events): void
    {
        foreach ($values as $key => $value) {
            $currentPath = $path.'.'.$key;

            if ($key === 'events' || str_ends_with((string) $key, '_events')) {
                foreach ((array) $value as $index => $eventName) {
                    if (is_string($eventName)) {
                        $events[$currentPath.'.'.$index] = $eventName;
                    }
                }

                continue;
            }

            if (is_array($value)) {
                $this->collectConfiguredEvents($value, $currentPath, $events);
            }
        }
    }

    /** @return list<string> */
    private function legacyLiteralViolations(): array
    {
        $violations = [];
        $aliases = array_keys($this->eventNames->legacyAliases());

        foreach ($this->productionFiles() as $file) {
            $contents = file_get_contents($file->getPathname()) ?: '';
            preg_match_all('/([\'\"])([^\'\"\\r\\n]+)\\1/', $contents, $matches);

            foreach (array_unique($matches[2] ?? []) as $literal) {
                if (in_array($literal, $aliases, true)) {
                    $relativePath = str_replace(base_path().DIRECTORY_SEPARATOR, '', $file->getPathname());
                    $violations[] = "Alias legado publicado em {$relativePath}: {$literal}";
                }
            }
        }

        return array_values(array_unique($violations));
    }

    /** @return iterable<SplFileInfo> */
    private function productionFiles(): iterable
    {
        $roots = [app_path(), config_path(), resource_path('js'), resource_path('views'), base_path('routes')];
        $excluded = array_map($this->normalizePath(...), [
            app_path('Core/Analytics/Domain/Services/AnalyticsEventNameResolver.php'),
        ]);

        foreach ($roots as $root) {
            if (! is_dir($root)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));

            foreach ($iterator as $file) {
                if (! $file->isFile() || in_array($this->normalizePath($file->getPathname()), $excluded, true)) {
                    continue;
                }

                if (! in_array($file->getExtension(), ['php', 'js'], true)) {
                    continue;
                }

                yield $file;
            }
        }
    }

    private function normalizePath(string $path): string
    {
        return str_replace('\\', '/', $path);
    }
}
