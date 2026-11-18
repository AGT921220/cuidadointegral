<?php

namespace App\Features\Care\Application;

use App\Models\Shift;
use Illuminate\Validation\ValidationException;

final class AssertWithinGeofence
{
    public function __invoke(?float $latitude, ?float $longitude, Shift $shift): void
    {
        $meters = config('care.geofence_meters');

        if ($meters === null || $shift->latitude === null || $shift->longitude === null) {
            return;
        }

        if ($latitude === null || $longitude === null) {
            throw ValidationException::withMessages([
                'latitude' => 'La ubicación es obligatoria porque el turno tiene un área configurada.',
            ]);
        }

        $distance = $this->meters(
            $latitude,
            $longitude,
            (float) $shift->latitude,
            (float) $shift->longitude,
        );

        if ($distance > (float) $meters) {
            throw ValidationException::withMessages([
                'latitude' => 'La ubicación está fuera del área configurada del servicio.',
            ]);
        }
    }

    private function meters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earth = 6371000;
        $latDelta = deg2rad($lat2 - $lat1);
        $lngDelta = deg2rad($lng2 - $lng1);
        $a = sin($latDelta / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($lngDelta / 2) ** 2;

        return 2 * $earth * asin(min(1, sqrt($a)));
    }
}
