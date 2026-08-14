<?php

declare(strict_types=1);

namespace App\Tools\SefazFiscalValidator\Presentation\Requests; use Illuminate\Foundation\Http\FormRequest; final class ExecuteToolRequest extends FormRequest { public function authorize(): bool{return true;} public function rules(): array{return ['access_key'=>['required','string','max:80']];}}
