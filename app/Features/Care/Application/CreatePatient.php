<?php

namespace App\Features\Care\Application;

use App\Models\Patient;

final class CreatePatient
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function __invoke(array $data): array
    {
        $patient = Patient::query()->create([
            'name' => $data['name'],
            'birth_date' => $data['birth_date'] ?? null,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'care_notes' => $data['care_notes'] ?? null,
        ]);

        return $patient->toCareArray();
    }
}
