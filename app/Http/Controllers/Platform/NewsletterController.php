<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Http\Requests\Platform\NewsletterRequest;
use Illuminate\Http\RedirectResponse;

final class NewsletterController extends Controller
{
    public function store(NewsletterRequest $request): RedirectResponse
    {
        $request->validated();

        return back()->with('status', 'Inscrição recebida. Você será avisado sobre as novidades da plataforma.');
    }
}
