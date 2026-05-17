<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service;
use App\Models\BusinessSchedule;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@nailsalon.com'],
            [
                'name' => 'Administrador',
                'password' => bcrypt('password'),
            ]
        );

        // Default services
        $services = [
            ['name' => 'Semipermanente', 'duration_minutes' => 60, 'price' => 35000, 'color' => '#ec4899', 'description' => 'Esmalte semipermanente con base y sellador'],
            ['name' => 'Press On', 'duration_minutes' => 45, 'price' => 25000, 'color' => '#8b5cf6', 'description' => 'Uñas press on personalizadas'],
            ['name' => 'Poly Gel', 'duration_minutes' => 90, 'price' => 55000, 'color' => '#f59e0b', 'description' => 'Extensión con poly gel'],
            ['name' => 'Pedicure', 'duration_minutes' => 60, 'price' => 30000, 'color' => '#10b981', 'description' => 'Pedicure completo con esmaltado'],
        ];

        foreach ($services as $service) {
            Service::firstOrCreate(['name' => $service['name']], array_merge($service, ['active' => true]));
        }

        // Business schedule Mon-Sat 9am-7pm
        $schedules = [
            ['day_of_week' => 1, 'open_time' => '09:00', 'close_time' => '19:00', 'slot_interval_minutes' => 30, 'active' => true],
            ['day_of_week' => 2, 'open_time' => '09:00', 'close_time' => '19:00', 'slot_interval_minutes' => 30, 'active' => true],
            ['day_of_week' => 3, 'open_time' => '09:00', 'close_time' => '19:00', 'slot_interval_minutes' => 30, 'active' => true],
            ['day_of_week' => 4, 'open_time' => '09:00', 'close_time' => '19:00', 'slot_interval_minutes' => 30, 'active' => true],
            ['day_of_week' => 5, 'open_time' => '09:00', 'close_time' => '19:00', 'slot_interval_minutes' => 30, 'active' => true],
            ['day_of_week' => 6, 'open_time' => '09:00', 'close_time' => '17:00', 'slot_interval_minutes' => 30, 'active' => true],
            ['day_of_week' => 0, 'open_time' => '10:00', 'close_time' => '15:00', 'slot_interval_minutes' => 30, 'active' => false],
        ];

        foreach ($schedules as $schedule) {
            BusinessSchedule::firstOrCreate(['day_of_week' => $schedule['day_of_week']], $schedule);
        }
    }
}
