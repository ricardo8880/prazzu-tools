<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureAuthenticatedForPersistence
{
    public function handle(Request $request, Closure $next): Response|RedirectResponse
    {
        if ($request->user() !== null) {
            return $next($request);
        }

        return redirect()
            ->route('login')
            ->with('auth_notice', 'Crie ou acesse sua conta gratuita para salvar e recuperar seus resultados. As ferramentas continuam disponíveis sem login.');
    }
}
