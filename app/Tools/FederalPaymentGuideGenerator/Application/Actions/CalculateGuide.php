<?php

declare(strict_types=1);

namespace App\Tools\FederalPaymentGuideGenerator\Application\Actions;

use App\Core\Money\Money;
use App\Tools\FederalPaymentGuideGenerator\Domain\Data\LatePaymentInput;
use App\Tools\FederalPaymentGuideGenerator\Domain\Enums\GuideType;
use App\Tools\FederalPaymentGuideGenerator\Domain\Services\LatePaymentCalculator;
use App\Tools\FederalPaymentGuideGenerator\Domain\Services\RevenueCodeCatalog;
use DateTimeImmutable;
use InvalidArgumentException;

final readonly class CalculateGuide
{
    public function __construct(
        private LatePaymentCalculator $calculator,
        private RevenueCodeCatalog $catalog,
    ) {}

    /** @param array<string,mixed> $data @return array<string,mixed> */
    public function execute(array $data): array
    {
        $type = GuideType::from((string) $data['guide_type']);
        $code = $this->catalog->find($type, (string) $data['revenue_code']);

        if ($code === null) {
            throw new InvalidArgumentException('O código selecionado não pertence ao tipo de guia informado.');
        }

        $result = $this->calculator->calculate(new LatePaymentInput(
            principal: Money::fromDecimal((string) $data['principal']),
            dueDate: new DateTimeImmutable((string) $data['due_date']),
            paymentDate: new DateTimeImmutable((string) $data['payment_date']),
            selicAccumulatedPercent: (string) ($data['selic_accumulated_percent'] ?? '0'),
        ));

        return [
            'guide' => [
                'type' => strtoupper($type->value),
                'code' => $code->code,
                'description' => $code->description,
                'periodicity' => $code->periodicity,
                'official_reference' => $code->officialReference,
                'requires_confirmation' => $code->requiresProfessionalConfirmation,
            ],
            'dates' => [
                'due_date' => (string) $data['due_date'],
                'payment_date' => (string) $data['payment_date'],
            ],
            'amounts' => [
                'principal' => $result->principal->formatPtBr(),
                'penalty' => $result->penalty->formatPtBr(),
                'interest' => $result->interest->formatPtBr(),
                'total' => $result->total->formatPtBr(),
            ],
            'calculation' => [
                'calendar_days_late' => $result->calendarDaysLate,
                'penalty_percent' => $result->penaltyPercent,
                'interest_percent' => $result->interestPercent,
            ],
            'normative_rule' => $result->normativeRule,
            'warnings' => array_values(array_unique([
                ...$result->warnings,
                'Confirme o código, o período de apuração e o vencimento no sistema oficial antes de pagar.',
                'A ferramenta não transmite nem emite documento oficial de arrecadação.',
            ])),
        ];
    }
}
