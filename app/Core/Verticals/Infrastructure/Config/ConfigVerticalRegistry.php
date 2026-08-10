<?php

namespace App\Core\Verticals\Infrastructure\Config;

use App\Core\Verticals\Contracts\VerticalRegistry;
use App\Core\Verticals\Domain\Data\Vertical;
use InvalidArgumentException;

final class ConfigVerticalRegistry implements VerticalRegistry
{
    /** @return list<Vertical> */
    public function all(): array
    {
        $registered = config('verticals.registered', []);

        if (! is_array($registered)) {
            throw new InvalidArgumentException('A configuração [verticals.registered] deve ser um array.');
        }

        $verticals = [];

        foreach ($registered as $slug => $configuration) {
            if (! is_string($slug) || ! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
                throw new InvalidArgumentException('Toda vertical deve possuir um slug válido em kebab-case.');
            }

            if (! is_array($configuration)) {
                throw new InvalidArgumentException("A configuração da vertical [{$slug}] deve ser um array.");
            }

            $name = trim((string) ($configuration['name'] ?? ''));
            $publicSlug = trim((string) ($configuration['public_slug'] ?? $slug));

            if ($name === '') {
                throw new InvalidArgumentException("A vertical [{$slug}] deve possuir um nome.");
            }

            if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $publicSlug)) {
                throw new InvalidArgumentException("A vertical [{$slug}] deve possuir um public_slug válido em kebab-case.");
            }

            $verticals[] = new Vertical($slug, $name, $publicSlug);
        }

        return $verticals;
    }

    public function find(string $slug): ?Vertical
    {
        $normalizedSlug = trim($slug);

        if ($normalizedSlug === '') {
            return null;
        }

        foreach ($this->all() as $vertical) {
            if ($vertical->slug === $normalizedSlug) {
                return $vertical;
            }
        }

        return null;
    }

    public function findByPublicSlug(string $publicSlug): ?Vertical
    {
        $normalizedSlug = trim($publicSlug);

        if ($normalizedSlug === '') {
            return null;
        }

        foreach ($this->all() as $vertical) {
            if ($vertical->publicSlug === $normalizedSlug) {
                return $vertical;
            }
        }

        return null;
    }

    public function default(): ?Vertical
    {
        $slug = config('verticals.default');

        return is_string($slug) ? $this->find($slug) : null;
    }
}
