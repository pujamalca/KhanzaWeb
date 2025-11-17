<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\User;

class AutoLogout
{
    /**
     * Handle an incoming request.
     *
     * Note: Expensive database cleanup moved to scheduled command (sessions:cleanup-expired)
     * This middleware now only handles per-user session validation
     */
    public function handle(Request $request, Closure $next)
    {
        // 🔍 Jika user sedang login, cek apakah sesi masih valid
        if (Auth::check()) {
            $authUser = Auth::user();
            $user = User::find($authUser->id);

            if (!$user) {
                Log::error('❌ User tidak ditemukan atau bukan instance dari User model.');
                return redirect('/superadmin/login')->with('error', 'Terjadi kesalahan pada autentikasi.');
            }

            $sessionId = session()->getId();
            $lastActivity = session('last_activity');

            // 🔍 Cek apakah sesi user masih ada di database
            $sessionExists = DB::table('sessions')->where('id', $sessionId)->exists();

            // 🔥 Jika sesi tidak ada (karena expired oleh Laravel), hapus last_session_id
            if (!$sessionExists) {
                Log::info("⚠️ Session expired, menghapus last_session_id untuk User ID: {$user->id}");
                $user->update(['last_session_id' => null]);

                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                return redirect('/superadmin/login')->with('warning', 'Sesi Anda telah berakhir. Silakan login kembali.');
            }

            // 🔥 Jika user tidak aktif selama 30 menit, logout otomatis
            if ($lastActivity && Carbon::parse($lastActivity)->diffInMinutes(now()) > 30) {
                Log::info("⏳ Auto logout: User ID {$user->id} tidak aktif selama 30 menit.");

                $user->update(['last_session_id' => null]);

                Auth::logout();
                session()->invalidate();
                session()->regenerateToken();

                return redirect('/superadmin/login')->with('warning', 'Anda telah logout otomatis karena tidak aktif selama 30 menit.');
            }

            // 🔥 Simpan waktu aktivitas terakhir
            session(['last_activity' => now()]);

            // 🔥 Pastikan last_session_id diperbarui hanya jika session masih aktif
            if ($sessionExists) {
                $user->update(['last_session_id' => $sessionId]);
            }
        }

        return $next($request);
    }
}
