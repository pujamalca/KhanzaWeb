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

Route::get('/berkas-pegawai/{filename}', function ($filename) {
    $filePath = "pages/berkaspegawai/photo/{$filename}";

    if (!Storage::disk('pegawai')->exists($filePath)) {
        abort(404, 'File tidak ditemukan.');
    }

    return response()->file(storage_path("app/pages/pegawai/photo/{$filename}"));
})->name('pegawai.berkas')->middleware('auth'); // Tambahkan middleware jika hanya user tertentu yang boleh akses

