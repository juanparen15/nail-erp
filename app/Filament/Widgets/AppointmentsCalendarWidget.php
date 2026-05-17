<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class AppointmentsCalendarWidget extends Widget
{
    protected static ?int $sort = 6;
    protected int|string|array $columnSpan = 'full';
    protected static string $view = 'filament.widgets.appointments-calendar-widget';
}
