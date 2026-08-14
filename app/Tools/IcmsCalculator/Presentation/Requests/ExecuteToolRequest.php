<?php

declare(strict_types=1);

namespace App\Tools\IcmsCalculator\Presentation\Requests; use Illuminate\Foundation\Http\FormRequest; final class ExecuteToolRequest extends FormRequest { public function authorize():bool{return true;} public function rules():array{return ['value'=>['required','brazilian_money','money_min:0.01'],'rate'=>['required','brazilian_percentage','percentage_min:0','percentage_max:100'],'reduction'=>['nullable','brazilian_percentage','percentage_min:0','percentage_max:100'],'value_excludes_icms'=>['nullable','boolean']];}}
