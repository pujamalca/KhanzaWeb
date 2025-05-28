<?php

namespace App\Listeners;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Illuminate\Database\Events\QueryExecuted;

class LogDatabaseQuery
{
    /**
     * Handle the event.
     */
    public function handle(QueryExecuted $event): void
    {
        // Ambil SQL query dalam huruf kecil
        $query = strtolower($event->sql);

        // Gantikan placeholder ? dengan data yang sebenarnya
        $bindings = $event->bindings;
        
        foreach ($bindings as $binding) {
            // Escape karakter khusus pada binding
            $binding = is_string($binding) ? "'$binding'" : $binding;

            // Gantikan tanda ? dengan binding yang sesuai
            if (!is_null($binding)) {
                // Secara berurutan mengganti placeholder ? dengan nilai yang sesuai
                $query = preg_replace('/\?/', $binding, $query, 1);
            }
        }

        // Cek apakah query adalah `INSERT`, `UPDATE`, atau `DELETE`
        if ($this->isWriteQuery($query) && !$this->isIgnoredQuery($query)) {
            // Ambil username dari user yang sedang login
            $usere = Auth::user() ? Auth::user()->username : 'guest'; // Jika tidak ada user yang login, set 'guest'

            // Ambil IP address dari request
            $ipAddress = Request::ip(); // Mendapatkan IP address

            // Inisialisasi awal
$sqle = $ipAddress . ' ' . strtoupper($event->connectionName) . ' ' . $query;

// Tangani DELETE jika ada
if (Str::contains($query, 'delete')) {
    preg_match('/delete from `(\w+)` where (.*)/', $query, $matches);
    $table = $matches[1] ?? '';
    $condition = $matches[2] ?? '';

    if ($table && $condition) {
        try {
            $deletedData = DB::table($table)->whereRaw($condition)->get();

            $deletedDataStr = $deletedData->isEmpty()
                ? 'No data deleted'
                : $deletedData->toJson();
        } catch (\Exception $e) {
            $deletedDataStr = 'Error fetching data: ' . $e->getMessage();
        }

        // Tambahkan info data yang dihapus ke $sqle
        $sqle .= ' | Data Deleted: ' . $deletedDataStr;
    }
}


            // Catat ke tabel trackersql
            DB::table('trackersql')->insert([
                'tanggal' => now(),
                'usere' => $usere, // Menggunakan 'usere' bukan 'username'
                'sqle' => $sqle, // Menyimpan IP address dan query lengkap di kolom 'sqle'
            ]);
        }
    }

    // Periksa apakah query adalah `INSERT`, `UPDATE`, atau `DELETE`.
    protected function isWriteQuery(string $query): bool
    {
        return Str::contains($query, ['insert', 'update', 'delete']);
    }

    // Periksa apakah query menuju tabel yang diabaikan (seperti trackersql, sessions, dll).
    protected function isIgnoredQuery(string $query): bool
    {
        // Daftar tabel yang harus diabaikan
        $ignoredTables = ['trackersql', 'sessions', 'cache', 'jobs'];

        foreach ($ignoredTables as $table) {
            if (Str::contains($query, $table)) {
                return true;
            }
        }

        return false;
    }
}
