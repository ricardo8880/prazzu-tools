<?php

declare(strict_types=1);
namespace App\Tools\TaxReformSimulator\Presentation\Requests;
use Illuminate\Foundation\Http\FormRequest;
final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool{return true;}
    public function rules(): array{return [
        'price'=>['required','us_money','money_min:0'],
        'state_rate'=>['required','percentage_value','percentage_min:0','percentage_max:100'],
        'local_rate'=>['nullable','percentage_value','percentage_min:0','percentage_max:100'],
        'district_rate'=>['nullable','percentage_value','percentage_min:0','percentage_max:100'],
        'quantity'=>['required','integer','min:1','max:1000000'],
        'tax_included'=>['nullable','boolean'],
    ];}
}
