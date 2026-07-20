<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Exceptions\InvalidValue;
use App\Core\ToolIntegration\Contracts\ToolResultPublisher;
use App\Core\ToolIntegration\Contracts\ToolResultResolver;
use App\Core\ToolIntegration\Data\IntegrationPayload;
use App\Core\Export\Services\TabularExportService;
use App\Http\Controllers\Controller;
use App\Tools\AccountingFeesCalculator\Application\Actions\BuildAccountingFeeHistoryExport;
use App\Tools\AccountingFeesCalculator\Application\Actions\BuildCommercialProposal;
use App\Tools\AccountingFeesCalculator\Application\Actions\BuildServiceContract;
use App\Tools\AccountingFeesCalculator\Application\Actions\CalculateAndStoreAccountingFees;
use App\Tools\AccountingFeesCalculator\Application\Actions\CalculateAndStoreFeeAdjustment;
use App\Tools\AccountingFeesCalculator\Application\Actions\DeleteAccountingFeeCalculation;
use App\Tools\AccountingFeesCalculator\Application\Actions\DeleteFeeAdjustment;
use App\Tools\AccountingFeesCalculator\Application\Actions\DuplicateAccountingFeeCalculation;
use App\Tools\AccountingFeesCalculator\Application\Actions\ListAccountingFeeHistory;
use App\Tools\AccountingFeesCalculator\Application\Actions\ListFeeAdjustments;
use App\Tools\AccountingFeesCalculator\Application\Actions\ToggleAccountingFeeCalculationFavorite;
use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingFeeCalculation;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\FeeAdjustment;
use App\Tools\AccountingFeesCalculator\Presentation\Requests\CalculateAccountingFeesRequest;
use App\Tools\AccountingFeesCalculator\Presentation\Requests\CalculateFeeAdjustmentRequest;
use App\Tools\AccountingFeesCalculator\Presentation\Requests\GenerateCommercialProposalRequest;
use App\Tools\AccountingFeesCalculator\Presentation\Requests\GenerateServiceContractRequest;
use App\Tools\AccountingFeesCalculator\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class AccountingFeesController extends Controller
{
    public function index(ToolResultResolver $integrations): View
    {
        return view('tools-calculadora-de-honorarios-contabeis::index', [
            'taxSnapshotIntegration' => $integrations->latest('company-tax-snapshot', 1),
        ]);
    }

    public function calculate(
        CalculateAccountingFeesRequest $request,
        CalculateAndStoreAccountingFees $action,
        ToolPersistenceAuthorizer $persistence,
        Tool $module,
        ToolResultPublisher $integrations,
    ): RedirectResponse {
        $canPersist = $persistence->allowsHistory($module, $request->user());

        try {
            $outcome = $action->execute($request->validated(), $this->owner($request), $canPersist);
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages([
                'monthly_revenue' => $exception->getMessage(),
            ]);
        }

        $validated = $request->validated();
        $integrations->publish(new IntegrationPayload(
            sourceTool: 'calculadora-de-honorarios-contabeis',
            contractName: 'company-operating-profile',
            contractVersion: 1,
            data: [
                'monthly_revenue' => (string) $validated['monthly_revenue'],
                'employees' => (int) $validated['employees'],
                'partners' => (int) $validated['partners'],
                'tax_regime' => (string) $validated['tax_regime'],
                'business_segment' => (string) $validated['business_segment'],
            ],
        ));

        return back()
            ->withInput()
            ->with('calculation_result', $outcome['result'])
            ->with('success', $this->persistenceMessage(
                saved: $outcome['saved'],
                authenticated: $request->user() !== null,
                resource: 'Cálculo',
            ));
    }

    public function proposal(GenerateCommercialProposalRequest $request, BuildCommercialProposal $action): View
    {
        return view('tools-calculadora-de-honorarios-contabeis::proposal', [
            'proposal' => $action->execute($request->validated()),
        ]);
    }

    public function contract(GenerateServiceContractRequest $request, BuildServiceContract $action): View
    {
        return view('tools-calculadora-de-honorarios-contabeis::contract', [
            'contract' => $action->execute($request->validated()),
        ]);
    }

    public function history(Request $request, ListAccountingFeeHistory $action): View
    {
        $favorite = $request->boolean('favorite');
        $calculations = $action->execute($this->owner($request), $favorite);

        return view('tools-calculadora-de-honorarios-contabeis::history.index', compact('calculations', 'favorite'));
    }

    public function duplicateCalculation(
        Request $request,
        AccountingFeeCalculation $calculation,
        DuplicateAccountingFeeCalculation $action,
    ): RedirectResponse {
        $duplicate = $action->execute($calculation, $this->owner($request));

        return redirect()->route('tools.calculadora-de-honorarios-contabeis.index')
            ->withInput($duplicate['input'])
            ->with('calculation_result', $duplicate['result'])
            ->with('success', 'Cálculo duplicado. Ajuste os dados e gere uma nova versão.');
    }

    public function toggleFavorite(
        Request $request,
        AccountingFeeCalculation $calculation,
        ToggleAccountingFeeCalculationFavorite $action,
    ): RedirectResponse {
        $favorite = $action->execute($calculation, $this->owner($request));

        return back()->with('success', $favorite
            ? 'Cálculo adicionado aos favoritos.'
            : 'Cálculo removido dos favoritos.');
    }

    public function exportHistory(
        Request $request,
        BuildAccountingFeeHistoryExport $action,
        TabularExportService $exporter,
    ): StreamedResponse {
        $rows = $action->execute($this->owner($request));

        return $exporter->csv(
            'historico-honorarios-contabeis.csv',
            ['Data', 'Faturamento mensal', 'Regime', 'Funcionários', 'Notas', 'Complexidade', 'Honorário mínimo', 'Honorário recomendado', 'Referência superior'],
            $rows,
        );
    }

    public function deleteCalculation(
        Request $request,
        AccountingFeeCalculation $calculation,
        DeleteAccountingFeeCalculation $action,
    ): RedirectResponse {
        $action->execute($calculation, $this->owner($request));

        return back()->with('success', 'Cálculo removido do histórico.');
    }

    public function adjustments(Request $request, ListFeeAdjustments $action): View
    {
        $adjustments = $action->execute($this->owner($request));

        return view('tools-calculadora-de-honorarios-contabeis::adjustments.index', compact('adjustments'));
    }

    public function calculateAdjustment(
        CalculateFeeAdjustmentRequest $request,
        CalculateAndStoreFeeAdjustment $action,
        ToolPersistenceAuthorizer $persistence,
        Tool $module,
    ): RedirectResponse {
        $canPersist = $persistence->allowsHistory($module, $request->user());

        try {
            $outcome = $action->execute($request->validated(), $this->owner($request), $canPersist);
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['percentage' => $exception->getMessage()]);
        }

        return redirect()->route('tools.calculadora-de-honorarios-contabeis.adjustments.index')
            ->with('success', $this->persistenceMessage(
                saved: $outcome['saved'],
                authenticated: $request->user() !== null,
                resource: 'Reajuste',
            ))
            ->with('adjustment_result', $outcome['result']);
    }

    public function deleteAdjustment(
        Request $request,
        FeeAdjustment $adjustment,
        DeleteFeeAdjustment $action,
    ): RedirectResponse {
        $action->execute($adjustment, $this->owner($request));

        return back()->with('success', 'Reajuste removido do histórico.');
    }

    private function owner(Request $request): AccountingFeesOwner
    {
        $sessionKey = (string) $request->session()->get('accounting_fees_history_key');

        if ($sessionKey === '') {
            $sessionKey = (string) Str::uuid();
            $request->session()->put('accounting_fees_history_key', $sessionKey);
        }

        return new AccountingFeesOwner(
            userId: $request->user() === null
                ? null
                : (int) $request->user()->getAuthIdentifier(),
            sessionKey: $sessionKey,
        );
    }

    private function persistenceMessage(bool $saved, bool $authenticated, string $resource): string
    {
        if ($saved) {
            return "{$resource} concluído e salvo no seu histórico.";
        }

        if (! $authenticated) {
            return "{$resource} concluído. Entre em uma conta para salvar quando o histórico estiver disponível.";
        }

        return "{$resource} concluído. O histórico é um recurso de continuidade do Prazzu Plus.";
    }
}
