<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentReminder extends Notification
{
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
        $date = \Carbon\Carbon::parse($appointment->appointment_date)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY');
        $time = substr($appointment->appointment_time, 0, 5);

        return (new MailMessage)
            ->subject('Recordatorio: Tu cita es mañana')
            ->view('emails.appointment-client', [
                'appointment'    => $appointment,
                'date'           => $date,
                'time'           => $time,
                'headerSubtitle' => 'Recordatorio de tu cita',
                'badge'          => 'Mañana',
                'badgeColor'     => '#8b5cf6',
                'greeting'       => '¡Hola, ' . $notifiable->name . '!',
                'intro'          => 'Te recordamos que tienes una cita mañana. ¡Te esperamos!',
                'closing'        => 'Si necesitas cancelar o reagendar, contáctanos lo antes posible. ¡Hasta mañana!',
                'buttonText'     => 'Ver mis citas',
                'buttonUrl'      => url('/reservar'),
            ]);
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
