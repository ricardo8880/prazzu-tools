<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Presentation\Controllers;

use App\Core\Exceptions\InvalidValue;
use App\Http\Controllers\Controller;
use App\Tools\SimplesNacionalCalculator\Application\Actions\AnalyzeSimplesNacionalAlerts;
use App\Tools\SimplesNacionalCalculator\Application\Actions\CompareAnnexes;
use App\Tools\SimplesNacionalCalculator\Application\Actions\CompareScenarios;
use App\Tools\SimplesNacionalCalculator\Application\Actions\DeleteSimplesNacionalCalculation;
use App\Tools\SimplesNacionalCalculator\Application\Actions\ProjectAnnualSimplesNacional;
use App\Tools\SimplesNacionalCalculator\Application\Actions\SaveSimplesNacionalCalculation;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\AnalyzeAlertsRequest;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\AnnualProjectionRequest;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\CompareAnnexesRequest;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\CompareScenariosRequest;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\SaveCalculationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class SimplesNacionalPlusController extends Controller
{
    public function alerts(AnalyzeAlertsRequest $request, AnalyzeSimplesNacionalAlerts $action): RedirectResponse
    {
        try {
            $analysis = $action->execute($request->validated());
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['rbt12' => $exception->getMessage()]);
        }

        return back()->withInput()->with('alerts_analysis', $analysis);
    }

    public function compareScenarios(CompareScenariosRequest $request, CompareScenarios $action): RedirectResponse
    {
        try {
            $comparison = $action->execute($request->validated('scenarios'));
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['scenarios' => $exception->getMessage()]);
        }

        return back()->withInput()->with('scenario_comparison', $comparison->toArray());
    }

    public function compareAnnexes(CompareAnnexesRequest $request, CompareAnnexes $action): RedirectResponse
    {
        $input = $request->validated();

        try {
            $comparison = $action->execute($input['annexes'], (string) $input['rbt12'], (string) $input['monthly_revenue']);
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['rbt12' => $exception->getMessage()]);
        }

        return back()->withInput()->with('annex_comparison', $comparison->toArray());
    }

    public function project(AnnualProjectionRequest $request, ProjectAnnualSimplesNacional $action): RedirectResponse
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

        return back()->withInput()->with('annual_projection', $projection);
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
