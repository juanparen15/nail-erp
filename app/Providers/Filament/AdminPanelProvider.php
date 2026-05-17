<?php

namespace App\Providers\Filament;

use App\Filament\Pages\AppSettings;
use App\Filament\Pages\ChangePassword;
use App\Filament\Pages\Dashboard;
use App\Filament\Pages\EditProfile;
use App\Filament\Widgets\AppointmentsCalendarWidget;
use App\Filament\Widgets\ClientStatsWidget;
use App\Filament\Widgets\RevenueStatsWidget;
use App\Filament\Widgets\ServicePopularityWidget;
use App\Filament\Widgets\TodayAppointmentsWidget;
use App\Filament\Widgets\UpcomingAppointmentsWidget;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->darkMode(true)
            ->colors([
                'primary' => Color::Pink,
            ])
            ->brandName('Kate Nails')
            ->userMenuItems([
                MenuItem::make()
                    ->label('Editar perfil')
                    ->icon('heroicon-o-user-circle')
                    ->url(fn () => EditProfile::getUrl()),

                MenuItem::make()
                    ->label('Cambiar contraseña')
                    ->icon('heroicon-o-key')
                    ->url(fn () => ChangePassword::getUrl()),

                MenuItem::make()
                    ->label('Ajustes generales')
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->url(fn () => AppSettings::getUrl()),
            ])
            ->renderHook(
                'panels::user-menu.before',
                fn (): \Illuminate\Contracts\View\View => view('filament.components.home-button'),
            )
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                // AccountWidget::class,
                TodayAppointmentsWidget::class,
                RevenueStatsWidget::class,
                ClientStatsWidget::class,
                UpcomingAppointmentsWidget::class,
                ServicePopularityWidget::class,
                AppointmentsCalendarWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
