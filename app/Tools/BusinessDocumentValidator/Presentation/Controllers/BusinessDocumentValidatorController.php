<?php

declare(strict_types=1);
namespace App\Tools\BusinessDocumentValidator\Presentation\Controllers;
use App\Http\Controllers\Controller;
use App\Tools\BusinessDocumentValidator\Application\Actions\ValidateBusinessDocument;
use App\Tools\BusinessDocumentValidator\Presentation\Requests\ValidateBusinessDocumentRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
final class BusinessDocumentValidatorController extends Controller
{
    public function index(Request $request):View{return view('tools-us-tax-id-validator::index');}
    public function validateDocument(ValidateBusinessDocumentRequest $request,ValidateBusinessDocument $action):View
    {
        $request->flash();
        return view('tools-us-tax-id-validator::index',['validationResult'=>$action->execute($request->validated())->toArray()]);
    }
}
