<?php

declare(strict_types=1);
namespace App\Tools\BusinessDocumentValidator\Presentation\Requests;
use Illuminate\Foundation\Http\FormRequest;
final class ValidateBusinessDocumentRequest extends FormRequest
{
    public function authorize():bool{return true;}
    public function rules():array{return ['document_number'=>['required','string','max:20','regex:/^[0-9\-\s]+$/']];}
    public function messages():array{return ['document_number.required'=>'Enter an EIN to validate.','document_number.regex'=>'Use only digits, spaces, or a hyphen.'];}
}
