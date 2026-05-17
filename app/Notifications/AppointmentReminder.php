<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Appointment $appointment
    ) {}

    public function via(object $notifiable): array
    {
        $channels = [];

        if (!empty($notifiable->email)) {
            $channels[] = 'mail';
        }

        if ($notifiable->whatsapp || $notifiable->phone) {
            $channels[] = 'whatsapp';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appointment = $this->appointment;
        $date = \Carbon\Carbon::parse($appointment->appointment_date)->locale('es')->isoFormat('dddd, D [de] MMMM');
        $time = substr($appointment->appointment_time, 0, 5);

        return (new MailMessage)
            ->subject('Recordatorio: Tu cita es mañana')
            ->greeting('¡Hola, ' . $notifiable->name . '!')
            ->line('Te recordamos que tienes una cita mañana.')
            ->line('**Servicio:** ' . $appointment->service->name)
            ->line('**Fecha:** ' . $date)
            ->line('**Hora:** ' . $time . ' hrs')
            ->line('Si necesitas cancelar o reagendar, contáctanos lo antes posible.')
            ->line('¡Hasta mañana!');
    }

    public function toWhatsapp(object $notifiable): array
    {
        $appointment = $this->appointment;
        $date = \Carbon\Carbon::parse($appointment->appointment_date)->locale('es')->isoFormat('dddd, D [de] MMMM');
        $time = substr($appointment->appointment_time, 0, 5);

        return [
            'to' => $notifiable->whatsapp ?? $notifiable->phone,
            'message' => "⏰ *Recordatorio de Cita*\n\n"
                . "Hola {$notifiable->name}! Te recordamos que mañana tienes cita.\n\n"
                . "💅 *Servicio:* {$appointment->service->name}\n"
                . "📅 *Fecha:* {$date}\n"
                . "🕐 *Hora:* {$time} hrs\n\n"
                . "Si necesitas cancelar, escríbenos a la brevedad. ¡Te esperamos! 💗",
        ];
    }
}
