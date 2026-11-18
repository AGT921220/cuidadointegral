<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['patient_id', 'user_id', 'link_type'])]
class PatientLink extends Model
{
    public const CAREGIVER = 'caregiver';

    public const FAMILY = 'family';

    public const SUPERVISOR = 'supervisor';

    /**
     * @return list<string>
     */
    public static function types(): array
    {
        return [self::CAREGIVER, self::FAMILY, self::SUPERVISOR];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
