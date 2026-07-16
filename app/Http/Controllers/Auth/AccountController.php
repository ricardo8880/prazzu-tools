<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class AccountController extends Controller
{
    public function __invoke(): View
    {
        return view('account.show');
    }
}
