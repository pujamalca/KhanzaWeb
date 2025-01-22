<?php

namespace App\Listeners;

use App\Filament\Auth\CustomLogin;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LogUserLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;

        // Log untuk memastikan event listener dipanggil
        Log::info('User Logged In:', ['user' => $user->id]);

        // Ambil username dan simpan sebagai nip di tabel tracker
        $nip = $user->username;  // Menggunakan username sebagai nip

        // Pastikan nip ada dan tidak null
        if ($nip) {
            // Simpan data login ke tabel tracker
            DB::table('tracker')->insert([
                'nip' => $nip, // Gunakan username sebagai nip
                'tgl_login' => now()->toDateString(), // Tanggal login
                'jam_login' => now()->toTimeString(), // Jam login
            ]);
        } else {
            Log::warning('User tidak memiliki username yang valid.', ['user' => $user->id]);
        }
    }
}
