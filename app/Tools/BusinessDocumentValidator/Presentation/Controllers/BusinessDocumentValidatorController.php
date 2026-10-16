<?php

declare(strict_types=1);

namespace App\Tools\BusinessDocumentValidator\Presentation\Controllers;

use App\Http\Controllers\Controller;
use App\Tools\BusinessDocumentValidator\Application\Actions\ValidateBusinessDocument;
use App\Tools\BusinessDocumentValidator\Domain\Validators\BusinessDocumentValidator;
use App\Tools\BusinessDocumentValidator\Presentation\Requests\ValidateBusinessDocumentRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class BusinessDocumentValidatorController extends Controller
{
    public function index(Request $request): View { return view('tools-us-tax-id-validator::index'); }

    public function validateDocument(ValidateBusinessDocumentRequest $request, ValidateBusinessDocument $action): View
    {
        $request->flash();
        return view('tools-us-tax-id-validator::index', ['validationResult' => $action->execute($request->validated())->toArray()]);
    }

    public function validateBatch(Request $request, BusinessDocumentValidator $validator): View
    {
        // Plus feature: batch_validation.
        $data = $request->validate(['ein_list' => ['required', 'string', 'max:10000']]);
        $values = preg_split('/[\r\n,;]+/', (string) $data['ein_list']) ?: [];
        $rows = [];
        foreach (array_slice(array_values(array_filter(array_map('trim', $values))), 0, 200) as $value) {
            $rows[] = $validator->validate($value)->toArray();
        }

        return view('tools-us-tax-id-validator::index', ['batchResults' => $rows]);
    }
}
