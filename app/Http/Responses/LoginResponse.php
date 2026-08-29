<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = Auth::user();

        $redirect = match ($user->role) {
            'superadmin', 'manager' => '/owner/dashboard',
            'agent' => '/agent/dashboard',
            'buyer' => '/buyer/portal',
            default => '/dashboard',
        };

        return redirect()->intended($redirect);
    }
}