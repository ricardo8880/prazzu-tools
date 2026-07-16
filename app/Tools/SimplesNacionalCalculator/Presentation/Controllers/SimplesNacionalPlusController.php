<?php

declare(strict_types=1);

namespace App\Tools\SimplesNacionalCalculator\Presentation\Controllers;

use App\Core\Exceptions\InvalidValue;
use App\Http\Controllers\Controller;
use App\Tools\SimplesNacionalCalculator\Application\Actions\CalculateSimplesNacional;
use App\Tools\SimplesNacionalCalculator\Application\Actions\CompareAnnexes;
use App\Tools\SimplesNacionalCalculator\Application\Actions\CompareScenarios;
use App\Tools\SimplesNacionalCalculator\Application\Actions\ProjectAnnualSimplesNacional;
use App\Tools\SimplesNacionalCalculator\Infrastructure\Models\SimplesNacionalCalculation;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\AnnualProjectionRequest;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\CompareAnnexesRequest;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\CompareScenariosRequest;
use App\Tools\SimplesNacionalCalculator\Presentation\Requests\SaveCalculationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

final class SimplesNacionalPlusController extends Controller
{
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
                (float) $input['monthly_growth'],
            );
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['monthly_revenue' => $exception->getMessage()]);
        }

        return back()->withInput()->with('annual_projection', $projection);
    }

    public function save(
        SaveCalculationRequest $request,
        CalculateSimplesNacional $calculate,
    ): RedirectResponse {
        $input = $request->validated();

        try {
            $result = $calculate->execute($input)->toArray();
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['rbt12' => $exception->getMessage()]);
        }

        SimplesNacionalCalculation::query()->create([
            'user_id' => $request->user()->getAuthIdentifier(),
            'session_key' => null,
            'company_name' => $input['company_name'],
            'reference_month' => $input['reference_month'].'-01',
            'annex' => $input['annex'],
            'rbt12_cents' => $this->moneyToCents($result['rbt12']),
            'monthly_revenue_cents' => $this->moneyToCents($result['monthly_revenue']),
            'estimated_das_cents' => $this->moneyToCents($result['estimated_das']),
            'effective_rate' => str_replace(['%', ','], ['', '.'], $result['effective_rate']),
            'payload' => $result,
        ]);

        return back()->with('history_success', 'Cálculo salvo no histórico mensal.');
    }

    public function destroy(Request $request, SimplesNacionalCalculation $calculation): RedirectResponse
    {
        abort_unless($this->owns($request, $calculation), 404);
        $calculation->delete();

        return back()->with('history_success', 'Registro removido do histórico.');
    }

    public static function historyFor(Request $request)
    {
        if ($request->user() === null) {
            return collect();
        }

        return SimplesNacionalCalculation::query()
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->latest('reference_month')
            ->latest('id')
            ->limit(24)
            ->get();
    }

    private function owns(Request $request, SimplesNacionalCalculation $calculation): bool
    {
        return $request->user() !== null
            && (int) $calculation->user_id === (int) $request->user()->getAuthIdentifier();
    }

    private function moneyToCents(string $value): int
    {
        return (int) str_replace(['R$', '.', ',', ' '], ['', '', '', ''], $value);
    }
}
