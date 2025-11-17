<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

Route::get('/', function () {
    return view('welcome');
});



Route::middleware(['auth'])->group(function () {
    Route::get('/berkas-pegawai/download/{record}/{filename}', function ($record, $filename) {
        // Security: Check if user is authenticated
        if (!Auth::check()) {
            abort(403, 'Unauthorized');
        }

        // Security: Validate record ID
        if (!is_numeric($record)) {
            abort(400, 'Invalid record ID');
        }

        // Security: Sanitize filename to prevent directory traversal
        $filename = basename($filename);
        if (str_contains($filename, '..') || str_contains($filename, '/') || str_contains($filename, '\\')) {
            abort(400, 'Invalid filename');
        }

        // Get the berkas_pegawai record
        $berkasPegawai = \App\Models\berkas_pegawai::find($record);
        if (!$berkasPegawai) {
            abort(404, 'Record tidak ditemukan');
        }

        // Authorization: Check if user can view this file
        $user = Auth::user();
        if (!$user->can('view_berkas::pegawai')) {
            // If user doesn't have general permission, check if it's their own file
            if ($berkasPegawai->nik !== $user->username) {
                abort(403, 'You do not have permission to access this file');
            }
        }

        $filePath = "pages/berkaspegawai/photo/$filename";

        if (!Storage::disk('pegawai')->exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        // Log file access for audit trail
        Log::info("File accessed: {$filename} by user {$user->username}");

        return response()->file(Storage::disk('pegawai')->path($filePath), [
            'Content-Disposition' => 'inline',
        ]);
    })
    ->middleware(['throttle:10,1']) // Rate limit: 10 requests per minute
    ->name('filament.resources.berkas-pegawai.download');
});


