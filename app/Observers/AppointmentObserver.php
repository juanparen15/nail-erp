<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Models\AppSetting;
use App\Models\User;
use App\Notifications\AppointmentCreated;
use App\Notifications\AppointmentStatusChanged;
use Illuminate\Support\Facades\Notification;

class AppointmentObserver
{
    public function created(Appointment $appointment): void
    {
        // ── Campanita (bell) para todos los usuarios del panel ──────────────
        foreach (User::all() as $user) {
            try {
                $user->notify(new AppointmentCreated($appointment));
            } catch (\Throwable $e) {
                logger()->error('Error al notificar cita (campanita): ' . $e->getMessage(), [
                    'appointment_id' => $appointment->id,
                    'user_id'        => $user->id,
                ]);
            }
        }

        // ── Correo al administrador configurado en Ajustes ──────────────────
        $adminEmail = AppSetting::get('admin_notification_email')
            ?: AppSetting::get('mail_from_address');

        if (! empty($adminEmail)) {
            try {
                Notification::route('mail', $adminEmail)
                    ->notify(new AppointmentCreated($appointment));
            } catch (\Throwable $e) {
                logger()->error('Error al notificar cita (correo admin): ' . $e->getMessage(), [
                    'appointment_id' => $appointment->id,
                    'admin_email'    => $adminEmail,
                ]);
            }
        }
    }

    public function updated(Appointment $appointment): void
    {
        // Solo notificamos a la clienta cuando cambia el estado.
        if (! $appointment->wasChanged('status')) {
            return;
        }

        $client = $appointment->client;

        if (! $client) {
            return;
        }

        // Estados que generan notificación a la clienta.
        if (! in_array($appointment->status, ['confirmed', 'completed', 'cancelled'], true)) {
            return;
        }

        try {
            $client->notify(new AppointmentStatusChanged($appointment, $appointment->status));
        } catch (\Throwable $e) {
            logger()->error('Error al notificar cambio de estado a la clienta: ' . $e->getMessage(), [
                'appointment_id' => $appointment->id,
                'status'         => $appointment->status,
            ]);
        }
    }
}
