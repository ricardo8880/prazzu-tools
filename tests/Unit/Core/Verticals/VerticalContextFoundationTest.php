<?php

namespace Tests\Unit\Core\Verticals;

use App\Core\Acquisition\Domain\Data\AcquisitionCallToAction;
use App\Core\Acquisition\Domain\Data\AcquisitionContext;
use App\Core\Acquisition\Domain\Data\AcquisitionHero;
use App\Core\Acquisition\Domain\Enums\AcquisitionContextStatus;
use App\Core\Verticals\Application\ResolveVerticalContext;
use App\Core\Verticals\Application\VerticalContext;
use App\Core\Verticals\Contracts\VerticalContextSource;
use App\Core\Verticals\Contracts\VerticalRegistry;
use App\Core\Verticals\Domain\Data\Vertical;
use App\Core\Verticals\Infrastructure\Config\ConfigVerticalRegistry;
use App\Core\Verticals\Infrastructure\Http\AcquisitionVerticalContextSource;
use Illuminate\Http\Request;
use Tests\TestCase;

final class VerticalContextFoundationTest extends TestCase
{
    public function test_config_registry_exposes_registered_and_default_vertical_without_closed_enum(): void
    {
        config()->set('verticals.registered', [
            'contabilidade' => ['name' => 'Contabilidade'],
            'rh' => ['name' => 'Recursos Humanos'],
        ]);
        config()->set('verticals.default', 'contabilidade');

        $registry = new ConfigVerticalRegistry;

        self::assertSame(['contabilidade', 'rh'], array_map(
            static fn (Vertical $vertical): string => $vertical->slug,
            $registry->all(),
        ));
        self::assertSame('Recursos Humanos', $registry->find('rh')?->name);
        self::assertSame('contabilidade', $registry->default()?->slug);
        self::assertNull($registry->find('juridico'));
    }

    public function test_resolver_uses_first_source_that_produces_a_vertical_and_allows_null_fallback(): void
    {
        $request = Request::create('/');
        $rh = new Vertical('rh', 'Recursos Humanos');

        $empty = new class implements VerticalContextSource
        {
            public function resolve(Request $request): ?Vertical
            {
                return null;
            }
        };

        $resolved = new class($rh) implements VerticalContextSource
        {
            public function __construct(private readonly Vertical $vertical) {}

            public function resolve(Request $request): ?Vertical
            {
                return $this->vertical;
            }
        };

        self::assertSame($rh, (new ResolveVerticalContext([$empty, $resolved]))->execute($request));
        self::assertNull((new ResolveVerticalContext([$empty]))->execute($request));
    }

    public function test_vertical_context_is_only_the_active_request_state(): void
    {
        $context = new VerticalContext;

        self::assertNull($context->active());
        self::assertNull($context->slug());

        $context->activate(new Vertical('contabilidade', 'Contabilidade'));

        self::assertSame('contabilidade', $context->slug());

        $context->activate(null);

        self::assertNull($context->active());
    }

    public function test_acquisition_context_can_contribute_without_becoming_vertical_context(): void
    {
        config()->set('verticals.registered', [
            'contabilidade' => ['name' => 'Contabilidade'],
            'rh' => ['name' => 'Recursos Humanos'],
        ]);
        config()->set('verticals.acquisition.keywords', [
            'rescisao-video-01' => 'rh',
        ]);

        $request = Request::create('/');
        $request->attributes->set('acquisition.context', $this->acquisitionContext());

        $source = new AcquisitionVerticalContextSource(new ConfigVerticalRegistry);
        $vertical = $source->resolve($request);

        self::assertSame('rh', $vertical?->slug);
        self::assertInstanceOf(AcquisitionContext::class, $request->attributes->get('acquisition.context'));
    }

    public function test_acquisition_mapping_to_unknown_vertical_is_ignored(): void
    {
        config()->set('verticals.registered', [
            'contabilidade' => ['name' => 'Contabilidade'],
        ]);
        config()->set('verticals.acquisition.keywords', [
            'rescisao-video-01' => 'nao-cadastrada',
        ]);

        $request = Request::create('/');
        $request->attributes->set('acquisition.context', $this->acquisitionContext());

        self::assertNull((new AcquisitionVerticalContextSource(new ConfigVerticalRegistry))->resolve($request));
    }

    private function acquisitionContext(): AcquisitionContext
    {
        return new AcquisitionContext(
            id: 1,
            name: 'Rescisão Instagram',
            keyword: 'rescisao-video-01',
            status: AcquisitionContextStatus::Active,
            campaignIdentifier: 'instagram-rescisao',
            hero: new AcquisitionHero(null, null, null, null, null),
            callToAction: new AcquisitionCallToAction(null, null, null, null, null),
            contextualMessage: null,
            contextualContinueLabel: null,
            contextualContinueUrl: null,
            contextualContinueToolSlug: null,
            toolsSectionTitle: null,
            primaryToolSlug: 'calculadora-rescisao',
            featuredToolSlugs: [],
            recommendedToolSlugs: [],
            articleSlugs: [],
        );
    }
}
