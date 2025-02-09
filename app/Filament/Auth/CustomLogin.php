<?php

namespace App\Filament\Auth;

use Filament\Pages\Auth\Login;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Models\User;

class CustomLogin extends Login
{
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            return null;
        }

        $data = $this->form->getState();

        if (!Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        // Cek apakah user dapat mengakses panel Filament
        if ($user instanceof FilamentUser && !$user->canAccessPanel(Filament::getCurrentPanel())) {
            Filament::auth()->logout();
            $this->throwFailureValidationException();
        }

        // Cek apakah user aktif
        if ($user->is_active === false) {
            Filament::auth()->logout();
            throw ValidationException::withMessages([
                'data.login' => 'Akun tidak aktif, hubungi Administrator!',
            ]);
        }

        // Cek apakah pengguna sudah login di perangkat lain
        if ($user->last_session_id && $user->last_session_id !== Session::getId()) {
            Log::warning("User ID: {$user->id} mencoba login di perangkat lain.");

            // Kirim notifikasi ke user
            if ($user instanceof \App\Models\User) {
                $user->notify(new \App\Notifications\MultipleLoginAlert());
            } else {
                Log::error("Error: User is not an instance of App\Models\User");
            }

            Filament::auth()->logout();
            session()->invalidate();

            throw ValidationException::withMessages([
                'data.login' => __('Akun Anda telah login di perangkat lain!'),
            ]);
        }

        // Simpan session_id baru agar lebih aman
            if ($user instanceof User) {
                $user->update(['last_session_id' => Session::getId()]);
            } else {
                Log::error("❌ User bukan instance dari User model: " . get_class($user));
            }
        // Regenerasi session agar lebih aman
        session()->regenerate();

        // Kirim notifikasi login sukses
        Notification::make()
            ->title('Login Berhasil')
            ->body('Anda berhasil masuk ke sistem.')
            ->success()
            ->send();

        return app(LoginResponse::class);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getLoginFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getRememberFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label(__('NIK / Email'))
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login_type = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $login_type => $data['login'],
            'password' => $data['password'],
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    public function logout()
    {
        $user = Filament::auth()->user();

        if ($user instanceof User) {
            Log::info("🚀 LOGOUT DIPANGGIL untuk User ID: {$user->id}");

            // Paksa update last_session_id ke NULL
            $user->update(['last_session_id' => null]);

            Log::info("✅ Query Update Logout berhasil.");

            Filament::auth()->logout();
            session()->invalidate();
            session()->regenerateToken();

            Log::info("🎯 Logout berhasil.");
        } else {
            Log::error("❌ Tidak ada user yang login atau user bukan instance dari User model.");
        }
    }
}
