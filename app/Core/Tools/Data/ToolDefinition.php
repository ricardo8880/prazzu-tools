<?php

namespace App\Core\Tools\Data;

use InvalidArgumentException;

final readonly class ToolDefinition
{
    /**
     * @param array<int, string> $keywords
     */
    public function __construct(
        public string $slug,
        public string $name,
        public string $description,
        public string $category,
        public string $icon,
        public string $routeName,
        public bool $isFree = true,
        public bool $isPremium = false,
        public bool $isFeatured = false,
        public bool $isActive = true,
        public array $keywords = [],
    ) {
        if ($this->slug === '' || $this->name === '' || $this->routeName === '') {
            throw new InvalidArgumentException('Slug, nome e rota da ferramenta são obrigatórios.');
        }

        if ($this->isFree && $this->isPremium) {
            throw new InvalidArgumentException('Uma ferramenta não pode ser gratuita e premium ao mesmo tempo.');
        }
    }
}
