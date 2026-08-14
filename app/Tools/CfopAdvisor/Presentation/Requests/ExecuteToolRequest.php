<?php

declare(strict_types=1);
namespace App\Tools\CfopAdvisor\Presentation\Requests;
use Illuminate\Foundation\Http\FormRequest;
final class ExecuteToolRequest extends FormRequest { public function authorize(): bool { return true; } public function rules(): array { return ['cfop'=>['required','regex:/^[1-7]\.?\d{3}$/']]; } public function messages(): array { return ['cfop.regex'=>'Informe um CFOP com quatro dígitos, por exemplo 5102 ou 5.102.']; } }
