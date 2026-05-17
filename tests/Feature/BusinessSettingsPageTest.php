<?php

namespace Tests\Feature;

use App\Filament\Pages\BusinessSettings;
use App\Models\BlockedDate;
use App\Models\BusinessSchedule;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class BusinessSettingsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed schedules
        for ($day = 0; $day <= 6; $day++) {
            BusinessSchedule::create([
                'day_of_week' => $day,
                'open_time' => '09:00',
                'close_time' => '19:00',
                'slot_interval_minutes' => 30,
                'active' => $day !== 0,
            ]);
        }
    }

    public function test_page_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(BusinessSettings::class)
            ->assertStatus(200)
            ->assertSee('Horarios de Atención')
            ->assertSee('Fechas Bloqueadas')
            ->assertSee('Lunes')
            ->assertSee('Martes');
    }

    public function test_schedules_can_be_saved(): void
    {
        $user = User::factory()->create();
        $schedule = BusinessSchedule::where('day_of_week', 1)->first();

        Livewire::actingAs($user)
            ->test(BusinessSettings::class)
            ->call('saveSchedules')
            ->assertHasNoErrors()
            ->assertNotified('Horarios guardados correctamente');
    }

    public function test_blocked_date_can_be_added_via_action(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(BusinessSettings::class)
            ->callAction('addBlockedDate', [
                'date' => '2026-12-25',
                'reason' => 'Navidad',
            ])
            ->assertHasNoErrors();

        $this->assertDatabaseHas('blocked_dates', [
            'date' => '2026-12-25',
            'reason' => 'Navidad',
        ]);
    }

    public function test_blocked_date_can_be_removed(): void
    {
        $user = User::factory()->create();
        $blocked = BlockedDate::create(['date' => '2026-12-31', 'reason' => 'Fin de año']);

        Livewire::actingAs($user)
            ->test(BusinessSettings::class)
            ->call('removeBlockedDate', $blocked->id)
            ->assertHasNoErrors()
            ->assertNotified('Fecha desbloqueada');

        $this->assertDatabaseMissing('blocked_dates', ['id' => $blocked->id]);
    }
}
