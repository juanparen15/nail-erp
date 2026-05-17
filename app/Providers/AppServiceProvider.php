<?php

namespace App\Providers;

use App\Channels\WhatsappChannel;
use Illuminate\Notifications\ChannelManager;
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
        Notification::extend('whatsapp', function ($app) {
            return new WhatsappChannel();
        });
    }
}
