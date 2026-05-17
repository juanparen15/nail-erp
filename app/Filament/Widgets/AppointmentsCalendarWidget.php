<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class AppointmentsCalendarWidget extends ChartWidget
{
    protected ?string $heading = 'Citas por día (últimas 4 semanas)';

    protected function getData(): array
    {
        $dates = [];
        $counts = [];
        $revenues = [];

        for ($i = 27; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $dates[] = $date->format('d/m');

            $dayAppointments = Appointment::whereDate('appointment_date', $date)
                ->whereIn('status', ['completed', 'confirmed'])
                ->get();

            $counts[] = $dayAppointments->count();
            $revenues[] = $dayAppointments->sum('total_price');
        }

        return [
            'datasets' => [
                [
                    'label' => 'Citas',
                    'data' => $counts,
                    'borderColor' => '#ec4899',
                    'backgroundColor' => 'rgba(236, 72, 153, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
            ],
            'labels' => $dates,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
