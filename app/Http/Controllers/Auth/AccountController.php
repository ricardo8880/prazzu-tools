<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Core\Tools\History\Application\Queries\UserToolContinuityQuery;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class AccountController extends Controller
{
    public function __invoke(Request $request, UserToolContinuityQuery $continuity): View
    {
        $user = $request->user();
        $summary = $continuity->summary((int) $user->getAuthIdentifier());

        $memberships = $user->organizationMemberships()
            ->with('organization')
            ->where('status', 'active')
            ->get();

        return view('account.show', [
            ...$summary,
            'memberships' => $memberships,
        ]);
    }
}
