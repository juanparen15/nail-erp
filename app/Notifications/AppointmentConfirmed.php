<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentConfirmed extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Appointment $appointment
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['mail'];

        // Add WhatsApp if client has a WhatsApp number
        if ($notifiable->whatsapp || $notifiable->phone) {
            $channels[] = 'whatsapp';
        }

        return array_filter($channels, fn($channel) => $channel === 'mail'
            ? !empty($notifiable->email)
            : true
        );
    }

    public function toMail(object $notifiable): MailMessage
    {
        $appointment = $this->appointment;
        $date = \Carbon\Carbon::parse($appointment->appointment_date)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY');
        $time = substr($appointment->appointment_time, 0, 5);

        return (new MailMessage)
            ->subject('Cita reservada - ' . $appointment->service->name)
            ->greeting('¡Hola, ' . $notifiable->name . '!')
            ->line('Tu cita ha sido reservada exitosamente.')
            ->line('**Servicio:** ' . $appointment->service->name)
            ->line('**Fecha:** ' . $date)
            ->line('**Hora:** ' . $time . ' hrs')
            ->line('**Total:** $' . number_format($appointment->total_price, 0, ',', '.'))
            ->line('Tu cita está pendiente de confirmación. Te notificaremos cuando sea confirmada.')
            ->action('Ver mis citas', url('/reservar'))
            ->line('¡Gracias por confiar en nosotros!');
    }

    public function toWhatsapp(object $notifiable): array
    {
        $appointment = $this->appointment;
        $date = \Carbon\Carbon::parse($appointment->appointment_date)->locale('es')->isoFormat('dddd, D [de] MMMM');
        $time = substr($appointment->appointment_time, 0, 5);

        return [
            'to' => $notifiable->whatsapp ?? $notifiable->phone,
            'message' => "✨ *Cita Reservada*\n\n"
                . "Hola {$notifiable->name}! Tu cita ha sido reservada.\n\n"
                . "💅 *Servicio:* {$appointment->service->name}\n"
                . "📅 *Fecha:* {$date}\n"
                . "🕐 *Hora:* {$time} hrs\n"
                . "💰 *Total:* \${$appointment->total_price}\n\n"
                . "Tu cita está _pendiente de confirmación_. Te avisaremos cuando sea confirmada. 💗",
        ];
    }
}
