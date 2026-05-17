<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStatsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = 3;

    protected function getStats(): array
    {
        $total = Client::count();

        $nuevosEsteMes = Client::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        $frecuentes = Client::where('total_visits', '>=', 3)->count();

        $inactivos = Client::where(function ($q) {
            $q->whereNull('last_visit_at')
              ->orWhere('last_visit_at', '<', Carbon::now()->subMonths(3));
        })->count();

        return [
            Stat::make('Total clientes', $total)
                ->description('Registrados en el sistema')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make('Nuevos este mes', $nuevosEsteMes)
                ->description('Clientes nuevos')
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('success'),

            Stat::make('Clientes frecuentes', $frecuentes)
                ->description('+3 visitas realizadas')
                ->descriptionIcon('heroicon-m-heart')
                ->color('info'),

            Stat::make('Clientes inactivos', $inactivos)
                ->description('Sin visitar hace +3 meses')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color('warning'),
        ];
    }
}
