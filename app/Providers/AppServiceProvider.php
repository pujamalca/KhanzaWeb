<?php

namespace App\Providers;

use App\Filament\Auth\CustomLogin;
use App\Listeners\LogUserLogin;
use Filament\Pages\Auth\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\Trackersql;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    protected $listen = [
        Login::class => [
            LogUserLogin::class, // Pastikan event Login terhubung dengan listener LogUserLogin
        ],
    ];

    public function boot(): void
    {

        // Mendaftarkan event dan listener di sini
        Event::listen(
            Login::class,
            LogUserLogin::class
        );
    }
}
