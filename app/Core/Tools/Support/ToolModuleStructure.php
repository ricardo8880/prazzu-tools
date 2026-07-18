<?php

declare(strict_types=1);

namespace App\Core\Tools\Support;

final class ToolModuleStructure
{
    /** @return list<string> */
    public static function requiredDirectories(): array
    {
        return [
            'Application',
            'Domain',
            'Infrastructure',
            'Presentation',
            'Resources',
            'Routes',
            'Tests',
            'Tests/Unit',
            'Tests/Feature',
        ];
    }

    /** @return list<string> */
    public static function requiredFiles(): array
    {
        return [
            'README.md',
            'Tool.php',
        ];
    }

    /** @return list<string> */
    public static function missingPaths(string $moduleRoot): array
    {
        $missing = [];

        foreach (self::requiredDirectories() as $directory) {
            if (! self::existsWithExactCase($moduleRoot, $directory, true)) {
                $missing[] = $directory.'/';
            }
        }

        foreach (self::requiredFiles() as $file) {
            if (! self::existsWithExactCase($moduleRoot, $file, false)) {
                $missing[] = $file;
            }
        }

        return $missing;
    }

    private static function existsWithExactCase(string $root, string $relativePath, bool $directory): bool
    {
        $current = rtrim($root, '/\\');

        foreach (explode('/', $relativePath) as $segment) {
            $entries = is_dir($current) ? scandir($current) : false;

            if ($entries === false || ! in_array($segment, $entries, true)) {
                return false;
            }

            $current .= DIRECTORY_SEPARATOR.$segment;
        }

        return $directory ? is_dir($current) : is_file($current);
    }
}
