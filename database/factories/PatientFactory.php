<?php

namespace Database\Factories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Patient>
 */
class PatientFactory extends Factory
{
    protected $model = Patient::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'birth_date' => fake()->date(),
            'phone' => fake()->numerify('55########'),
            'address' => fake()->streetAddress(),
            'latitude' => 19.432608,
            'longitude' => -99.133209,
            'care_notes' => 'Hidratación y medicación indicada por la familia.',
        ];
    }
}
