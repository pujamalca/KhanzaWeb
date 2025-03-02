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



Route::get('/berkas-pegawai/download/{record}/{filename}', function ($record, $filename) {
    $filePath = "pages/berkaspegawai/photo/$filename";

    if (!Storage::disk('pegawai')->exists($filePath)) {
        abort(404, 'File tidak ditemukan');
    }

    return response()->file(Storage::disk('pegawai')->path($filePath), [
        'Content-Disposition' => 'inline',
    ]);
})->name('filament.resources.berkas-pegawai.download');


