<?php

namespace App\Models;

use Database\Factories\PatientFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'birth_date', 'phone', 'address', 'latitude', 'longitude', 'care_notes'])]
class Patient extends Model
{
    /** @use HasFactory<PatientFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'latitude' => 'float',
            'longitude' => 'float',
        ];
    }

    public function links(): HasMany
    {
        return $this->hasMany(PatientLink::class);
    }

    public function shifts(): HasMany
    {
        return $this->hasMany(Shift::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(CareTask::class);
    }

    /**
     * @return array<string, mixed>
     */
    public function toCareArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'birth_date' => $this->birth_date?->toDateString(),
            'phone' => $this->phone,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'care_notes' => $this->care_notes,
        ];
    }
}
