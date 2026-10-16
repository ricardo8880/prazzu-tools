@php
    $money = static fn (int $minor): string => \App\Core\Money\Money::fromMinor($minor)->formatEnUs();
    $details = $result->details;
@endphp

<h2>Take-home pay summary</h2>
<table class="table table-sm"><tbody>
@foreach ($result->summary as $item)<tr><th>{{ $item->label }}</th><td class="text-end">{{ $item->value }}</td></tr>@endforeach
</tbody></table>

<h2 class="h5 mt-4">Details</h2>
<table class="table table-sm"><tbody>
<tr><th>Taxable gross pay</th><td>{{ $money($details['taxable_gross_minor']) }}</td></tr>
<tr><th>Pre-tax deductions</th><td>{{ $money($details['pretax_minor']) }}</td></tr>
<tr><th>Estimated federal income tax</th><td>{{ $money($details['federal_income_tax_minor']) }}</td></tr>
<tr><th>Social Security</th><td>{{ $money($details['social_security_minor']) }}</td></tr>
<tr><th>Medicare</th><td>{{ $money($details['medicare_minor']) }}</td></tr>
<tr><th>Additional Medicare withholding</th><td>{{ $money($details['additional_medicare_minor']) }}</td></tr>
<tr><th>State/local withholding</th><td>{{ $money($details['state_local_minor']) }}</td></tr>
<tr><th>Other entered deductions</th><td>{{ $money($details['user_discounts_minor']) }}</td></tr>
<tr><th>Total deductions</th><td>{{ $money($details['deductions_minor']) }}</td></tr>
<tr><th>Estimated take-home pay</th><td><strong>{{ $money($details['net_minor']) }}</strong></td></tr>
</tbody></table>
<p class="small text-muted">Planning estimate using 2026 federal assumptions. Actual payroll withholding can differ based on Form W-4 entries, benefits, year-to-date wages, and state/local law.</p>
