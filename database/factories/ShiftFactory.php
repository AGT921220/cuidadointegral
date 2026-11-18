<?php

namespace Database\Factories;

use App\Models\Patient;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shift>
 */
class ShiftFactory extends Factory
{
    protected $model = Shift::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $starts = now()->addHour();

        return [
            'patient_id' => Patient::factory(),
            'caregiver_id' => User::factory(),
            'status' => Shift::STATUS_SCHEDULED,
            'starts_at' => $starts,
            'ends_at' => $starts->copy()->addHours(8),
            'service_address' => fake()->streetAddress(),
            'latitude' => 19.432608,
            'longitude' => -99.133209,
            'instructions' => 'Tocar el timbre y esperar a un familiar.',
        ];
    }
}
