<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        if (! Schema::hasTable('blog_categories') || ! Schema::hasColumn('blog_categories', 'vertical_slug')) {
            return;
        }

        $now = now();
        $categoryId = DB::table('blog_categories')->where('slug', 'gestao-de-pessoas')->value('id');

        if ($categoryId === null) {
            $categoryId = DB::table('blog_categories')->insertGetId([
                'name' => 'Gestão de Pessoas',
                'slug' => 'gestao-de-pessoas',
                'description' => 'Indicadores, processos e práticas de Recursos Humanos.',
                'vertical_slug' => 'rh',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (! DB::table('blog_posts')->where('slug', 'como-calcular-turnover')->exists()) {
            $postId = DB::table('blog_posts')->insertGetId([
                'title' => 'Como calcular turnover em Recursos Humanos',
                'slug' => 'como-calcular-turnover',
                'excerpt' => 'Entenda uma fórmula operacional de turnover e os cuidados ao comparar períodos.',
                'content' => '<p>Turnover é um indicador de rotatividade. Uma forma operacional de cálculo usa a média entre admissões e desligamentos, dividida pelo quadro médio do mesmo período.</p><p>Antes de comparar equipes ou meses, mantenha o mesmo critério de quadro médio e registre a metodologia usada pela organização.</p>',
                'category_id' => $categoryId,
                'category' => 'Gestão de Pessoas',
                'vertical_slug' => 'rh',
                'status' => 'published',
                'is_featured' => true,
                'published_at' => $now,
                'content_updated_at' => $now,
                'primary_keyword' => 'turnover rh',
                'related_keywords' => json_encode(['rotatividade', 'gestão de pessoas', 'indicadores de RH'], JSON_THROW_ON_ERROR),
                'meta_title' => 'Como calcular turnover em RH | Prazzu Tools',
                'meta_description' => 'Entenda uma fórmula operacional de turnover e como manter comparações consistentes em Recursos Humanos.',
                'should_index' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('blog_post_tool')->insert([
                'blog_post_id' => $postId,
                'tool_slug' => 'calculadora-turnover',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (! DB::table('blog_posts')->where('slug', 'indicadores-de-rh-com-criterios-consistentes')->exists()) {
            DB::table('blog_posts')->insert([
                'title' => 'Indicadores de RH: como manter critérios consistentes',
                'slug' => 'indicadores-de-rh-com-criterios-consistentes',
                'excerpt' => 'Veja por que período, população e fórmula precisam ser consistentes ao acompanhar indicadores de RH.',
                'content' => '<p>Indicadores de Recursos Humanos ganham valor quando o critério de cálculo permanece estável ao longo do tempo.</p><p>Defina o período, a população observada e a fórmula antes de comparar equipes, unidades ou meses. Registre mudanças metodológicas para não interpretar uma alteração de critério como mudança real de desempenho.</p>',
                'category_id' => $categoryId,
                'category' => 'Gestão de Pessoas',
                'vertical_slug' => 'rh',
                'status' => 'published',
                'is_featured' => false,
                'published_at' => $now,
                'content_updated_at' => $now,
                'primary_keyword' => 'indicadores de rh',
                'related_keywords' => json_encode(['gestão de pessoas', 'métricas de RH', 'turnover'], JSON_THROW_ON_ERROR),
                'meta_title' => 'Indicadores de RH com critérios consistentes | Prazzu Tools',
                'meta_description' => 'Entenda como manter período, população e fórmula consistentes ao acompanhar indicadores de Recursos Humanos.',
                'should_index' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        if (app()->environment('testing')) {
            return;
        }

        if (! Schema::hasTable('blog_posts')) {
            return;
        }

        $postId = DB::table('blog_posts')->where('slug', 'como-calcular-turnover')->value('id');
        if ($postId !== null && Schema::hasTable('blog_post_tool')) {
            DB::table('blog_post_tool')->where('blog_post_id', $postId)->delete();
        }
        DB::table('blog_posts')->whereIn('slug', ['como-calcular-turnover', 'indicadores-de-rh-com-criterios-consistentes'])->delete();
        DB::table('blog_categories')->where('slug', 'gestao-de-pessoas')->where('vertical_slug', 'rh')->delete();
    }
};
