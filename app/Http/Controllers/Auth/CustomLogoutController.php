<?php

namespace App\Http\Controllers\Auth;

use Filament\Http\Controllers\Auth\LogoutController as BaseLogoutController;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class CustomLogoutController extends BaseLogoutController
{
    public function __invoke(): LogoutResponse
    {
        $user = Auth::user();
        if ($user instanceof User) {
            $user->update(['last_session_id' => null]);
        }


        return parent::__invoke();
    }
}
