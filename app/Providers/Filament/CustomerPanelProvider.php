<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\Login;
use App\Filament\Resources\LiveCallResource\Widgets\StatsOverview;
use App\Filament\Widgets\DashboardChart;
use App\Filament\Widgets\DashboardStats;
use App\Livewire\CustomerPersonalInfo;
use App\Livewire\CustomerUpdatePassword;
use Filament\Http\Middleware\Authenticate;
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
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;

class CustomerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('customer')
           // ->path('customer')
            ->login(Login::class)
            ->profile()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->favicon(asset('images/favicon.png'))
            ->plugin(
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true, // Sets the 'account' link in the panel User Menu (default = true)
                        shouldRegisterNavigation: false, // Adds a main navigation item for the My Profile page (default = false)
                        hasAvatars: false, // Enables the avatar upload form component (default = false)
                        slug: 'my-profile' // Sets the slug for the profile page (default = 'my-profile')
                    )
                   ->myProfileComponents([
                         'personal_info' => CustomerPersonalInfo::class,
                        'update_password' => CustomerUpdatePassword::class, // replaces UpdatePassword component with your own.
                        // 'two_factor_authentication' => ,
                        // 'sanctum_tokens' =>
                    ])

            )
            ->resources([
                config('filament-logger.activity_resource')
            ])
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
           // ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
             //   Widgets\AccountWidget::class,
                DashboardStats::class,
                DashboardChart::class
            ])
            ->navigationGroups([
                'Campaigns',
                'Reports',
                'Misc.',
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
            ]);
    }
}
