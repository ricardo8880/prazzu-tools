<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Presentation\Controllers;

use App\Core\Exceptions\InvalidValue;
use App\Http\Controllers\Controller;
use App\Tools\AccountingFeesCalculator\Application\Actions\BuildCommercialProposal;
use App\Tools\AccountingFeesCalculator\Application\Actions\BuildServiceContract;
use App\Tools\AccountingFeesCalculator\Application\Actions\CalculateAccountingFees;
use App\Tools\AccountingFeesCalculator\Application\Actions\CalculateFeeAdjustment;
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
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

final class AccountingFeesController extends Controller
{
    public function index(): View
    {
        return view('tools-calculadora-de-honorarios-contabeis::index');
    }

    public function calculate(CalculateAccountingFeesRequest $request, CalculateAccountingFees $action): RedirectResponse
    {
        try {
            $result = $action->execute($request->validated());
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages([
                'monthly_revenue' => $exception->getMessage(),
            ]);
        }

        $resultData = $result->toArray();
        $saved = false;

        if ($request->user() !== null) {
            AccountingFeeCalculation::query()->create([
                'user_id' => $request->user()->getAuthIdentifier(),
                'session_key' => null,
                'input' => $request->validated(),
                'result' => $resultData,
            ]);
            $saved = true;
        }

        return back()
            ->withInput()
            ->with('calculation_result', $resultData)
            ->with('success', $saved
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

    public function history(Request $request): View
    {
        $userId = (int) $request->user()->getAuthIdentifier();
        $favorite = $request->boolean('favorite');

        $calculations = AccountingFeeCalculation::query()
            ->visibleTo($userId, '')
            ->when($favorite, fn ($query) => $query->where('is_favorite', true))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('tools-calculadora-de-honorarios-contabeis::history.index', compact('calculations', 'favorite'));
    }

    public function duplicateCalculation(Request $request, AccountingFeeCalculation $calculation): RedirectResponse
    {
        $this->ensureCalculationVisible($request, $calculation);

        return redirect()->route('tools.calculadora-de-honorarios-contabeis.index')
            ->withInput($calculation->input)
            ->with('calculation_result', $calculation->result)
            ->with('success', 'Cálculo duplicado. Ajuste os dados e gere uma nova versão.');
    }

    public function toggleFavorite(Request $request, AccountingFeeCalculation $calculation): RedirectResponse
    {
        $this->ensureCalculationVisible($request, $calculation);
        $calculation->update(['is_favorite' => ! $calculation->is_favorite]);

        return back()->with('success', $calculation->is_favorite ? 'Cálculo adicionado aos favoritos.' : 'Cálculo removido dos favoritos.');
    }

    public function shareCalculation(Request $request, AccountingFeeCalculation $calculation): RedirectResponse
    {
        $this->ensureCalculationVisible($request, $calculation);

        if ($calculation->share_token === null) {
            $calculation->update(['share_token' => (string) Str::uuid()]);
        }

        return back()->with('share_url', route('tools.calculadora-de-honorarios-contabeis.shared', $calculation->share_token));
    }

    public function sharedCalculation(string $token): View
    {
        $calculation = AccountingFeeCalculation::query()->where('share_token', $token)->firstOrFail();

        return view('tools-calculadora-de-honorarios-contabeis::history.shared', compact('calculation'));
    }

    public function exportHistory(Request $request): StreamedResponse
    {
        [$userId, $sessionKey] = $this->owner($request);
        $calculations = AccountingFeeCalculation::query()->visibleTo($userId, '')->latest()->get();

        return response()->streamDownload(function () use ($calculations): void {
            $output = fopen('php://output', 'wb');
            fwrite($output, "\xEF\xBB\xBF");
            fputcsv($output, ['Data', 'Faturamento mensal', 'Regime', 'Funcionários', 'Notas', 'Complexidade', 'Honorário mínimo', 'Honorário recomendado', 'Referência superior'], ';');

            foreach ($calculations as $calculation) {
                fputcsv($output, [
                    $calculation->created_at?->format('d/m/Y H:i'),
                    data_get($calculation->input, 'monthly_revenue'),
                    data_get($calculation->input, 'tax_regime'),
                    data_get($calculation->input, 'employees'),
                    data_get($calculation->input, 'monthly_invoices'),
                    data_get($calculation->result, 'complexity_level'),
                    data_get($calculation->result, 'minimum_fee'),
                    data_get($calculation->result, 'recommended_fee'),
                    data_get($calculation->result, 'upper_reference_fee'),
                ], ';');
            }

            fclose($output);
        }, 'historico-honorarios-contabeis.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function deleteCalculation(Request $request, AccountingFeeCalculation $calculation): RedirectResponse
    {
        $this->ensureCalculationVisible($request, $calculation);
        $calculation->delete();

        return back()->with('success', 'Cálculo removido do histórico.');
    }

    public function adjustments(Request $request): View
    {
        [$userId, $sessionKey] = $this->owner($request);

        $adjustments = FeeAdjustment::query()
            ->visibleTo($userId, '')
            ->latest()
            ->paginate(12);

        return view('tools-calculadora-de-honorarios-contabeis::adjustments.index', compact('adjustments'));
    }

    public function calculateAdjustment(
        CalculateFeeAdjustmentRequest $request,
        CalculateFeeAdjustment $action,
    ): RedirectResponse {
        $data = $request->validated();

        try {
            $result = $action->execute(
                $this->moneyToCents((string) $data['current_value']),
                (float) $data['percentage'],
            );
        } catch (InvalidValue $exception) {
            throw ValidationException::withMessages(['percentage' => $exception->getMessage()]);
        }

        $saved = false;
        if ($request->user() !== null) {
            FeeAdjustment::query()->create([
            'user_id' => $request->user()->getAuthIdentifier(),
            'session_key' => null,
            'client_name' => trim($data['client_name']),
            'index_type' => $data['index_type'],
            'reference_period' => $data['reference_period'],
            'percentage' => $result->percentage,
            'current_value_cents' => $result->currentValueCents,
            'difference_cents' => $result->differenceCents,
            'adjusted_value_cents' => $result->adjustedValueCents,
            'notes' => filled($data['notes'] ?? null) ? trim((string) $data['notes']) : null,
            ]);
            $saved = true;
        }

        return redirect()->route('tools.calculadora-de-honorarios-contabeis.adjustments.index')
            ->with('success', $saved
                ? 'Reajuste calculado e salvo no seu histórico.'
                : 'Reajuste calculado. Crie uma conta gratuita para salvar este resultado.')
            ->with('adjustment_result', $result->toArray());
    }

    public function deleteAdjustment(Request $request, FeeAdjustment $adjustment): RedirectResponse
    {
        [$userId, $sessionKey] = $this->owner($request);

        abort_unless(
            ($userId !== null && (int) $adjustment->user_id === (int) $userId)
            || ($userId === null && $adjustment->user_id === null && hash_equals((string) $adjustment->session_key, $sessionKey)),
            404,
        );

        $adjustment->delete();

        return back()->with('success', 'Reajuste removido do histórico.');
    }

    public function crm(Request $request): View
    {
        [$userId, $sessionKey] = $this->owner($request);
        $search = trim((string) $request->query('search'));
        $status = (string) $request->query('status');

        $clients = AccountingClient::query()
            ->visibleTo($userId, '')
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('company_name', 'like', "%{$search}%")
                        ->orWhere('document', 'like', "%{$search}%")
                        ->orWhere('contact_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, ['prospect', 'negotiation', 'client', 'inactive'], true), fn ($query) => $query->where('pipeline_status', $status))
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString();

        $summary = AccountingClient::query()
            ->visibleTo($userId, '')
            ->selectRaw('pipeline_status, COUNT(*) as total')
            ->groupBy('pipeline_status')
            ->pluck('total', 'pipeline_status');

        return view('tools-calculadora-de-honorarios-contabeis::crm.index', compact('clients', 'summary', 'search', 'status'));
    }

    public function createClient(): View
    {
        return view('tools-calculadora-de-honorarios-contabeis::crm.form', [
            'client' => new AccountingClient(),
        ]);
    }

    public function storeClient(StoreAccountingClientRequest $request): RedirectResponse
    {
        $data = $request->validated();

        AccountingClient::query()->create([
            ...$this->clientPayload($data),
            'user_id' => $userId,
            'session_key' => $userId === null ? $sessionKey : null,
        ]);

        return redirect()->route('tools.calculadora-de-honorarios-contabeis.crm.index')
            ->with('success', 'Cliente adicionado ao CRM.');
    }

    public function editClient(Request $request, AccountingClient $client): View
    {
        $this->ensureVisible($request, $client);

        return view('tools-calculadora-de-honorarios-contabeis::crm.form', compact('client'));
    }

    public function updateClient(UpdateAccountingClientRequest $request, AccountingClient $client): RedirectResponse
    {
        $this->ensureVisible($request, $client);
        $client->update($this->clientPayload($request->validated()));

        return redirect()->route('tools.calculadora-de-honorarios-contabeis.crm.index')
            ->with('success', 'Cadastro atualizado com sucesso.');
    }

    public function deleteClient(Request $request, AccountingClient $client): RedirectResponse
    {
        $this->ensureVisible($request, $client);
        $client->delete();

        return redirect()->route('tools.calculadora-de-honorarios-contabeis.crm.index')
            ->with('success', 'Cliente removido do CRM.');
    }

    private function owner(Request $request): array
    {
        $sessionKey = (string) $request->session()->get('accounting_fees_crm_key');

        if ($sessionKey === '') {
            $sessionKey = (string) Str::uuid();
            $request->session()->put('accounting_fees_crm_key', $sessionKey);
        }

        return [$request->user()?->getAuthIdentifier(), $sessionKey];
    }

    private function ensureVisible(Request $request, AccountingClient $client): void
    {
        [$userId, $sessionKey] = $this->owner($request);

        abort_unless(
            ($userId !== null && (int) $client->user_id === (int) $userId)
            || ($userId === null && $client->user_id === null && hash_equals((string) $client->session_key, $sessionKey)),
            404,
        );
    }


    private function ensureCalculationVisible(Request $request, AccountingFeeCalculation $calculation): void
    {
        [$userId, $sessionKey] = $this->owner($request);

        abort_unless(
            ($userId !== null && (int) $calculation->user_id === (int) $userId)
            || ($userId === null && $calculation->user_id === null && hash_equals((string) $calculation->session_key, $sessionKey)),
            404,
        );
    }

    private function clientPayload(array $data): array
    {
        return [
            'company_name' => trim($data['company_name']),
            'document' => filled($data['document'] ?? null) ? trim((string) $data['document']) : null,
            'contact_name' => trim($data['contact_name']),
            'email' => filled($data['email'] ?? null) ? mb_strtolower(trim((string) $data['email'])) : null,
            'phone' => filled($data['phone'] ?? null) ? trim((string) $data['phone']) : null,
            'monthly_fee_cents' => $this->moneyToCents((string) $data['monthly_fee']),
            'proposal_status' => $data['proposal_status'],
            'contract_status' => $data['contract_status'],
            'pipeline_status' => $data['pipeline_status'],
            'notes' => filled($data['notes'] ?? null) ? trim((string) $data['notes']) : null,
        ];
    }

    private function moneyToCents(string $value): int
    {
        $normalized = str_replace(['R$', ' ', '.'], '', $value);
        $normalized = str_replace(',', '.', $normalized);

        return (int) round(((float) $normalized) * 100);
    }
}
