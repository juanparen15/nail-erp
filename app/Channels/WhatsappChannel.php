<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Twilio\Rest\Client as TwilioClient;

class WhatsappChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toWhatsapp')) {
            return;
        }

        $data = $notification->toWhatsapp($notifiable);

        if (empty($data['to']) || empty($data['message'])) {
            return;
        }

        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        $from = config('services.twilio.whatsapp_from', 'whatsapp:+14155238886');

        if (!$sid || !$token || $sid === 'your_twilio_sid') {
            logger()->info('WhatsApp (mock): To=' . $data['to'] . ' | ' . $data['message']);
            return;
        }

        try {
            $client = new TwilioClient($sid, $token);
            $to = $data['to'];

            if (!str_starts_with($to, 'whatsapp:')) {
                $to = 'whatsapp:+57' . preg_replace('/\D/', '', $to);
            }

            $client->messages->create($to, [
                'from' => $from,
                'body' => $data['message'],
            ]);
        } catch (\Exception $e) {
            logger()->error('WhatsApp send failed: ' . $e->getMessage());
        }
    }
}
