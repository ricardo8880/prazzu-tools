<?php

namespace App\Http\Controllers\Auth;

use App\Core\Tools\Favorites\Services\UserToolFavorites;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request, UserToolFavorites $favorites): RedirectResponse
    {
        $credentials = $request->safe()->only(['email', 'password']);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Não foi possível entrar com os dados informados.',
            ]);
        }

        $request->session()->regenerate();

        $favoriteAdded = false;
        if ($request->query('source') === 'tool_favorite') {
            $tool = trim((string) $request->query('tool', ''));
            if ($tool !== '') {
                try {
                    $favoriteAdded = $favorites->favorite($tool, (int) $request->user()->getAuthIdentifier());
                } catch (NotFoundHttpException) {
                    // A intenção de retorno nunca deve transformar autenticação válida em erro.
                }
            }
        }

        return redirect()->intended(route('account.show'))
            ->with('status', $favoriteAdded ? 'Você entrou na sua conta e a ferramenta foi adicionada aos favoritos.' : 'Você entrou na sua conta.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('status', 'Você saiu da sua conta com segurança.');
    }
}
