<?php

namespace App\Features\Care\Application;

use App\Models\Shift;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class CheckOutShift
{
    public function __construct(
        private readonly CareScope $careScope,
        private readonly AssertWithinGeofence $assertWithinGeofence,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function __invoke(User $user, Shift $shift, array $data): array
    {
        $this->careScope->assertCanOperateShift($user, $shift);

        if ($shift->check_in_at === null) {
            throw ValidationException::withMessages([
                'shift' => 'No puedes registrar la salida sin una entrada.',
            ]);
        }

        if ($shift->check_out_at !== null) {
            throw ValidationException::withMessages([
                'shift' => 'La salida de este turno ya fue registrada.',
            ]);
        }

        $latitude = isset($data['latitude']) ? (float) $data['latitude'] : null;
        $longitude = isset($data['longitude']) ? (float) $data['longitude'] : null;
        ($this->assertWithinGeofence)($latitude, $longitude, $shift);

        DB::transaction(function () use ($user, $shift, $data, $latitude, $longitude): void {
            $from = $shift->status;
            $shift->forceFill([
                'status' => Shift::STATUS_COMPLETED,
                'check_out_at' => now(),
                'check_out_latitude' => $latitude,
                'check_out_longitude' => $longitude,
                'check_out_accuracy' => $data['accuracy'] ?? null,
            ])->save();

            $shift->events()->create([
                'user_id' => $user->id,
                'from_status' => $from,
                'to_status' => Shift::STATUS_COMPLETED,
                'note' => 'Salida registrada',
            ]);

            if ($latitude !== null && $longitude !== null) {
                $shift->locations()->create([
                    'user_id' => $user->id,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'accuracy' => $data['accuracy'] ?? null,
                    'recorded_at' => now(),
                ]);
            }
        });

        $shift->refresh()->load(['patient', 'caregiver:id,name']);

        return $shift->toCareArray();
    }
}
