<?php

namespace App\Livewire;

use App\Models\Appointment;
use App\Models\BlockedDate;
use App\Models\BusinessSchedule;
use App\Models\Client;
use App\Models\Service;
use App\Notifications\AppointmentConfirmed;
use Carbon\Carbon;
use Livewire\Component;

class BookingForm extends Component
{
    public int $step = 1;
    public int $totalSteps = 4;

    // Step 1: Service
    public ?int $selectedServiceId = null;
    public ?Service $selectedService = null;

    // Step 2: Date
    public ?string $selectedDate = null;

    // Step 3: Time
    public ?string $selectedTime = null;
    public array $availableSlots = [];

    // Step 4: Client data
    public string $clientName = '';
    public string $clientPhone = '';
    public string $clientEmail = '';
    public string $clientWhatsapp = '';
    public string $appointmentNotes = '';

    // Result
    public ?Appointment $confirmedAppointment = null;
    public bool $bookingComplete = false;

    public function mount(): void
    {
        // Initialize
    }

    public function selectService(int $serviceId): void
    {
        $this->selectedServiceId = $serviceId;
        $this->selectedService = Service::findOrFail($serviceId);
        $this->selectedDate = null;
        $this->selectedTime = null;
    }

    public function nextStep(): void
    {
        $this->validateCurrentStep();
        $this->step++;

        if ($this->step === 3 && $this->selectedDate) {
            $this->loadAvailableSlots();
        }

        $this->dispatch('stepChanged', step: $this->step);
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
        $this->dispatch('stepChanged', step: $this->step);
    }

    public function goToStep(int $step): void
    {
        if ($step < $this->step) {
            $this->step = $step;
            $this->dispatch('stepChanged', step: $this->step);
        }
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = $date;
        $this->selectedTime = null;
    }

    public function selectTime(string $time): void
    {
        $this->selectedTime = $time;
    }

    public function loadAvailableSlots(): void
    {
        if (!$this->selectedDate || !$this->selectedService) {
            $this->availableSlots = [];
            return;
        }

        $date = Carbon::parse($this->selectedDate);
        $dayOfWeek = $date->dayOfWeek;

        $schedule = BusinessSchedule::where('day_of_week', $dayOfWeek)
            ->where('active', true)
            ->first();

        if (!$schedule) {
            $this->availableSlots = [];
            return;
        }

        $allSlots = $schedule->getAvailableSlots(
            $this->selectedDate,
            $this->selectedService->duration_minutes
        );

        // Filter out already booked slots
        $bookedSlots = Appointment::where('appointment_date', $this->selectedDate)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('appointment_time')
            ->map(fn($t) => substr($t, 0, 5))
            ->toArray();

        $this->availableSlots = array_filter($allSlots, function ($slot) use ($bookedSlots) {
            return !in_array($slot, $bookedSlots);
        });

        $this->availableSlots = array_values($this->availableSlots);
    }

    public function getAvailableDatesProperty(): array
    {
        $dates = [];
        $today = Carbon::today();

        for ($i = 1; $i <= 60; $i++) {
            $date = $today->copy()->addDays($i);
            $dayOfWeek = $date->dayOfWeek;

            // Check if business is open
            $hasSchedule = BusinessSchedule::where('day_of_week', $dayOfWeek)
                ->where('active', true)
                ->exists();

            if (!$hasSchedule) continue;

            // Check if date is blocked
            if (BlockedDate::isBlocked($date->format('Y-m-d'))) continue;

            $dates[] = $date->format('Y-m-d');
        }

        return $dates;
    }

    private function validateCurrentStep(): void
    {
        match ($this->step) {
            1 => $this->validate(['selectedServiceId' => 'required|exists:services,id']),
            2 => $this->validate(['selectedDate' => 'required|date|after:today']),
            3 => $this->validate(['selectedTime' => 'required']),
            default => null,
        };
    }

    public function confirmBooking(): void
    {
        $this->validate([
            'clientName' => 'required|min:2|max:100',
            'clientPhone' => 'required|min:7|max:20',
            'clientEmail' => 'nullable|email',
        ]);

        // Find or create client
        $client = Client::where('phone', $this->clientPhone)->first();

        if (!$client) {
            $client = Client::create([
                'name' => $this->clientName,
                'phone' => $this->clientPhone,
                'email' => $this->clientEmail ?: null,
                'whatsapp' => $this->clientWhatsapp ?: $this->clientPhone,
            ]);
        } else {
            $client->update([
                'name' => $this->clientName,
                'email' => $this->clientEmail ?: $client->email,
            ]);
        }

        // Create appointment
        $appointment = Appointment::create([
            'client_id' => $client->id,
            'service_id' => $this->selectedServiceId,
            'appointment_date' => $this->selectedDate,
            'appointment_time' => $this->selectedTime . ':00',
            'status' => 'pending',
            'notes' => $this->appointmentNotes ?: null,
            'total_price' => $this->selectedService->price,
        ]);

        // Send confirmation notification
        try {
            $client->notify(new AppointmentConfirmed($appointment));
        } catch (\Exception $e) {
            // Log error but don't fail the booking
            logger()->error('Failed to send confirmation: ' . $e->getMessage());
        }

        $this->confirmedAppointment = $appointment->load(['client', 'service']);
        $this->bookingComplete = true;
        $this->step = 5;
        $this->dispatch('stepChanged', step: 5);
    }

    public function render()
    {
        return view('livewire.booking-form', [
            'services' => Service::active()->orderBy('name')->get(),
            'availableDates' => $this->availableDates,
        ])->layout('layouts.booking');
    }
}
