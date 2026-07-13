<?php

namespace App\Tools\MarginMarkupCalculator\Presentation\Controllers;

use App\Core\Access\Data\ToolAccessContext;
use App\Core\Access\Enums\AccountRole;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Access\Services\ToolExecutionAuthorizer;
use App\Core\Dates\ReferenceDate;
use App\Core\Tools\History\Contracts\ToolRunRecorder;
use App\Core\Tools\History\Data\RuleVersion;
use App\Core\Usage\Contracts\UsageMetrics;
use App\Core\Usage\Data\UsageLimit;
use App\Http\Controllers\Controller;
use App\Tools\MarginMarkupCalculator\Application\Actions\CalculateMarginMarkup;
use App\Tools\MarginMarkupCalculator\Domain\Calculators\MarginMarkupCalculator;
use App\Tools\MarginMarkupCalculator\Presentation\Requests\CalculateMarginMarkupRequest;
use App\Tools\MarginMarkupCalculator\Tool;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

final class MarginMarkupController extends Controller
{
    public function index()
    {
        return view('tools-calculadora-margem-markup::index');
    }

    public function calculate(CalculateMarginMarkupRequest $request, CalculateMarginMarkup $action, ToolExecutionAuthorizer $authorizer, ToolRunRecorder $recorder, UsageMetrics $metrics, Tool $module)
    {
        $user = $request->user();
        $context = new ToolAccessContext(
            userId: $user?->id,
            role: $user?->role ?? AccountRole::User,
            plan: $user?->subscription_plan ?? SubscriptionPlan::Free,
        );
        $subject = $user ? 'user:'.$user->id : 'ip:'.$request->ip();
        $decision = $authorizer->authorize($module->manifest(), $context, $subject, new UsageLimit(20, 3600));

        abort_unless($decision->allowed, 429, 'Limite de uso atingido ou acesso indisponível.');

        $input = $request->validated();
        $startedAt = hrtime(true);
        $run = null;

        try {
            $run = $recorder->start($module, new RuleVersion(MarginMarkupCalculator::RULE_VERSION), ReferenceDate::fromString($input['reference_date']), $input, $user?->id);
            $result = $action->execute($input);
            $recorder->succeed($run, $result->toArray());
            $metrics->record($module->manifest()->slug, 'calculated', $user?->id, durationMs: (int) ((hrtime(true) - $startedAt) / 1_000_000));

            return back()->withInput()->with('calculation_result', $result->toArray());
        } catch (Throwable $exception) {
            if ($run !== null) {
                $recorder->fail($run, 'calculation.failed');
            }
            throw $exception;
        }
    }

    public function export(Request $request, CalculateMarginMarkup $action): StreamedResponse
    {
        $validated = $request->validate([
            'reference_date' => ['required', 'date_format:Y-m-d'],
            'base_cost' => ['required', 'string'],
            'additional_costs' => ['nullable', 'string'],
            'desired_margin' => ['required', 'numeric', 'min:0', 'lt:100'],
        ]);
        $result = $action->execute($validated)->toArray();

        return response()->streamDownload(function () use ($validated, $result): void {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, ['Campo', 'Valor'], ';');
            foreach ([
                'Data de referência' => $validated['reference_date'],
                'Custo total' => $result['total_cost'],
                'Preço de venda' => $result['sale_price'],
                'Lucro' => $result['profit'],
                'Margem' => $result['margin'],
                'Markup' => $result['markup'],
                'Versão da regra' => $result['rule_version'],
            ] as $label => $value) {
                fputcsv($handle, [$label, $value], ';');
            }
            fclose($handle);
        }, 'margem-markup.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
