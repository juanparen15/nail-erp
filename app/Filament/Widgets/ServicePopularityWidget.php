<?php

namespace App\Filament\Widgets;

use App\Models\Service;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class ServicePopularityWidget extends ChartWidget
{
    protected static ?string $heading = 'Servicios más solicitados (este mes)';
    protected static ?int $sort = 5;
    protected int|string|array $columnSpan = 1;

    protected function getData(): array
    {
        $startMes = Carbon::now()->startOfMonth();
        $endMes   = Carbon::now()->endOfMonth();

        $services = Service::withCount(['appointments' => function ($q) use ($startMes, $endMes) {
            $q->whereBetween('appointment_date', [$startMes, $endMes]);
        }])->get();

        return [
            'datasets' => [
                [
                    'label'           => 'Citas',
                    'data'            => $services->pluck('appointments_count')->toArray(),
                    'backgroundColor' => $services->pluck('color')->toArray(),
                    'borderWidth'     => 2,
                    'borderColor'     => '#ffffff',
                ],
            ],
            'labels' => $services->pluck('name')->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
