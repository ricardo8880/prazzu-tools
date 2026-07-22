<?php

namespace Tests\Unit\Core\Acquisition;

use App\Core\Acquisition\Application\ResolveAcquisitionContext;
use App\Core\Acquisition\Contracts\AcquisitionContextRepository;
use App\Core\Acquisition\Domain\Data\AcquisitionCallToAction;
use App\Core\Acquisition\Domain\Data\AcquisitionContext;
use App\Core\Acquisition\Domain\Data\AcquisitionHero;
use App\Core\Acquisition\Domain\Enums\AcquisitionContextStatus;
use PHPUnit\Framework\TestCase;

final class ResolveAcquisitionContextTest extends TestCase
{
    public function test_it_returns_null_when_keyword_is_missing(): void
    {
        $repository = new class implements AcquisitionContextRepository
        {
            public bool $wasCalled = false;

            public function activeByKeyword(string $keyword): ?AcquisitionContext
            {
                $this->wasCalled = true;

                return null;
            }
        };

        $resolver = new ResolveAcquisitionContext($repository);

        self::assertNull($resolver->execute(null));
        self::assertNull($resolver->execute('   '));
        self::assertFalse($repository->wasCalled);
    }

    public function test_it_normalizes_keyword_and_returns_repository_result(): void
    {
        $context = $this->context();

        $repository = new class($context) implements AcquisitionContextRepository
        {
            public ?string $receivedKeyword = null;

            public function __construct(private readonly AcquisitionContext $context) {}

            public function activeByKeyword(string $keyword): ?AcquisitionContext
            {
                $this->receivedKeyword = $keyword;

                return $this->context;
            }
        };

        $resolver = new ResolveAcquisitionContext($repository);

        self::assertSame($context, $resolver->execute('  rescisao-video-01  '));
        self::assertSame('rescisao-video-01', $repository->receivedKeyword);
    }

    private function context(): AcquisitionContext
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
