<?php

declare(strict_types=1);

namespace App\Tools\AccountingFeesCalculator\Presentation\Requests;

use App\Tools\AccountingFeesCalculator\Domain\Enums\AccountingService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class GenerateCommercialProposalRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'client_company'=>['required','string','max:150'],'client_document'=>['nullable','string','max:30'],'contact_name'=>['required','string','max:120'],'accounting_firm'=>['required','string','max:150'],
            'monthly_fee'=>['required','us_money','money_min:0.01'],'setup_fee'=>['nullable','us_money','money_min:0'],'due_day'=>['required','integer','between:1,28'],'validity_days'=>['required','integer',Rule::in([7,15,30,45,60])],
            'services'=>['required','array','min:1'],'services.*'=>['required',Rule::enum(AccountingService::class)],'notes'=>['nullable','string','max:2000'],
        ];
    }
    public function messages(): array { return ['services.required'=>'Select at least one service for the proposal.','services.min'=>'Select at least one service for the proposal.']; }
}
