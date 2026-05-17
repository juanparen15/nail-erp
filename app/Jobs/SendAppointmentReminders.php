<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Notifications\AppointmentReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAppointmentReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Appointment::forReminder()
            ->with('client', 'service')
            ->each(function (Appointment $appointment) {
                try {
                    $appointment->client->notify(new AppointmentReminder($appointment));
                    $appointment->update(['reminder_sent' => true]);
                } catch (\Exception $e) {
                    logger()->error("Reminder failed for appointment {$appointment->id}: " . $e->getMessage());
                }
            });
    }
}
