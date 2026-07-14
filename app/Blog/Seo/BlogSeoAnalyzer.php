<?php

namespace App\Blog\Seo;

use App\Blog\Models\BlogPost;
use Illuminate\Support\Str;

final class BlogSeoAnalyzer
{
    /** @return array<int, array{level: string, message: string}> */
    public function analyze(BlogPost $post): array
    {
        $issues = [];
        $title = trim((string) ($post->meta_title ?: $post->title));
        $description = trim((string) ($post->meta_description ?: $post->excerpt));
        $keyword = Str::lower(trim((string) $post->primary_keyword));
        $plainContent = trim(preg_replace('/\s+/', ' ', strip_tags((string) $post->content)) ?? '');
        $wordCount = str_word_count($plainContent);

        if ($title === '') {
            $issues[] = $this->danger('Defina um título para a postagem.');
        } elseif (mb_strlen($title) < 30) {
            $issues[] = $this->warning('O título SEO está curto. Procure usar entre 30 e 60 caracteres.');
        } elseif (mb_strlen($title) > 60) {
            $issues[] = $this->warning('O título SEO pode ser cortado nos resultados de busca. Procure usar até 60 caracteres.');
        }

        if ($description === '') {
            $issues[] = $this->danger('Adicione uma meta description.');
        } elseif (mb_strlen($description) < 120) {
            $issues[] = $this->warning('A meta description está curta. Procure usar entre 120 e 160 caracteres.');
        } elseif (mb_strlen($description) > 160) {
            $issues[] = $this->warning('A meta description pode ser cortada. Procure usar até 160 caracteres.');
        }

        if ($keyword === '') {
            $issues[] = $this->warning('Informe a palavra-chave principal para orientar a revisão editorial.');
        } else {
            if (! Str::contains(Str::lower($post->title), $keyword)) {
                $issues[] = $this->warning('A palavra-chave principal não aparece no título da postagem.');
            }

            if (! Str::contains(Str::lower($post->excerpt), $keyword)) {
                $issues[] = $this->warning('A palavra-chave principal não aparece no resumo.');
            }

            if (! Str::contains(Str::lower($plainContent), $keyword)) {
                $issues[] = $this->warning('A palavra-chave principal não aparece no conteúdo.');
            }
        }

        if ($wordCount < 600) {
            $issues[] = $this->warning("O conteúdo possui aproximadamente {$wordCount} palavras. Avalie se responde à intenção de busca com profundidade.");
        }

        if (! preg_match('/<h2\b/i', (string) $post->content)) {
            $issues[] = $this->warning('Use ao menos um subtítulo H2 para organizar a leitura.');
        }

        if ($post->cover_image_path && ! trim((string) $post->cover_image_alt)) {
            $issues[] = $this->warning('A imagem de capa está sem texto alternativo.');
        }

        if (! preg_match('/<a\b[^>]*href=/i', (string) $post->content) && $post->relatedToolSlugs()->isEmpty()) {
            $issues[] = $this->warning('Inclua ao menos um link interno ou uma ferramenta relacionada.');
        }

        if (! $post->should_index) {
            $issues[] = $this->info('Esta postagem está configurada como noindex e não deverá aparecer nos buscadores.');
        }

        if ($issues === []) {
            $issues[] = ['level' => 'success', 'message' => 'Os principais elementos técnicos de SEO estão preenchidos.'];
        }

        return $issues;
    }

    /** @return array{level: string, message: string} */
    private function danger(string $message): array
    {
        return ['level' => 'danger', 'message' => $message];
    }

    /** @return array{level: string, message: string} */
    private function warning(string $message): array
    {
        return ['level' => 'warning', 'message' => $message];
    }

    /** @return array{level: string, message: string} */
    private function info(string $message): array
    {
        return ['level' => 'info', 'message' => $message];
    }
}
