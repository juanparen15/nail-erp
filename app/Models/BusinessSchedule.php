<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSchedule extends Model
{
    protected $fillable = [
        'day_of_week',
        'open_time',
        'close_time',
        'slot_interval_minutes',
        'active',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'active' => 'boolean',
        'slot_interval_minutes' => 'integer',
    ];

    public static array $dayNames = [
        0 => 'Domingo',
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
    ];

    public function getDayNameAttribute(): string
    {
        return self::$dayNames[$this->day_of_week] ?? 'Desconocido';
    }

    public function getAvailableSlots(string $date, int $durationMinutes): array
    {
        $slots = [];
        $current = strtotime($date . ' ' . $this->open_time);
        $close = strtotime($date . ' ' . $this->close_time);
        $interval = $this->slot_interval_minutes * 60;
        $serviceDuration = $durationMinutes * 60;

        while (($current + $serviceDuration) <= $close) {
            $slots[] = date('H:i', $current);
            $current += $interval;
        }

        return $slots;
    }
}
