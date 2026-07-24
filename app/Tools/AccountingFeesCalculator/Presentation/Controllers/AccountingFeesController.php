<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Presentation\Controllers;

use App\Core\Access\Services\ToolPersistenceAuthorizer;
use App\Core\Exceptions\InvalidValue;
use App\Core\Export\Services\TabularExportService;
use App\Core\ToolIntegration\Contracts\ToolResultPublisher;
use App\Core\ToolIntegration\Contracts\ToolResultResolver;
use App\Core\ToolIntegration\Data\IntegrationPayload;
use App\Http\Controllers\Controller;
use App\Tools\AccountingFeesCalculator\Application\Actions\BuildAccountingFeeHistoryExport;
use App\Tools\AccountingFeesCalculator\Application\Actions\BuildCommercialProposal;
use App\Tools\AccountingFeesCalculator\Application\Actions\BuildServiceContract;
use App\Tools\AccountingFeesCalculator\Application\Actions\CalculateAndStoreAccountingFees;
use App\Tools\AccountingFeesCalculator\Application\Actions\CalculateAndStoreFeeAdjustment;
use App\Tools\AccountingFeesCalculator\Application\Actions\ManageAccountingFeesHistory;
use App\Tools\AccountingFeesCalculator\Presentation\Requests\CalculateAccountingFeesRequest;
use App\Tools\AccountingFeesCalculator\Presentation\Requests\CalculateFeeAdjustmentRequest;
use App\Tools\AccountingFeesCalculator\Presentation\Requests\GenerateCommercialProposalRequest;
use App\Tools\AccountingFeesCalculator\Presentation\Requests\GenerateServiceContractRequest;
use App\Tools\AccountingFeesCalculator\Tool;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class AccountingFeesController extends Controller
{
    public function __construct(private readonly ToolResultResolver $resolver) {}

    public function index(): View
    {
        return view('tools-calculadora-de-honorarios-contabeis::index', ['taxSnapshotIntegration' => $this->resolver->latest('company-tax-snapshot', 1)]);
    }

    public function calculate(CalculateAccountingFeesRequest $request, CalculateAndStoreAccountingFees $action, ToolPersistenceAuthorizer $persistence, Tool $module, ToolResultPublisher $integrations): View
    {
        try {
            $outcome = $action->execute($request->validated(), $this->userId($request), $persistence->allowsHistory($module, $request->user()));
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['monthly_revenue' => $exception->getMessage()]);
        }

        $validated = $request->validated();
        $integrations->publish(new IntegrationPayload(sourceTool: ManageAccountingFeesHistory::TOOL_SLUG, contractName: 'company-operating-profile', contractVersion: 1, data: [
            'monthly_revenue' => (string) $validated['monthly_revenue'], 'employees' => (int) $validated['employees'], 'partners' => (int) $validated['partners'],
            'tax_regime' => (string) $validated['tax_regime'], 'business_segment' => (string) $validated['business_segment'],
        ]));

        $request->flash();

        return view('tools-calculadora-de-honorarios-contabeis::index', [
            'taxSnapshotIntegration' => $this->resolver->latest('company-tax-snapshot', 1),
            'calculationResult' => $outcome['result'],
            'successMessage' => $this->persistenceMessage($outcome['saved'], $request->user() !== null, 'Cálculo'),
        ]);
    }

    public function proposal(GenerateCommercialProposalRequest $request, BuildCommercialProposal $action): View
    {
        return view('tools-calculadora-de-honorarios-contabeis::proposal', ['proposal' => $action->execute($request->validated())]);
    }

    public function contract(GenerateServiceContractRequest $request, BuildServiceContract $action): View
    {
        return view('tools-calculadora-de-honorarios-contabeis::contract', ['contract' => $action->execute($request->validated())]);
    }

    public function history(Request $request, ManageAccountingFeesHistory $history): View
    {
        $favorite = $request->boolean('favorite');
        $page = $history->paginate($this->requiredUserId($request), ManageAccountingFeesHistory::TYPE_CALCULATION, max(1, $request->integer('page', 1)), $favorite);

        return view('tools-calculadora-de-honorarios-contabeis::history.index', ['calculations' => $this->paginator($page, $request), 'favorite' => $favorite]);
    }

    public function duplicateCalculation(Request $request, string $run, ManageAccountingFeesHistory $history): RedirectResponse
    {
        $entry = $history->owned($run, $this->requiredUserId($request), ManageAccountingFeesHistory::TYPE_CALCULATION);
        $input = $entry->input;
        unset($input['run_type']);

        return redirect()->route('tools.calculadora-de-honorarios-contabeis.index')->withInput($input)->with('calculation_result', $entry->result)->with('success', 'Cálculo duplicado. Ajuste os dados e gere uma nova versão.');
    }

    public function toggleFavorite(Request $request, string $run, ManageAccountingFeesHistory $history): RedirectResponse
    {
        $favorite = $history->toggleFavorite($run, $this->requiredUserId($request));

        return back()->with('success', $favorite ? 'Cálculo adicionado aos favoritos.' : 'Cálculo removido dos favoritos.');
    }

    public function exportHistory(Request $request, BuildAccountingFeeHistoryExport $action, TabularExportService $exporter): StreamedResponse
    {
        return $exporter->csv('historico-honorarios-contabeis.csv', ['Data', 'Faturamento mensal', 'Regime', 'Funcionários', 'Notas', 'Complexidade', 'Honorário mínimo', 'Honorário recomendado', 'Referência superior'], $action->execute($this->requiredUserId($request)));
    }

    public function deleteCalculation(Request $request, string $run, ManageAccountingFeesHistory $history): RedirectResponse
    {
        $history->delete($run, $this->requiredUserId($request), ManageAccountingFeesHistory::TYPE_CALCULATION);

        return back()->with('success', 'Cálculo removido do histórico.');
    }

    public function adjustments(Request $request, ManageAccountingFeesHistory $history): View
    {
        $adjustments = $request->user() === null ? null : $this->paginator($history->paginate($this->requiredUserId($request), ManageAccountingFeesHistory::TYPE_ADJUSTMENT, max(1, $request->integer('page', 1))), $request);

        return view('tools-calculadora-de-honorarios-contabeis::adjustments.index', compact('adjustments'));
    }

    public function calculateAdjustment(CalculateFeeAdjustmentRequest $request, CalculateAndStoreFeeAdjustment $action, ToolPersistenceAuthorizer $persistence, Tool $module, ManageAccountingFeesHistory $history): View
    {
        try {
            $outcome = $action->execute($request->validated(), $this->userId($request), $persistence->allowsHistory($module, $request->user()));
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['percentage' => $exception->getMessage()]);
        }

        $adjustments = $request->user() === null ? null : $this->paginator($history->paginate($this->requiredUserId($request), ManageAccountingFeesHistory::TYPE_ADJUSTMENT, max(1, $request->integer('page', 1))), $request);

        $request->flash();

        return view('tools-calculadora-de-honorarios-contabeis::adjustments.index', [
            'adjustments' => $adjustments,
            'adjustmentResult' => $outcome['result'],
            'successMessage' => $this->persistenceMessage($outcome['saved'], $request->user() !== null, 'Reajuste'),
        ]);
    }

    public function deleteAdjustment(Request $request, string $run, ManageAccountingFeesHistory $history): RedirectResponse
    {
        $history->delete($run, $this->requiredUserId($request), ManageAccountingFeesHistory::TYPE_ADJUSTMENT);

        return back()->with('success', 'Reajuste removido do histórico.');
    }

    private function userId(Request $request): ?int
    {
        return $request->user() === null ? null : (int) $request->user()->getAuthIdentifier();
    }

    private function requiredUserId(Request $request): int
    {
        return (int) $request->user()->getAuthIdentifier();
    }

    private function paginator($page, Request $request): LengthAwarePaginator
    {
        return new LengthAwarePaginator($page->items, $page->total, $page->perPage, $page->page, ['path' => $request->url(), 'query' => $request->query()]);
    }

    private function persistenceMessage(bool $saved, bool $authenticated, string $resource): string
    {
        return $saved ? "{$resource} concluído e salvo no seu histórico." : (! $authenticated ? "{$resource} concluído. Entre em uma conta para salvar quando o histórico estiver disponível." : "{$resource} concluído. O histórico é um recurso de continuidade do Prazzu Plus.");
    }
}
