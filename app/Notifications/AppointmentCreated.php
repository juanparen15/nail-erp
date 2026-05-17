<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCreated extends Notification
{
    public function __construct(
        public readonly Appointment $appointment,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    // ── Filament bell ─────────────────────────────────────────────────────────

    public function toDatabase(object $notifiable): array
    {
        $appointment = $this->appointment;
        $date        = $appointment->appointment_date->format('d/m/Y');
        $time        = substr($appointment->appointment_time, 0, 5);

        return [
            'format'    => 'filament',   // Required by Filament bell filter
            'duration'  => 'persistent', // Keep in bell until manually dismissed
            'title'     => 'Nueva cita registrada',
            'body'      => "{$appointment->client->name} — {$appointment->service->name} el {$date} a las {$time}",
            'icon'      => 'heroicon-o-calendar',
            'iconColor' => 'success',
            'actions'   => [
                [
                    'name'  => 'view',
                    'label' => 'Ver cita',
                    'color' => 'primary',
                    'url'   => url('/admin/appointments/' . $appointment->id . '/edit'),
                ],
            ],
        ];
    }

    // ── Email ─────────────────────────────────────────────────────────────────

    public function toMail(object $notifiable): MailMessage
    {
        $appointment = $this->appointment;
        $date        = $appointment->appointment_date->format('d/m/Y');
        $time        = substr($appointment->appointment_time, 0, 5);
        $editUrl     = url('/admin/appointments/' . $appointment->id . '/edit');

        return (new MailMessage)
            ->subject('Nueva cita: ' . $appointment->client->name . ' — ' . $date)
            ->view('emails.appointment-created', [
                'appointment' => $appointment,
                'date'        => $date,
                'time'        => $time,
                'editUrl'     => $editUrl,
            ]);
    }
}
