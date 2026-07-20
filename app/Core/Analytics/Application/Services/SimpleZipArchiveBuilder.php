<?php

declare(strict_types=1);

namespace App\Core\Analytics\Application\Services;

final class SimpleZipArchiveBuilder
{
    /** @param array<string,string> $files */
    public function build(array $files): string
    {
        $body = '';
        $directory = '';
        $offset = 0;
        $count = 0;

        foreach ($files as $name => $content) {
            $name = $this->safeName($name);
            $crc = crc32($content);
            $size = strlen($content);
            $time = $this->dosTime();
            $date = $this->dosDate();
            $local = pack('VvvvvvVVVvv', 0x04034B50, 20, 0x0800, 0, $time, $date, $crc, $size, $size, strlen($name), 0)
                .$name.$content;
            $body .= $local;
            $directory .= pack('VvvvvvvVVVvvvvvVV', 0x02014B50, 20, 20, 0x0800, 0, $time, $date, $crc, $size, $size, strlen($name), 0, 0, 0, 0, 0, $offset).$name;
            $offset += strlen($local);
            $count++;
        }

        return $body.$directory.pack('VvvvvVVv', 0x06054B50, 0, 0, $count, $count, strlen($directory), strlen($body), 0);
    }

    private function safeName(string $name): string
    {
        $name = str_replace('\\', '/', trim($name));
        $parts = array_values(array_filter(explode('/', $name), fn (string $part): bool => $part !== '' && $part !== '.' && $part !== '..'));

        if ($parts === []) {
            throw new \InvalidArgumentException('Nome de arquivo inválido para o pacote ZIP.');
        }

        return implode('/', $parts);
    }

    private function dosTime(): int
    {
        return ((int) date('H') << 11) | ((int) date('i') << 5) | intdiv((int) date('s'), 2);
    }

    private function dosDate(): int
    {
        return (((int) date('Y') - 1980) << 9) | ((int) date('m') << 5) | (int) date('d');
    }
}
