<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function appointments(Request $request): JsonResponse
    {
        $start = $request->query('start', now()->startOfMonth()->toDateString());
        $end   = $request->query('end',   now()->endOfMonth()->toDateString());

        $appointments = Appointment::with(['client', 'service'])
            ->whereDate('appointment_date', '>=', $start)
            ->whereDate('appointment_date', '<=', $end)
            ->get()
            ->map(function (Appointment $appointment) {
                $color = match ($appointment->status) {
                    'pending'   => '#f59e0b',
                    'confirmed' => '#3b82f6',
                    'completed' => '#10b981',
                    'cancelled' => '#ef4444',
                    'no_show'   => '#6b7280',
                    default     => '#6b7280',
                };

                $startDatetime = $appointment->appointment_date->format('Y-m-d')
                    . 'T' . substr($appointment->appointment_time, 0, 5);

                return [
                    'id'              => $appointment->id,
                    'title'           => ($appointment->client->name ?? 'Sin nombre')
                                        . ' — ' . ($appointment->service->name ?? ''),
                    'start'           => $startDatetime,
                    'backgroundColor' => $color,
                    'borderColor'     => $color,
                    'textColor'       => '#ffffff',
                    'extendedProps'   => [
                        'client'  => $appointment->client->name ?? '',
                        'service' => $appointment->service->name ?? '',
                        'status'  => $appointment->status_label,
                        'price'   => '$' . number_format($appointment->total_price, 0, ',', '.'),
                        'phone'   => $appointment->client->phone ?? '',
                    ],
                ];
            });

        return response()->json($appointments);
    }
}
