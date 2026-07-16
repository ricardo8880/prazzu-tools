<?php

namespace App\Http\Controllers\Auth;

use App\Core\Identity\Notifications\PasswordChanged;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

final class PasswordController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = $request->user();
        $user->forceFill(['password' => Hash::make($validated['password'])])->save();
        $user->notify(new PasswordChanged);

        return back()->with('status', 'Sua senha foi alterada com segurança.');
    }
}
