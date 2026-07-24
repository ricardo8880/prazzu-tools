<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Presentation\Controllers;

use App\Core\Access\Contracts\ToolFeatureAccessGate;
use App\Core\Exceptions\InvalidValue;
use App\Core\ToolIntegration\Contracts\ToolResultResolver;
use App\Core\Tools\ToolRegistry;
use App\Http\Controllers\Controller;
use App\Tools\SimplesNacionalCalculator\Application\Actions\AnalyzeSimplesNacionalAlerts;
use App\Tools\SimplesNacionalCalculator\Application\Actions\CompareAnnexes;
use App\Tools\SimplesNacionalCalculator\Application\Actions\CompareScenarios;
use App\Tools\SimplesNacionalCalculator\Application\Actions\DeleteSimplesNacionalCalculation;
use App\Tools\SimplesNacionalCalculator\Application\Actions\ListSimplesNacionalCalculations;
use App\Tools\SimplesNacionalCalculator\Application\Actions\ProjectAnnualSimplesNacional;
use App\Tools\SimplesNacionalCalculator\Application\Actions\SaveSimplesNacionalCalculation;
use App\Tools\SimplesNacionalCalculator\Application\Features\SimplesNacionalFeature;
use App\Tools\SimplesNacionalCalculator\Domain\Enums\TaxAnnex;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\AnalyzeAlertsRequest;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\AnnualProjectionRequest;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\CompareAnnexesRequest;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\CompareScenariosRequest;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\SaveCalculationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class SimplesNacionalPlusController extends Controller
{
    public function __construct(
        private readonly ToolFeatureAccessGate $featureGate,
        private readonly ToolRegistry $tools,
        private readonly ListSimplesNacionalCalculations $history,
        private readonly ToolResultResolver $integrations,
    ) {}
    public function alerts(AnalyzeAlertsRequest $request, AnalyzeSimplesNacionalAlerts $action): View
    {
        try {
            $analysis = $action->execute($request->validated());
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['rbt12' => $exception->getMessage()]);
        }

        $request->flash();

        return $this->renderIndex($request, ['alertsAnalysis' => $analysis]);
    }

    public function compareScenarios(CompareScenariosRequest $request, CompareScenarios $action): View
    {
        try {
            $comparison = $action->execute($request->validated('scenarios'));
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['scenarios' => $exception->getMessage()]);
        }

        $request->flash();

        return $this->renderIndex($request, ['scenarioComparison' => $comparison->toArray()]);
    }

    public function compareAnnexes(CompareAnnexesRequest $request, CompareAnnexes $action): View
    {
        $input = $request->validated();

        try {
            $comparison = $action->execute($input['annexes'], (string) $input['rbt12'], (string) $input['monthly_revenue']);
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['rbt12' => $exception->getMessage()]);
        }

        $request->flash();

        return $this->renderIndex($request, ['annexComparison' => $comparison->toArray()]);
    }

    public function project(AnnualProjectionRequest $request, ProjectAnnualSimplesNacional $action): View
    {
        $input = $request->validated();

        try {
            $projection = $action->execute(
                (string) $input['annex'],
                (string) $input['monthly_revenue'],
                (string) $input['monthly_growth'],
            );
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['monthly_revenue' => $exception->getMessage()]);
        }

        $request->flash();

        return $this->renderIndex($request, ['annualProjection' => $projection]);
    }

    /** @param array<string, mixed> $resultData */
    private function renderIndex(Request $request, array $resultData): View
    {
        $manifest = $this->tools->findManifest('calculadora-simples-nacional');
        abort_if($manifest === null, 404);

        return view('tools-calculadora-simples-nacional::index', [
            'annexes' => TaxAnnex::cases(),
            'history' => $this->history->recent($request->user() === null ? null : (int) $request->user()->getAuthIdentifier()),
            'operatingProfileIntegration' => $this->integrations->latest('company-operating-profile', 1),
            'plusAccess' => collect([
                SimplesNacionalFeature::CompareScenarios,
                SimplesNacionalFeature::CompareAnnexes,
                SimplesNacionalFeature::MonthlyHistory,
                SimplesNacionalFeature::AnnualProjection,
                SimplesNacionalFeature::Alerts,
            ])->mapWithKeys(fn (SimplesNacionalFeature $feature): array => [
                $feature->value => $this->featureGate->decide($manifest, $feature->value, $request->user())->allowed,
            ])->all(),
            ...$resultData,
        ]);
    }

    public function save(
        SaveCalculationRequest $request,
        SaveSimplesNacionalCalculation $action,
    ): RedirectResponse {
        try {
            $action->execute(
                $request->validated(),
                (int) $request->user()->getAuthIdentifier(),
            );
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['rbt12' => $exception->getMessage()]);
        }

        return back()->with('history_success', 'Cálculo salvo no histórico mensal.');
    }

    public function destroy(
        Request $request,
        string $calculation,
        DeleteSimplesNacionalCalculation $action,
    ): RedirectResponse {
        $action->execute(
            $calculation,
            (int) $request->user()->getAuthIdentifier(),
        );

        return back()->with('history_success', 'Registro removido do histórico.');
    }
}
