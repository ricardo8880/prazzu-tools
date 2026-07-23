<?php

declare(strict_types=1);

namespace App\Tools\TaxRegimeComparator\Api\Actions;

use App\Core\Money\Money;
use App\Core\Money\Percentage;
use App\Core\Tools\Api\Actions\UsesFormRequestRules;
use App\Core\Tools\Api\Contracts\ToolApiAction;
use App\Core\Tools\Api\Data\ToolExecutionContext;
use App\Tools\TaxRegimeComparator\Application\Actions\CompareTaxRegimes;
use App\Tools\TaxRegimeComparator\Application\Data\TaxComparisonInput;
use App\Tools\TaxRegimeComparator\Application\Presenters\TaxComparisonResultPresenter;
use App\Tools\TaxRegimeComparator\Domain\Enums\BusinessActivity;
use App\Tools\TaxRegimeComparator\Presentation\Requests\CompareTaxRegimesRequest;
use DateTimeImmutable;

final readonly class CompareApiAction implements ToolApiAction
{
    use UsesFormRequestRules;

    public function __construct(private CompareTaxRegimes $action, private TaxComparisonResultPresenter $presenter) {}

    public function name(): string { return 'compare'; }
    protected function requestClass(): string { return CompareTaxRegimesRequest::class; }

    public function execute(array $input, ToolExecutionContext $context): mixed
    {
        $dto = new TaxComparisonInput(
            new DateTimeImmutable((string) $input['reference_date']),
            BusinessActivity::from((string) $input['business_activity']),
            Money::fromDecimal((string) $input['monthly_revenue']),
            Money::fromDecimal((string) $input['revenue_last_twelve_months']),
            Money::fromDecimal((string) $input['payroll_last_twelve_months']),
            Money::fromDecimal((string) $input['monthly_operating_costs']),
            Money::fromDecimal((string) $input['monthly_deductible_expenses']),
            filled($input['monthly_pis_cofins_credit_base'] ?? null) ? Money::fromDecimal((string) $input['monthly_pis_cofins_credit_base']) : null,
            filled($input['indirect_tax_rate'] ?? null) ? Percentage::fromString((string) $input['indirect_tax_rate']) : null,
            $input['state'] ?? null,
            $input['municipality'] ?? null,
        );

        return $this->presenter->present($this->action->execute($dto));
    }
}
