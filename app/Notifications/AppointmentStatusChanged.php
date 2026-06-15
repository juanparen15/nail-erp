<?php

namespace App\Notifications;

use App\Models\Appointment;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentStatusChanged extends Notification
{
    public function __construct(
        public readonly Appointment $appointment,
        public readonly string $status,
    ) {}

    public function via(object $notifiable): array
    {
        $channels = [];

        if (! empty($notifiable->email)) {
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

        $meta = $this->meta();

        return (new MailMessage)
            ->subject($meta['subject'] . ' - ' . $appointment->service->name)
            ->view('emails.appointment-client', [
                'appointment'    => $appointment,
                'date'           => $date,
                'time'           => $time,
                'headerSubtitle' => $meta['subject'],
                'badge'          => $meta['badge'],
                'badgeColor'     => $meta['badgeColor'],
                'greeting'       => '¡Hola, ' . $notifiable->name . '!',
                'intro'          => $meta['intro'],
                'closing'        => $meta['closing'],
                'buttonText'     => $meta['buttonText'],
                'buttonUrl'      => $meta['buttonUrl'],
            ]);
    }

    public function toWhatsapp(object $notifiable): array
    {
        $appointment = $this->appointment;
        $date = \Carbon\Carbon::parse($appointment->appointment_date)->locale('es')->isoFormat('dddd, D [de] MMMM');
        $time = substr($appointment->appointment_time, 0, 5);

        $meta = $this->meta();

        $message = "{$meta['emoji']} *{$meta['subject']}*\n\n"
            . "Hola {$notifiable->name}! {$meta['intro']}\n\n"
            . "💅 *Servicio:* {$appointment->service->name}\n"
            . "📅 *Fecha:* {$date}\n"
            . "🕐 *Hora:* {$time} hrs\n\n"
            . "{$meta['closing']} 💗";

        return [
            'to'      => $notifiable->whatsapp ?? $notifiable->phone,
            'message' => $message,
        ];
    }

    /** Textos y estilos según el estado. */
    private function meta(): array
    {
        return match ($this->status) {
            'confirmed' => [
                'subject'    => '¡Tu cita fue confirmada!',
                'badge'      => 'Confirmada',
                'badgeColor' => '#3b82f6',
                'emoji'      => '✅',
                'intro'      => 'Tu cita ha sido confirmada. ¡Te esperamos!',
                'closing'    => 'Si necesitas reagendar o cancelar, contáctanos lo antes posible.',
                'buttonText' => 'Ver mis citas',
                'buttonUrl'  => url('/reservar'),
            ],
            'completed' => [
                'subject'    => '¡Gracias por tu visita!',
                'badge'      => 'Completada',
                'badgeColor' => '#10b981',
                'emoji'      => '🌟',
                'intro'      => 'Tu servicio ha sido completado. ¡Esperamos que hayas quedado encantada!',
                'closing'    => 'Nos encantaría verte pronto de nuevo. ¡Reserva tu próxima cita cuando quieras!',
                'buttonText' => 'Reservar nueva cita',
                'buttonUrl'  => url('/reservar'),
            ],
            'cancelled' => [
                'subject'    => 'Tu cita fue cancelada',
                'badge'      => 'Cancelada',
                'badgeColor' => '#ef4444',
                'emoji'      => '❌',
                'intro'      => 'Lamentamos informarte que tu cita ha sido cancelada.',
                'closing'    => 'Puedes reservar una nueva cita cuando quieras. ¡Te esperamos!',
                'buttonText' => 'Reservar nueva cita',
                'buttonUrl'  => url('/reservar'),
            ],
            default => [
                'subject'    => 'Actualización de tu cita',
                'badge'      => 'Actualizada',
                'badgeColor' => '#71717a',
                'emoji'      => 'ℹ️',
                'intro'      => 'El estado de tu cita ha cambiado.',
                'closing'    => 'Gracias por confiar en Kate Nails.',
                'buttonText' => 'Ver mis citas',
                'buttonUrl'  => url('/reservar'),
            ],
        };
    }
}
