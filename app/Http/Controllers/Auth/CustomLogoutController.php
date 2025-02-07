<?php

namespace App\Http\Controllers\Auth;

use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LogoutResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomLogoutController
{
    public function __invoke(): LogoutResponse
    {
        $user = Filament::auth()->user();

        if ($user) {
            Log::info("🔍 User ID: {$user->id} sedang logout");

            // Paksa update last_session_id ke NULL
            DB::table('users')->where('id', $user->id)->update(['last_session_id' => null]);

            Log::info("✅ last_session_id berhasil dihapus dari database.");
        } else {
            Log::error("❌ Tidak ada user yang ditemukan saat logout.");
        }

        Filament::auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        Log::info("🎯 Logout berhasil untuk User ID: {$user->id}");

        return app(LogoutResponse::class);
    }
}
