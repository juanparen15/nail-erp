<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Models\User;
use App\Notifications\AppointmentCreated;

class AppointmentObserver
{
    public function created(Appointment $appointment): void
    {
        $users = User::all();

        foreach ($users as $user) {
            try {
                $user->notify(new AppointmentCreated($appointment));
            } catch (\Throwable $e) {
                logger()->error('Error al enviar notificación de cita: ' . $e->getMessage(), [
                    'appointment_id' => $appointment->id,
                    'user_id'        => $user->id,
                ]);
            }
        }
    }
}
