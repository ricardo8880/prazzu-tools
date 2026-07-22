<?php

namespace Tests\Feature\Admin\Acquisition;

use App\Core\Acquisition\Infrastructure\Persistence\AcquisitionContextRecord;
use App\Core\Acquisition\Infrastructure\Persistence\AcquisitionContextToolRecord;
use App\Core\Access\Enums\AccountRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AcquisitionContextAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create([
            'role' => AccountRole::Administrator,
        ]));
    }

    public function test_admin_can_open_context_index_and_create_pages(): void
    {
        $this->get(route('admin.acquisition.contexts.index'))->assertOk();
        $this->get(route('admin.acquisition.contexts.create'))->assertOk();
    }

    public function test_admin_can_create_context_with_ordered_content(): void
    {
        $tools = app(\App\Core\Tools\ToolCatalog::class)->all(false)->pluck('slug')->take(3)->values();

        $response = $this->post(route('admin.acquisition.contexts.store'), [
            'name' => 'Rescisão Instagram',
            'keyword' => 'rescisao-video-01',
            'campaign_identifier' => 'instagram-rescisao',
            'status' => 'active',
            'tools_section_title' => 'Ferramentas para rescisão',
            'hero_title_before' => 'Calcule sua',
            'hero_title_line' => 'rescisão',
            'hero_title_highlight' => 'com segurança',
            'cta_label' => 'Calcular agora',
            'primary_tool_slug' => $tools->first(),
            'featured_tools' => $tools->take(2)->all(),
            'recommended_tools' => $tools->skip(1)->take(2)->all(),
        ]);

        $context = AcquisitionContextRecord::query()->where('keyword', 'rescisao-video-01')->firstOrFail();

        $response->assertRedirect(route('admin.acquisition.contexts.edit', $context->getKey()));
        $this->assertSame('active', $context->status->value);
        $this->assertSame('Ferramentas para rescisão', $context->tools_section_title);
        $this->assertSame(
            $tools->take(2)->all(),
            AcquisitionContextToolRecord::query()
                ->where('acquisition_context_id', $context->getKey())
                ->where('placement', 'featured')
                ->orderBy('position')
                ->pluck('tool_slug')
                ->all(),
        );
    }

    public function test_admin_can_toggle_context_status(): void
    {
        $context = AcquisitionContextRecord::query()->create([
            'name' => 'Contexto',
            'keyword' => 'contexto-teste',
            'status' => 'inactive',
        ]);

        $this->patch(route('admin.acquisition.contexts.toggle', $context->getKey()))
            ->assertSessionHas('status');

        $this->assertSame('active', $context->fresh()->status->value);
    }

    public function test_regular_user_cannot_access_context_administration(): void
    {
        $this->actingAs(User::factory()->create([
            'role' => AccountRole::User,
        ]));

        $this->get(route('admin.acquisition.contexts.index'))->assertForbidden();
        $this->post(route('admin.acquisition.contexts.store'), [])->assertForbidden();
    }

    public function test_invalid_tool_and_article_references_are_rejected(): void
    {
        $this->from(route('admin.acquisition.contexts.create'))
            ->post(route('admin.acquisition.contexts.store'), [
                'name' => 'Contexto inválido',
                'keyword' => 'contexto-invalido',
                'status' => 'inactive',
                'primary_tool_slug' => 'ferramenta-inexistente',
                'featured_tools' => ['ferramenta-inexistente'],
                'articles' => ['artigo-inexistente'],
            ])
            ->assertRedirect(route('admin.acquisition.contexts.create'))
            ->assertSessionHasErrors([
                'primary_tool_slug',
                'featured_tools.0',
                'articles.0',
            ]);

        $this->assertDatabaseMissing('acquisition_contexts', [
            'keyword' => 'contexto-invalido',
        ]);
    }

    public function test_deleting_context_removes_ordered_relations(): void
    {
        $context = AcquisitionContextRecord::query()->create([
            'name' => 'Contexto removível',
            'keyword' => 'contexto-removivel',
            'status' => 'inactive',
        ]);
        $context->tools()->create([
            'tool_slug' => app(\App\Core\Tools\ToolCatalog::class)->all(false)->first()['slug'],
            'placement' => 'featured',
            'position' => 0,
        ]);

        $this->delete(route('admin.acquisition.contexts.destroy', $context->getKey()))
            ->assertRedirect(route('admin.acquisition.contexts.index'));

        $this->assertDatabaseMissing('acquisition_contexts', ['id' => $context->getKey()]);
        $this->assertDatabaseMissing('acquisition_context_tools', [
            'acquisition_context_id' => $context->getKey(),
        ]);
    }

    public function test_keyword_must_be_unique(): void
    {
        AcquisitionContextRecord::query()->create([
            'name' => 'Primeiro',
            'keyword' => 'campanha-unica',
            'status' => 'inactive',
        ]);

        $this->from(route('admin.acquisition.contexts.create'))
            ->post(route('admin.acquisition.contexts.store'), [
                'name' => 'Segundo',
                'keyword' => 'campanha-unica',
                'status' => 'inactive',
            ])
            ->assertRedirect(route('admin.acquisition.contexts.create'))
            ->assertSessionHasErrors('keyword');
    }
}
