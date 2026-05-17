<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RevenueStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 2;

    protected function getStats(): array
    {
        $today          = Carbon::today();
        $startMes       = Carbon::now()->startOfMonth();
        $endMes         = Carbon::now()->endOfMonth();
        $startMesPasado = Carbon::now()->subMonth()->startOfMonth();
        $endMesPasado   = Carbon::now()->subMonth()->endOfMonth();

        $ingresosHoy = Appointment::whereDate('appointment_date', $today)
            ->where('status', 'completed')
            ->sum('total_price');

        $ingresosMes = Appointment::whereBetween('appointment_date', [$startMes, $endMes])
            ->where('status', 'completed')
            ->sum('total_price');

        $ingresosMesPasado = Appointment::whereBetween('appointment_date', [$startMesPasado, $endMesPasado])
            ->where('status', 'completed')
            ->sum('total_price');

        $citasCompletadasMes = Appointment::whereBetween('appointment_date', [$startMes, $endMes])
            ->where('status', 'completed')
            ->count();

        $promedio = $citasCompletadasMes > 0 ? $ingresosMes / $citasCompletadasMes : 0;

        $diferencia  = $ingresosMes - $ingresosMesPasado;
        $tendencia   = $diferencia >= 0 ? 'up' : 'down';
        $porcentaje  = $ingresosMesPasado > 0 ? abs(($diferencia / $ingresosMesPasado) * 100) : 0;

        return [
            Stat::make('Ingresos hoy', '$' . number_format($ingresosHoy, 0, ',', '.'))
                ->description('Citas completadas hoy')
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('success'),

            Stat::make('Ingresos este mes', '$' . number_format($ingresosMes, 0, ',', '.'))
                ->description(number_format($porcentaje, 1) . '% vs mes anterior')
                ->descriptionIcon($tendencia === 'up' ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($tendencia === 'up' ? 'success' : 'danger'),

            Stat::make('Mes anterior', '$' . number_format($ingresosMesPasado, 0, ',', '.'))
                ->description('Total del mes pasado')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('gray'),

            Stat::make('Promedio por cita', '$' . number_format($promedio, 0, ',', '.'))
                ->description('Este mes (' . $citasCompletadasMes . ' citas)')
                ->descriptionIcon('heroicon-m-calculator')
                ->color('info'),
        ];
    }
}
