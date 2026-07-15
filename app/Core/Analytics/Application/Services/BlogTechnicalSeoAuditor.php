<?php

namespace App\Core\Analytics\Application\Services;

use App\Blog\Models\BlogPost;
use Illuminate\Support\Str;

final class BlogTechnicalSeoAuditor
{
    /** @return array<string, mixed> */
    public function audit(BlogPost $post): array
    {
        $content = (string) $post->content;
        $plainText = trim(preg_replace('/\s+/u', ' ', strip_tags($content)) ?? '');
        $title = trim((string) ($post->meta_title ?: $post->title));
        $description = trim((string) ($post->meta_description ?: $post->excerpt));
        $canonical = trim((string) ($post->canonical_url ?: route('blog.show', $post->slug)));
        $images = $this->images($content, $post);
        $links = $this->links($content);
        $schemaTypes = $this->schemaTypes($content);
        $wordCount = $this->wordCount($plainText);

        $checks = [
            $this->check('title', 'Título SEO', $title !== '', $this->between(mb_strlen($title), 30, 60), "{$this->lengthLabel($title)} caracteres"),
            $this->check('slug', 'Slug', $post->slug !== '', ! Str::contains($post->slug, ['_', ' ']), '/blog/'.$post->slug),
            $this->check('description', 'Meta description', $description !== '', $this->between(mb_strlen($description), 120, 160), "{$this->lengthLabel($description)} caracteres"),
            $this->check('canonical', 'Canonical', $canonical !== '', Str::startsWith($canonical, ['http://', 'https://']), $canonical),
            $this->check('schema', 'Dados estruturados', $schemaTypes !== [], $schemaTypes !== [], $schemaTypes === [] ? 'Nenhum schema detectado' : implode(', ', $schemaTypes)),
            $this->check('indexation', 'Indexação', (bool) $post->should_index, (bool) $post->should_index, $post->should_index ? 'index,follow' : 'noindex,nofollow'),
            $this->check('words', 'Quantidade de palavras', $wordCount > 0, $wordCount >= 600, number_format($wordCount, 0, ',', '.').' palavras'),
            $this->check('images', 'Imagens e ALT', $images['total'] > 0, $images['without_alt'] === 0, "{$images['total']} imagens · {$images['without_alt']} sem ALT"),
            $this->check('internal_links', 'Links internos', $links['internal'] > 0 || $post->relatedToolSlugs()->isNotEmpty(), $links['internal'] >= 2 || $post->relatedToolSlugs()->isNotEmpty(), "{$links['internal']} no conteúdo"),
            $this->check('external_links', 'Links externos', true, true, "{$links['external']} no conteúdo"),
            $this->check('updated', 'Atualização do conteúdo', true, $this->isRecentlyUpdated($post), $this->lastUpdateLabel($post)),
        ];

        $passed = collect($checks)->where('status', 'success')->count();
        $warnings = collect($checks)->where('status', 'warning')->count();
        $errors = collect($checks)->where('status', 'danger')->count();
        $score = (int) round(($passed / max(1, count($checks))) * 100);

        return [
            'score' => $score,
            'status' => $errors > 0 ? 'danger' : ($warnings > 0 ? 'warning' : 'success'),
            'checks' => $checks,
            'title' => $title,
            'title_length' => mb_strlen($title),
            'description' => $description,
            'description_length' => mb_strlen($description),
            'canonical' => $canonical,
            'robots' => $post->should_index ? 'index,follow' : 'noindex,nofollow',
            'schema_types' => $schemaTypes,
            'word_count' => $wordCount,
            'reading_time_minutes' => $post->readingTimeMinutes(),
            'images' => $images,
            'links' => $links,
            'created_at' => $post->created_at,
            'updated_at' => $post->content_updated_at ?: $post->updated_at,
            'passed' => $passed,
            'warnings' => $warnings,
            'errors' => $errors,
        ];
    }

    /** @return array{total:int,with_alt:int,without_alt:int} */
    private function images(string $content, BlogPost $post): array
    {
        preg_match_all('/<img\b[^>]*>/iu', $content, $matches);
        $total = count($matches[0]);
        $withAlt = 0;

        foreach ($matches[0] as $tag) {
            if (preg_match('/\balt\s*=\s*(["\'])(.*?)\1/iu', $tag, $alt) && trim($alt[2]) !== '') {
                $withAlt++;
            }
        }

        if ($post->cover_image_path) {
            $total++;
            if (trim((string) $post->cover_image_alt) !== '') {
                $withAlt++;
            }
        }

        return ['total' => $total, 'with_alt' => $withAlt, 'without_alt' => max(0, $total - $withAlt)];
    }

    /** @return array{internal:int,external:int,total:int} */
    private function links(string $content): array
    {
        preg_match_all('/<a\b[^>]*href\s*=\s*(["\'])(.*?)\1/iu', $content, $matches);
        $internal = 0;
        $external = 0;
        $host = parse_url((string) config('app.url'), PHP_URL_HOST);

        foreach ($matches[2] ?? [] as $href) {
            $href = trim(html_entity_decode($href, ENT_QUOTES));
            if ($href === '' || Str::startsWith($href, ['#', 'mailto:', 'tel:', 'javascript:'])) {
                continue;
            }

            $linkHost = parse_url($href, PHP_URL_HOST);
            if ($linkHost === null || $linkHost === false || $linkHost === '' || ($host && $linkHost === $host)) {
                $internal++;
            } else {
                $external++;
            }
        }

        return ['internal' => $internal, 'external' => $external, 'total' => $internal + $external];
    }

    /** @return array<int, string> */
    private function schemaTypes(string $content): array
    {
        preg_match_all('/["\']@type["\']\s*:\s*["\']([^"\']+)["\']/iu', $content, $matches);

        return collect($matches[1] ?? [])->map('trim')->filter()->unique()->values()->all();
    }

    private function wordCount(string $plainText): int
    {
        if ($plainText === '') {
            return 0;
        }

        preg_match_all('/[\p{L}\p{N}]+(?:[\'’-][\p{L}\p{N}]+)*/u', $plainText, $matches);

        return count($matches[0]);
    }

    /** @return array{key:string,label:string,status:string,detail:string} */
    private function check(string $key, string $label, bool $exists, bool $optimal, string $detail): array
    {
        return [
            'key' => $key,
            'label' => $label,
            'status' => ! $exists ? 'danger' : ($optimal ? 'success' : 'warning'),
            'detail' => $detail,
        ];
    }

    private function between(int $value, int $minimum, int $maximum): bool
    {
        return $value >= $minimum && $value <= $maximum;
    }

    private function lengthLabel(string $value): int
    {
        return mb_strlen($value);
    }

    private function isRecentlyUpdated(BlogPost $post): bool
    {
        $date = $post->content_updated_at ?: $post->updated_at ?: $post->created_at;

        return $date !== null && $date->greaterThanOrEqualTo(now()->subMonths(12));
    }

    private function lastUpdateLabel(BlogPost $post): string
    {
        $date = $post->content_updated_at ?: $post->updated_at ?: $post->created_at;

        return $date ? $date->format('d/m/Y') : 'Sem data';
    }
}
