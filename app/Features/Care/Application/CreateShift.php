<?php

namespace App\Features\Care\Application;

use App\Models\Patient;
use App\Models\PatientLink;
use App\Models\Shift;
use Illuminate\Support\Facades\DB;

final class CreateShift
{
    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function __invoke(array $data): array
    {
        $patient = Patient::query()->findOrFail($data['patient_id']);

        $shift = DB::transaction(function () use ($patient, $data): Shift {
            $shift = Shift::query()->create([
                'patient_id' => $patient->id,
                'caregiver_id' => $data['caregiver_id'],
                'status' => Shift::STATUS_SCHEDULED,
                'starts_at' => $data['starts_at'],
                'ends_at' => $data['ends_at'],
                'service_address' => $data['service_address'] ?? $patient->address,
                'latitude' => $data['latitude'] ?? $patient->latitude,
                'longitude' => $data['longitude'] ?? $patient->longitude,
                'instructions' => $data['instructions'] ?? null,
            ]);

            PatientLink::query()->firstOrCreate([
                'patient_id' => $patient->id,
                'user_id' => $data['caregiver_id'],
                'link_type' => PatientLink::CAREGIVER,
            ]);

            return $shift;
        });

        $shift->load(['patient', 'caregiver:id,name']);

        return $shift->toCareArray();
    }
}
