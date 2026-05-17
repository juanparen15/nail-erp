<?php

namespace App\Notifications;

use App\Models\MarketingCampaign;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MarketingMessage extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly MarketingCampaign $campaign
    ) {}

    public function via(object $notifiable): array
    {
        $channels = $this->campaign->channels ?? ['email'];
        $via = [];

        if (in_array('email', $channels) && !empty($notifiable->email)) {
            $via[] = 'mail';
        }

        if (in_array('whatsapp', $channels) && ($notifiable->whatsapp || $notifiable->phone)) {
            $via[] = 'whatsapp';
        }

        return $via;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->campaign->title)
            ->greeting('¡Hola, ' . $notifiable->name . '!')
            ->line($this->campaign->message)
            ->action('Reservar cita', url('/reservar'));
    }

    public function toWhatsapp(object $notifiable): array
    {
        return [
            'to' => $notifiable->whatsapp ?? $notifiable->phone,
            'message' => "💅 *{$this->campaign->title}*\n\n{$this->campaign->message}\n\n"
                . "Reserva tu cita: " . url('/reservar'),
        ];
    }
}
