<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Presentation\Controllers;

use App\Core\Exceptions\InvalidValue;
use App\Core\Export\Services\TabularExportService;
use App\Http\Controllers\Controller;
use App\Tools\AccountingFeesCalculator\Application\Actions\BuildAccountingFeeHistoryExport;
use App\Tools\AccountingFeesCalculator\Application\Actions\BuildCommercialProposal;
use App\Tools\AccountingFeesCalculator\Application\Actions\BuildServiceContract;
use App\Tools\AccountingFeesCalculator\Application\Actions\CalculateAndStoreAccountingFees;
use App\Tools\AccountingFeesCalculator\Application\Actions\CalculateAndStoreFeeAdjustment;
use App\Tools\AccountingFeesCalculator\Application\Actions\DeleteAccountingClient;
use App\Tools\AccountingFeesCalculator\Application\Actions\DeleteAccountingFeeCalculation;
use App\Tools\AccountingFeesCalculator\Application\Actions\DeleteFeeAdjustment;
use App\Tools\AccountingFeesCalculator\Application\Actions\DuplicateAccountingFeeCalculation;
use App\Tools\AccountingFeesCalculator\Application\Actions\GetAccountingClientForEditing;
use App\Tools\AccountingFeesCalculator\Application\Actions\GetSharedAccountingFeeCalculation;
use App\Tools\AccountingFeesCalculator\Application\Actions\ListAccountingClients;
use App\Tools\AccountingFeesCalculator\Application\Actions\ListAccountingFeeHistory;
use App\Tools\AccountingFeesCalculator\Application\Actions\ListFeeAdjustments;
use App\Tools\AccountingFeesCalculator\Application\Actions\PrepareNewAccountingClient;
use App\Tools\AccountingFeesCalculator\Application\Actions\ShareAccountingFeeCalculation;
use App\Tools\AccountingFeesCalculator\Application\Actions\StoreAccountingClient;
use App\Tools\AccountingFeesCalculator\Application\Actions\ToggleAccountingFeeCalculationFavorite;
use App\Tools\AccountingFeesCalculator\Application\Actions\UpdateAccountingClient;
use App\Tools\AccountingFeesCalculator\Application\Data\AccountingFeesOwner;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingClient;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\AccountingFeeCalculation;
use App\Tools\AccountingFeesCalculator\Infrastructure\Models\FeeAdjustment;
use App\Tools\AccountingFeesCalculator\Presentation\Requests\CalculateAccountingFeesRequest;
use App\Tools\AccountingFeesCalculator\Presentation\Requests\CalculateFeeAdjustmentRequest;
use App\Tools\AccountingFeesCalculator\Presentation\Requests\GenerateCommercialProposalRequest;
use App\Tools\AccountingFeesCalculator\Presentation\Requests\GenerateServiceContractRequest;
use App\Tools\AccountingFeesCalculator\Presentation\Requests\StoreAccountingClientRequest;
use App\Tools\AccountingFeesCalculator\Presentation\Requests\UpdateAccountingClientRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class AccountingFeesController extends Controller
{
    public function index(): View
    {
        return view('tools-calculadora-de-honorarios-contabeis::index');
    }

    public function calculate(
        CalculateAccountingFeesRequest $request,
        CalculateAndStoreAccountingFees $action,
    ): RedirectResponse {
        try {
            $outcome = $action->execute($request->validated(), $this->owner($request));
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages([
                'monthly_revenue' => $exception->getMessage(),
            ]);
        }

        return back()
            ->withInput()
            ->with('calculation_result', $outcome['result'])
            ->with('success', $outcome['saved']
                ? 'Cálculo concluído e salvo no seu histórico.'
                : 'Cálculo concluído. Crie uma conta gratuita para salvar e recuperar seus resultados.');
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

    public function shareCalculation(
        Request $request,
        AccountingFeeCalculation $calculation,
        ShareAccountingFeeCalculation $action,
    ): RedirectResponse {
        $token = $action->execute($calculation, $this->owner($request));

        return back()->with('share_url', route('tools.calculadora-de-honorarios-contabeis.shared', $token));
    }

    public function sharedCalculation(string $token, GetSharedAccountingFeeCalculation $action): View
    {
        return view('tools-calculadora-de-honorarios-contabeis::history.shared', [
            'calculation' => $action->execute($token),
        ]);
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
    ): RedirectResponse {
        try {
            $outcome = $action->execute($request->validated(), $this->owner($request));
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['percentage' => $exception->getMessage()]);
        }

        return redirect()->route('tools.calculadora-de-honorarios-contabeis.adjustments.index')
            ->with('success', $outcome['saved']
                ? 'Reajuste calculado e salvo no seu histórico.'
                : 'Reajuste calculado. Crie uma conta gratuita para salvar este resultado.')
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

    public function crm(Request $request, ListAccountingClients $action): View
    {
        $search = trim((string) $request->query('search'));
        $status = (string) $request->query('status');
        $listing = $action->execute($this->owner($request), $search, $status);

        return view('tools-calculadora-de-honorarios-contabeis::crm.index', [
            ...$listing,
            'search' => $search,
            'status' => $status,
        ]);
    }

    public function createClient(PrepareNewAccountingClient $action): View
    {
        return view('tools-calculadora-de-honorarios-contabeis::crm.form', [
            'client' => $action->execute(),
        ]);
    }

    public function storeClient(
        StoreAccountingClientRequest $request,
        StoreAccountingClient $action,
    ): RedirectResponse {
        try {
            $action->execute($request->validated(), $this->owner($request));
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['monthly_fee' => $exception->getMessage()]);
        }

        return redirect()->route('tools.calculadora-de-honorarios-contabeis.crm.index')
            ->with('success', 'Cliente adicionado ao CRM.');
    }

    public function editClient(
        Request $request,
        AccountingClient $client,
        GetAccountingClientForEditing $action,
    ): View {
        return view('tools-calculadora-de-honorarios-contabeis::crm.form', [
            'client' => $action->execute($client, $this->owner($request)),
        ]);
    }

    public function updateClient(
        UpdateAccountingClientRequest $request,
        AccountingClient $client,
        UpdateAccountingClient $action,
    ): RedirectResponse {
        try {
            $action->execute($client, $request->validated(), $this->owner($request));
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['monthly_fee' => $exception->getMessage()]);
        }

        return redirect()->route('tools.calculadora-de-honorarios-contabeis.crm.index')
            ->with('success', 'Cadastro atualizado com sucesso.');
    }

    public function deleteClient(
        Request $request,
        AccountingClient $client,
        DeleteAccountingClient $action,
    ): RedirectResponse {
        $action->execute($client, $this->owner($request));

        return redirect()->route('tools.calculadora-de-honorarios-contabeis.crm.index')
            ->with('success', 'Cliente removido do CRM.');
    }

    private function owner(Request $request): AccountingFeesOwner
    {
        $sessionKey = (string) $request->session()->get('accounting_fees_crm_key');

        if ($sessionKey === '') {
            $sessionKey = (string) Str::uuid();
            $request->session()->put('accounting_fees_crm_key', $sessionKey);
        }

        return new AccountingFeesOwner(
            userId: $request->user() === null
                ? null
                : (int) $request->user()->getAuthIdentifier(),
            sessionKey: $sessionKey,
        );
    }
}
