<?php

namespace Tests\Unit\Blog;

use App\Blog\Models\BlogPost;
use App\Blog\Seo\BlogSeoAnalyzer;
use Tests\TestCase;

final class BlogSeoAnalyzerTest extends TestCase
{
    public function test_it_reports_missing_essential_seo_information(): void
    {
        $issues = (new BlogSeoAnalyzer())->analyze(new BlogPost([
            'title' => '',
            'excerpt' => '',
            'content' => '<p>Conteúdo curto.</p>',
            'should_index' => true,
        ]));

        $messages = collect($issues)->pluck('message')->implode(' ');

        $this->assertStringContainsString('título', $messages);
        $this->assertStringContainsString('meta description', $messages);
        $this->assertStringContainsString('palavra-chave', $messages);
    }
}
