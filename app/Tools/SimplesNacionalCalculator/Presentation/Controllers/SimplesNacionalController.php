<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Presentation\Controllers;

use App\Core\Access\Contracts\ToolFeatureAccessGate;
use App\Core\Exceptions\InvalidValue;
use App\Core\ToolIntegration\Contracts\ToolResultPublisher;
use App\Core\ToolIntegration\Contracts\ToolResultResolver;
use App\Core\Tools\ToolRegistry;
use App\Http\Controllers\Controller;
use App\Tools\SimplesNacionalCalculator\Application\Actions\CalculateFactorR;
use App\Tools\SimplesNacionalCalculator\Application\Actions\ListSimplesNacionalCalculations;
use App\Tools\SimplesNacionalCalculator\Application\Calculators\StandardSimplesNacionalCalculator;
use App\Tools\SimplesNacionalCalculator\Application\Data\SimplesNacionalCalculationInput;
use App\Tools\SimplesNacionalCalculator\Application\Features\SimplesNacionalFeature;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\CalculateSimplesNacionalRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class SimplesNacionalController extends Controller
{
    public function __construct(private readonly ToolResultResolver $resolver) {}

    public function index(
        Request $request,
        ToolFeatureAccessGate $featureGate,
        ToolRegistry $tools,
        ListSimplesNacionalCalculations $history,
    ): View {
        $manifest = $tools->findManifest('calculadora-simples-nacional');
        abort_if($manifest === null, 404);

        return view('tools-calculadora-simples-nacional::index', [
            'annexes' => TaxAnnex::cases(),
            'history' => $history->recent(
                $request->user() === null
                    ? null
                    : (int) $request->user()->getAuthIdentifier(),
            ),
            'operatingProfileIntegration' => $this->resolver->latest('company-operating-profile', 1),
            'plusAccess' => collect([
                SimplesNacionalFeature::CompareScenarios,
                SimplesNacionalFeature::CompareAnnexes,
                SimplesNacionalFeature::MonthlyHistory,
                SimplesNacionalFeature::AnnualProjection,
                SimplesNacionalFeature::Alerts,
            ])->mapWithKeys(fn (SimplesNacionalFeature $feature): array => [
                $feature->value => $featureGate->decide($manifest, $feature->value, $request->user())->allowed,
            ])->all(),
        ]);
    }

    public function calculate(
        CalculateSimplesNacionalRequest $request,
        StandardSimplesNacionalCalculator $calculateSimples,
        CalculateFactorR $calculateFactorR,
        ToolResultPublisher $integrations,
        ToolFeatureAccessGate $featureGate,
        ToolRegistry $tools,
        ListSimplesNacionalCalculations $history,
    ): View {
        $input = $request->validated();
        $factorRResult = null;

        try {
            if ($request->boolean('use_factor_r')) {
                $factorRResult = $calculateFactorR->execute([
                    'payroll_12' => (string) $input['payroll_12'],
                    'rbt12' => (string) $input['rbt12'],
                ]);

                $input['annex'] = $factorRResult->applicableAnnex->value;
            }

            $calculation = $calculateSimples->calculate(SimplesNacionalCalculationInput::fromArray([
                'annex' => (string) $input['annex'],
                'rbt12' => (string) $input['rbt12'],
                'monthly_revenue' => (string) $input['monthly_revenue'],
            ]));
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages([
                'rbt12' => $exception->getMessage(),
            ]);
        }

        $resultData = $calculation->details;

        if ($calculation->integrationPayload !== null) {
            $integrations->publish($calculation->integrationPayload);
        }

        $manifest = $tools->findManifest('calculadora-simples-nacional');
        abort_if($manifest === null, 404);
        $request->flash();

        return view('tools-calculadora-simples-nacional::index', [
            'annexes' => TaxAnnex::cases(),
            'history' => $history->recent($request->user() === null ? null : (int) $request->user()->getAuthIdentifier()),
            'operatingProfileIntegration' => $this->resolver->latest('company-operating-profile', 1),
            'plusAccess' => collect([
                SimplesNacionalFeature::CompareScenarios,
                SimplesNacionalFeature::CompareAnnexes,
                SimplesNacionalFeature::MonthlyHistory,
                SimplesNacionalFeature::AnnualProjection,
                SimplesNacionalFeature::Alerts,
            ])->mapWithKeys(fn (SimplesNacionalFeature $feature): array => [
                $feature->value => $featureGate->decide($manifest, $feature->value, $request->user())->allowed,
            ])->all(),
            'calculationResult' => $resultData,
            'factorRResult' => $factorRResult?->toArray(),
        ]);
    }
}
