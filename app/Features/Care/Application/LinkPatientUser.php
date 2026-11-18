<?php

namespace App\Features\Care\Application;

use App\Models\Patient;
use App\Models\PatientLink;

final class LinkPatientUser
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function __invoke(Patient $patient, array $data): array
    {
        $link = PatientLink::query()->firstOrCreate([
            'patient_id' => $patient->id,
            'user_id' => $data['user_id'],
            'link_type' => $data['link_type'],
        ]);

        return [
            'id' => $link->id,
            'patient_id' => $link->patient_id,
            'user_id' => $link->user_id,
            'link_type' => $link->link_type,
        ];
    }
}
