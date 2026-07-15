<?php
namespace App\Http\Requests\Analytics;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
final class TrackToolEventRequest extends FormRequest { public function authorize(): bool{return true;} public function rules(): array{return ['tool'=>['required','string','max:120'],'event'=>['required',Rule::in(['tool.calculation_started','tool.time_spent'])],'seconds'=>['nullable','integer','min:0','max:86400']];} }
