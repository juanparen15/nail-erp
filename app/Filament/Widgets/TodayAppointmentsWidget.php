<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TodayAppointmentsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();

        $total      = Appointment::whereDate('appointment_date', $today)->count();
        $pendientes = Appointment::whereDate('appointment_date', $today)->where('status', 'pending')->count();
        $confirmadas = Appointment::whereDate('appointment_date', $today)->where('status', 'confirmed')->count();
        $completadas = Appointment::whereDate('appointment_date', $today)->where('status', 'completed')->count();

        return [
            Stat::make('Citas hoy', $total)
                ->description('Total programadas')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Pendientes', $pendientes)
                ->description('Sin confirmar')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('Confirmadas', $confirmadas)
                ->description('Listas para atender')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('info'),

            Stat::make('Completadas', $completadas)
                ->description('Finalizadas hoy')
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),
        ];
    }
}
