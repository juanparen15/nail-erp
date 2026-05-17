<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentCancelled extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Appointment $appointment,
        public readonly string $reason = ''
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

        $mail = (new MailMessage)
            ->subject('Cita cancelada - ' . $appointment->service->name)
            ->greeting('Hola, ' . $notifiable->name)
            ->line('Lamentamos informarte que tu cita ha sido cancelada.')
            ->line('**Servicio:** ' . $appointment->service->name)
            ->line('**Fecha:** ' . $date . ' a las ' . $time . ' hrs');

        if ($this->reason) {
            $mail->line('**Motivo:** ' . $this->reason);
        }

        return $mail
            ->action('Reservar nueva cita', url('/reservar'))
            ->line('Puedes reservar una nueva cita cuando quieras. ¡Te esperamos!');
    }

    public function toWhatsapp(object $notifiable): array
    {
        $appointment = $this->appointment;
        $date = \Carbon\Carbon::parse($appointment->appointment_date)->locale('es')->isoFormat('D [de] MMMM');
        $time = substr($appointment->appointment_time, 0, 5);

        $message = "❌ *Cita Cancelada*\n\n"
            . "Hola {$notifiable->name}, lamentamos informarte que tu cita ha sido cancelada.\n\n"
            . "💅 *Servicio:* {$appointment->service->name}\n"
            . "📅 *Fecha:* {$date} a las {$time} hrs\n";

        if ($this->reason) {
            $message .= "📝 *Motivo:* {$this->reason}\n";
        }

        $message .= "\nPuedes reservar una nueva cita en cualquier momento. ¡Disculpa los inconvenientes! 💗";

        return [
            'to' => $notifiable->whatsapp ?? $notifiable->phone,
            'message' => $message,
        ];
    }
}
