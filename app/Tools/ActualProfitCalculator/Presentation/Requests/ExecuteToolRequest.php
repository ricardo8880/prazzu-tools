<?php

declare(strict_types=1);

namespace App\Tools\ActualProfitCalculator\Presentation\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ExecuteToolRequest extends FormRequest { public function authorize():bool{return true;} public function rules():array{return ['accounting_profit'=>['required','brazilian_money'],'additions'=>['nullable','brazilian_money','money_min:0'],'exclusions'=>['nullable','brazilian_money','money_min:0'],'irpj_loss_balance'=>['nullable','brazilian_money','money_min:0'],'csll_negative_balance'=>['nullable','brazilian_money','money_min:0'],'months'=>['required','integer','min:1','max:12'],'show_diagnostics'=>['nullable','boolean']];} }
