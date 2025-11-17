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
        $sql = strtolower($event->sql);

        // Cek apakah query adalah `INSERT`, `UPDATE`, atau `DELETE`
        if ($this->isWriteQuery($sql) && !$this->isIgnoredQuery($sql)) {
            // Ambil username dari user yang sedang login
            $username = Auth::user() ? Auth::user()->username : 'guest';

            // Ambil IP address dari request
            $ipAddress = Request::ip();

            // Build query string dengan SAFE method - gunakan json_encode untuk bindings
            $sqlWithBindings = $this->buildSafeQueryString($event->sql, $event->bindings);

            // Format log string
            $logString = sprintf(
                '%s %s %s',
                $ipAddress,
                strtoupper($event->connectionName),
                $sqlWithBindings
            );

            // Handle DELETE query - capture deleted data SAFELY
            if (Str::contains($sql, 'delete')) {
                $deletedDataInfo = $this->captureDeletedDataSafely($event->sql, $event->bindings);
                if ($deletedDataInfo) {
                    $logString .= ' | Data Deleted: ' . $deletedDataInfo;
                }
            }

            // Catat ke tabel trackersql
            DB::table('trackersql')->insert([
                'tanggal' => now(),
                'usere' => $username,
                'sqle' => $logString,
            ]);
        }
    }

    /**
     * Build safe query string dengan proper escaping
     */
    protected function buildSafeQueryString(string $sql, array $bindings): string
    {
        // Use vsprintf with proper escaping
        $sql = str_replace('?', '%s', $sql);

        $escapedBindings = array_map(function ($binding) {
            if (is_null($binding)) {
                return 'NULL';
            }
            if (is_bool($binding)) {
                return $binding ? '1' : '0';
            }
            if (is_numeric($binding)) {
                return $binding;
            }
            // Use DB::connection()->getPdo()->quote() for safe string escaping
            try {
                return DB::connection()->getPdo()->quote($binding);
            } catch (\Exception $e) {
                // Fallback to json_encode jika quote gagal
                return json_encode($binding);
            }
        }, $bindings);

        try {
            return vsprintf($sql, $escapedBindings);
        } catch (\Exception $e) {
            // Fallback: return original SQL with JSON bindings
            return $sql . ' | Bindings: ' . json_encode($bindings);
        }
    }

    /**
     * Capture deleted data dengan SAFE method - TIDAK menggunakan whereRaw
     */
    protected function captureDeletedDataSafely(string $sql, array $bindings): ?string
    {
        // Parse DELETE query dengan regex
        if (!preg_match('/delete\s+from\s+`?(\w+)`?\s+where\s+(.*)/i', $sql, $matches)) {
            return null;
        }

        $table = $matches[1] ?? '';
        $whereClause = $matches[2] ?? '';

        if (empty($table)) {
            return null;
        }

        try {
            // IMPORTANT: Jangan gunakan whereRaw dengan user input!
            // Instead, kita cukup log jumlah rows yang akan dihapus

            // Rebuild WHERE conditions dari original query dengan bindings
            // This is complex and risky, jadi kita simplify: log count only
            $query = DB::table($table);

            // Parse simple WHERE conditions (untuk basic cases only)
            // Untuk production, consider menggunakan SQL parser library
            if (preg_match('/`?(\w+)`?\s*=\s*\?/', $whereClause, $whereMatch)) {
                $column = $whereMatch[1];
                if (!empty($bindings)) {
                    $value = $bindings[0];
                    $count = $query->where($column, $value)->count();
                    return "Approximately {$count} row(s) affected";
                }
            }

            // Fallback: return generic message
            return 'Row(s) deleted from ' . $table;

        } catch (\Exception $e) {
            return 'Error capturing deleted data: ' . $e->getMessage();
        }
    }

    /**
     * Periksa apakah query adalah `INSERT`, `UPDATE`, atau `DELETE`.
     */
    protected function isWriteQuery(string $query): bool
    {
        return Str::contains($query, ['insert', 'update', 'delete']);
    }

    /**
     * Periksa apakah query menuju tabel yang diabaikan (seperti trackersql, sessions, dll).
     */
    protected function isIgnoredQuery(string $query): bool
    {
        // Daftar tabel yang harus diabaikan
        $ignoredTables = ['trackersql', 'sessions', 'cache', 'jobs', 'failed_jobs'];

        foreach ($ignoredTables as $table) {
            if (Str::contains($query, $table)) {
                return true;
            }
        }

        return false;
    }
}
