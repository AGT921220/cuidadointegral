<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['patient_id', 'shift_id', 'title', 'instructions'])]
class CareTask extends Model
{
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(CareTaskRecord::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function toCareArray(): array
    {
        $records = $this->relationLoaded('records')
            ? $this->records->map(fn (CareTaskRecord $record): array => $record->toCareArray())->values()->all()
            : [];

        return [
            'id' => $this->id,
            'patient_id' => $this->patient_id,
            'shift_id' => $this->shift_id,
            'title' => $this->title,
            'instructions' => $this->instructions,
            'status' => $records[0]['status'] ?? CareTaskRecord::STATUS_PENDING,
            'records' => $records,
        ];
    }
}
