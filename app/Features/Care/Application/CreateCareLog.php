<?php

namespace App\Features\Care\Application;

use App\Models\Shift;
use App\Models\User;

final class CreateCareLog
{
    public function __construct(private readonly CareScope $careScope) {}

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function __invoke(User $user, Shift $shift, array $data): array
    {
        $this->careScope->assertCanOperateShift($user, $shift);

        $log = $shift->careLogs()->create([
            'patient_id' => $shift->patient_id,
            'user_id' => $user->id,
            'kind' => $data['kind'],
            'body' => $data['body'],
        ]);

        $log->load('user:id,name');

        return $log->toCareArray();
    }
}
