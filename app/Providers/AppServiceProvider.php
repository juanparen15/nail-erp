<?php

namespace App\Providers;

use App\Channels\WhatsappChannel;
use App\Models\AppSetting;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        App::setLocale('es');

        Notification::extend('whatsapp', function ($app) {
            return new WhatsappChannel();
        });

        // Carga los ajustes de la BD y sobreescribe la config de Laravel.
        // El try/catch evita fallos durante php artisan migrate (tabla aún no existe).
        try {
            AppSetting::reloadConfig();
        } catch (\Throwable) {
            // La tabla app_settings aún no existe o la BD no está disponible.
        }
    }
}
