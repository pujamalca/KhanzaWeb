<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\FilamentServiceProvider as BaseFilamentServiceProvider;
use Filament\Facades\Filament;
use App\Filament\Auth\CustomLogin;
use Filament\Navigation\UserMenuItem;
use pxlrbt\FilamentSpotlight\SpotlightPlugin;

class SuperadminPanelProvider extends PanelProvider
{


    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('superadmin')
            ->path('superadmin')
            //bisa hide di desktop
            ->sidebarCollapsibleOnDesktop()
            ->favicon('/fadhila.png') // Tambahkan favicon di sini
            ->login(CustomLogin::class)
            // ->userMenuItems([
            //     UserMenuItem::make()
            //         ->label('Profile') // Label untuk menu
            //         ->url(route('App.Filament.Pages.Profile')) // URL menuju halaman profil
            //         ->icon('heroicon-o-user'), // Ikon menu
            // ])
            // ->sidebar()
            // ->notifications()
            //Ganti Warna
            ->colors([
                'primary' => Color::Sky,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                SpotlightPlugin::make(),
            ]);;
    }
}
