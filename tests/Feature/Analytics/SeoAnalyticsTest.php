<?php

namespace Tests\Feature\Analytics;

use App\Blog\Enums\BlogPostStatus;
use App\Blog\Models\BlogPost;
use App\Core\Analytics\Application\Services\BlogTechnicalSeoAuditor;
use App\Core\Analytics\Models\SeoMetricSnapshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SeoAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_audits_blog_technical_seo(): void
    {
        $post = $this->post();
        $audit = app(BlogTechnicalSeoAuditor::class)->audit($post);

        $this->assertSame('index,follow', $audit['robots']);
        $this->assertSame(1, $audit['images']['total']);
        $this->assertSame(1, $audit['links']['internal']);
        $this->assertContains('Article', $audit['schema_types']);
    }

    public function test_it_displays_and_records_seo_metrics(): void
    {
        $post = $this->post();
        SeoMetricSnapshot::query()->create([
            'blog_post_id' => $post->getKey(), 'metric_date' => now()->toDateString(),
            'source' => 'google_search_console', 'search_type' => 'web', 'device' => 'all',
            'clicks' => 20, 'impressions' => 200, 'average_position' => 4.5,
        ]);

        $this->get(route('admin.analytics.seo', ['period' => '30']))
            ->assertOk()->assertSee('SEO Analytics')->assertSee('Artigo SEO')->assertSee('10,00%');

        $this->get(route('admin.analytics.seo.posts.show', $post))
            ->assertOk()->assertSee('Auditoria técnica')->assertSee('Registrar métricas diárias');

        $this->post(route('admin.analytics.seo.posts.metrics.store', $post), [
            'metric_date' => now()->subDay()->toDateString(), 'source' => 'google_search_console',
            'search_type' => 'web', 'device' => 'mobile', 'country_code' => 'br',
            'clicks' => 5, 'impressions' => 100, 'average_position' => 8.2,
            'discover_clicks' => 1, 'discover_impressions' => 20,
            'news_clicks' => 0, 'news_impressions' => 0,
            'rich_result_clicks' => 2, 'rich_result_impressions' => 10,
        ])->assertRedirect();

        $this->assertDatabaseHas('analytics_seo_metric_snapshots', [
            'blog_post_id' => $post->getKey(), 'device' => 'mobile', 'country_code' => 'BR', 'clicks' => 5,
        ]);
    }

    private function post(): BlogPost
    {
        return BlogPost::query()->create([
            'title' => 'Artigo SEO completo para profissionais contábeis',
            'slug' => 'artigo-seo-completo',
            'excerpt' => str_repeat('Descrição completa para mecanismos de busca e profissionais contábeis. ', 3),
            'content' => '<script type="application/ld+json">{"@type":"Article"}</script><h2>Conteúdo</h2><p>'.str_repeat('palavra ', 650).'</p><img src="x.jpg" alt="Imagem"><a href="/ferramentas">Ferramenta</a>',
            'category' => 'SEO', 'status' => BlogPostStatus::Published->value,
            'published_at' => now(), 'meta_title' => 'Artigo SEO completo para profissionais contábeis',
            'meta_description' => str_repeat('Descrição completa para mecanismos de busca. ', 4),
            'should_index' => true,
        ]);
    }
}
