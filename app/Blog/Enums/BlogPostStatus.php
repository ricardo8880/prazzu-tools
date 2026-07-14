<?php

namespace App\Blog\Enums;

enum BlogPostStatus: string
{
    case Draft = 'draft';
    case Scheduled = 'scheduled';
    case Published = 'published';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Rascunho',
            self::Scheduled => 'Agendado',
            self::Published => 'Publicado',
        };
    }

    public function canBePublicAt(?\DateTimeInterface $publishedAt, \DateTimeInterface $now): bool
    {
        return $this === self::Published
            && $publishedAt !== null
            && $publishedAt <= $now;
    }
}
