<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;

class AutoLogout
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $lastActivity = session('last_activity');

            // Jika lebih dari 30 menit tidak aktif, logout
            if ($lastActivity && Carbon::parse($lastActivity)->diffInMinutes(now()) > 30) {
                // Set last_session_id menjadi null sebelum logout
                if ($user instanceof User) {
                    $user->update(['last_session_id' => null]);
                }

                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                return redirect('/superadmin/login')->with('warning', 'Anda telah logout otomatis karena tidak aktif selama 30 menit.');
            }

            // Update waktu aktivitas terakhir
            session(['last_activity' => now()]);
        }

        return $next($request);
    }
}
