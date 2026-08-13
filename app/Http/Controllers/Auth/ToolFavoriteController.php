<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Core\Tools\Favorites\Services\UserToolFavorites;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class ToolFavoriteController extends Controller
{
    public function __invoke(Request $request, string $tool, UserToolFavorites $favorites): RedirectResponse
    {
        $favorited = $favorites->toggle($tool, (int) $request->user()->getAuthIdentifier());

        return back()->with(
            'tool_favorite_status',
            $favorited ? 'Ferramenta adicionada aos seus favoritos.' : 'Ferramenta removida dos seus favoritos.',
        );
    }
}
