<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Core\Tools\Favorites\Services\UserToolFavorites;
use App\Core\Tools\History\Application\Queries\UserToolContinuityQuery;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountController extends Controller
{
    public function __invoke(Request $request, UserToolContinuityQuery $continuity, UserToolFavorites $toolFavorites): View
    {
        $user = $request->user();
        $userId = (int) $user->getAuthIdentifier();
        $summary = $continuity->summary($userId);
        $favoriteTools = $toolFavorites->forUser($userId);

        $memberships = $user->organizationMemberships()
            ->with('organization')
            ->where('status', 'active')
            ->get();

        return view('account.show', [
            ...$summary,
            'favoriteTools' => $favoriteTools,
            'toolFavoriteCount' => $favoriteTools->count(),
            'memberships' => $memberships,
        ]);
    }
}
