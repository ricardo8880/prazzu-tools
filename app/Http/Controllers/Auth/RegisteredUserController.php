<?php

namespace App\Http\Controllers\Auth;

use App\Core\Access\Enums\AccountRole;
use App\Core\Access\Enums\SubscriptionPlan;
use App\Core\Identity\Notifications\WelcomeToPrazzuTools;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::query()->create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => $request->string('password')->toString(),
            'role' => AccountRole::User,
            'subscription_plan' => SubscriptionPlan::Free,
        ]);

        event(new Registered($user));
        $user->notify(new WelcomeToPrazzuTools);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('account.show')
            ->with('status', 'Sua conta gratuita foi criada. Confirme seu e-mail para proteger os dados salvos.');
    }
}
