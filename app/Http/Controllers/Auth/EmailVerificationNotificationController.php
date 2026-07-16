<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class EmailVerificationNotificationController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('account.show');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'Enviamos um novo link de confirmação para seu e-mail.');
    }
}
