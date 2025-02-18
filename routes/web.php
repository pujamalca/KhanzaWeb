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

Route::get('/pegawai/photo/{filename}', function ($filename, Request $request) {
    $pegawai = Pegawai::where('photo', $filename)->firstOrFail();

    // Opsional: Pastikan user login sebelum bisa melihat foto
    if (!Auth::check()) {
        abort(403, 'Anda tidak diizinkan mengakses file ini.');
    }

    $path = storage_path('app/pages/pegawai/photo/' . $filename);

    if (!Storage::disk('private')->exists('pages/pegawai/photo/' . $filename)) {
        abort(404);
    }

    return Response::file($path);
})->name('pegawai.photo');


Route::post('/test-upload', function (Request $request) {
    $file = $request->file('photo');

    if (!$file) {
        Log::error("❌ File tidak ditemukan dalam request.");
        return response()->json(['error' => 'File tidak ditemukan.'], 400);
    }

    $path = $file->store('livewire-tmp', 'local');

    Log::info("🚀 File upload berhasil: " . $path);
    return response()->json(['path' => $path]);
});

