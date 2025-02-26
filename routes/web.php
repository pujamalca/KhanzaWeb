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
        return response()->json(['error' => 'File tidak ditemukan.'], 404);
    }

    return response()->file(Storage::disk('pegawai')->path($filePath));
})->name('pegawai.berkas')->middleware('auth');


