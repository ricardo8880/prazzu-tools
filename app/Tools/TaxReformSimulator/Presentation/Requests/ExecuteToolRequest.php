<?php

declare(strict_types=1);

namespace App\Tools\TaxReformSimulator\Presentation\Requests; use Illuminate\Foundation\Http\FormRequest; final class ExecuteToolRequest extends FormRequest { public function authorize():bool{return true;} public function rules():array{return ['revenue'=>['required','brazilian_money','money_min:0'],'legacy_federal_rate'=>['required','brazilian_percentage','percentage_min:0','percentage_max:100'],'legacy_subnational_rate'=>['required','brazilian_percentage','percentage_min:0','percentage_max:100'],'cbs_reference_rate'=>['required','brazilian_percentage','percentage_min:0','percentage_max:100'],'ibs_reference_rate'=>['required','brazilian_percentage','percentage_min:0','percentage_max:100'],'credit_base_percent'=>['required','brazilian_percentage','percentage_min:0','percentage_max:100'],'year'=>['required','integer','min:2026','max:2033'],'show_diagnostics'=>['nullable','boolean']];} }
