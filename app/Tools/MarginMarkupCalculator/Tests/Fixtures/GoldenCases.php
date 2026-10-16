<?php

declare(strict_types=1);

namespace App\Tools\MarginMarkupCalculator\Tests\Fixtures;

use App\Core\Quality\Data\GoldenCase;
use App\Core\Quality\Data\GoldenCaseSuite;
use App\Core\Quality\Enums\GoldenCaseKind;

final class GoldenCases
{
    public const PLACEHOLDER_REFERENCE = 'TODO: replace with an official source, reviewed calculation, or approved case.';

    public static function suite(): GoldenCaseSuite
    {
        $reference = 'MarginMarkupCalculatorTest protects the formula, cost composition, domain rejections, and calculation details for rule 2.1.0.';

        return new GoldenCaseSuite(
            toolSlug: 'margin-markup-calculator',
            cases: [
                new GoldenCase(
                    identifier: 'typical',
                    title: '$120 total cost with selling-cost percentages produces a $200 price',
                    kind: GoldenCaseKind::Typical,
                    input: ['base_cost' => '100.00', 'additional_costs' => '10.00', 'freight_cost' => '5.00', 'packaging_cost' => '3.00', 'fixed_expenses' => '2.00', 'desired_margin' => '20', 'taxes' => '10', 'commission' => '5', 'card_fees' => '2', 'marketplace_fees' => '3'],
                    expected: ['total_cost' => '$120.00', 'sale_price' => '$200.00', 'net_profit' => '$40.00', 'markup_multiplier' => '1.6667'],
                    reference: $reference,
                    normativeRuleVersion: '2.1.0',
                    roundingPolicy: 'Monetary divisions use HalfUp; values are preserved in cents.',
                ),
                new GoldenCase(
                    identifier: 'boundary',
                    title: 'A combined percentage of one hundred percent has no valid denominator',
                    kind: GoldenCaseKind::Boundary,
                    input: ['base_cost' => '100.00', 'desired_margin' => '50', 'taxes' => '30', 'commission' => '20'],
                    expected: ['outcome' => 'domain-error', 'message' => 'The combined margin, taxes, commission, and fees must be below 100%.'],
                    reference: $reference,
                    normativeRuleVersion: '2.1.0',
                ),
                new GoldenCase(
                    identifier: 'invalid-input',
                    title: 'Zero total cost is rejected',
                    kind: GoldenCaseKind::InvalidInput,
                    input: ['base_cost' => '0.00', 'desired_margin' => '25'],
                    expected: ['outcome' => 'domain-error', 'message' => 'Total cost must be greater than zero.'],
                    reference: $reference,
                    normativeRuleVersion: '2.1.0',
                ),
                new GoldenCase(
                    identifier: 'rounding',
                    title: 'Price and markup are derived without floats in the domain',
                    kind: GoldenCaseKind::Rounding,
                    input: ['total_cost_minor' => 12000, 'sale_price_minor' => 20000],
                    expected: ['markup' => '66.666667', 'markup_multiplier' => '1.6667'],
                    reference: $reference,
                    normativeRuleVersion: '2.1.0',
                    roundingPolicy: 'Percentages use Percentage and monetary divisions use IntegerRounding/HalfUp.',
                ),
                new GoldenCase(
                    identifier: 'non-applicable',
                    title: 'Business fixed costs are not allocated automatically',
                    kind: GoldenCaseKind::NonApplicable,
                    input: ['scenario' => 'automatic-fixed-cost-allocation'],
                    expected: ['outcome' => 'explicitly-out-of-scope'],
                    reference: 'The module README requires fixed costs to be allocated per unit or sale beforehand.',
                    normativeRuleVersion: '2.1.0',
                ),
            ],
        );
    }
}
