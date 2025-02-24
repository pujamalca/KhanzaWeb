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
use App\Http\Controllers\Auth\CustomLogoutController;
use App\Filament\Pages\Settings;
use App\Http\Middleware\AutoLogout;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Navigation\MenuItem;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\UserMenuItem;
use Filament\Pages\Dashboard;
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
            // ->logout(CustomLogoutController::class)
            ->userMenuItems([
                'logout' => MenuItem::make()->label('Log out'),
            ])
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
                AutoLogout::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                AutoLogout::class,
            ])
            ->routes(fn() => [])
            ->plugins([
                SpotlightPlugin::make(),
                \BezhanSalleh\FilamentShield\FilamentShieldPlugin::make(),

                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 3
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                        'lg' => 4,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm' => 2,
                    ]),
            ]);;
    }
}

