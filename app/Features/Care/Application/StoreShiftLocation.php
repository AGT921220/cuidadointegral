<?php

namespace App\Features\Care\Application;

use App\Models\Shift;
use App\Models\User;

final class StoreShiftLocation
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

        $latitude = (float) $data['latitude'];
        $longitude = (float) $data['longitude'];
        ($this->assertWithinGeofence)($latitude, $longitude, $shift);

        $location = $shift->locations()->create([
            'user_id' => $user->id,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'accuracy' => $data['accuracy'] ?? null,
            'recorded_at' => $data['recorded_at'] ?? now(),
        ]);

        $location->load('user:id,name');

        return $location->toCareArray();
    }
}
