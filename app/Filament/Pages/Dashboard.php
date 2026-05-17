<?php

namespace App\Filament\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Panel de control';
    }

    public static function getNavigationLabel(): string
    {
        return 'Panel de control';
    }
}
