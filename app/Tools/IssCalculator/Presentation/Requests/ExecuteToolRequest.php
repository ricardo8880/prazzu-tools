<?php

declare(strict_types=1);
namespace App\Tools\IssCalculator\Presentation\Requests;
use Illuminate\Foundation\Http\FormRequest;
final class ExecuteToolRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array { return [
        'competence'=>['required','date_format:Y-m'],'municipality'=>['required','string','max:120'],'service'=>['required','string','max:180'],'value'=>['required','brazilian_money','money_min:0.01'],'rate'=>['required','brazilian_percentage','percentage_min:0','percentage_max:100'],'taker'=>['nullable','string','max:160'],'retained'=>['nullable','boolean'],
        'services'=>['nullable','array','max:10'],'services.*.competence'=>['nullable','date_format:Y-m'],'services.*.municipality'=>['nullable','string','max:120'],'services.*.service'=>['nullable','string','max:180'],'services.*.taker'=>['nullable','string','max:160'],'services.*.value'=>['nullable','brazilian_money','money_min:0.01'],'services.*.rate'=>['nullable','brazilian_percentage','percentage_min:0','percentage_max:100'],'services.*.retained'=>['nullable','boolean'],
        'municipality_scenarios'=>['nullable','array','max:5'],'municipality_scenarios.*.municipality'=>['nullable','string','max:120'],'municipality_scenarios.*.rate'=>['nullable','brazilian_percentage','percentage_min:0','percentage_max:100'],
    ]; }
}
