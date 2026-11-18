<?php

namespace App\Features\Care\Application;

use App\Features\Permissions\Constants\PermissionTypes;
use App\Models\CareLog;
use App\Models\CareTaskRecord;
use App\Models\HealthRecord;
use App\Models\Incident;
use App\Models\Shift;
use App\Models\ShiftLocation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final class SearchCareReports
{
    public function __construct(private readonly CareScope $careScope) {}

    /**
     * @param  array<string, mixed>  $filters
     * @return array<string, mixed>
     */
    public function __invoke(User $user, array $filters): array
    {
        if (! empty($filters['patient_id'])) {
            $this->careScope->assertCanViewPatient($user, (int) $filters['patient_id']);
        }

        $limit = min(max((int) ($filters['limit'] ?? 20), 1), 20);
        $offset = max((int) ($filters['offset'] ?? 0), 0);

        $query = Shift::query()->with([
            'patient',
            'caregiver:id,name',
            'locations.user:id,name',
            'taskRecords.user:id,name',
            'taskRecords.task:id,title',
            'careLogs.user:id,name',
            'healthRecords.user:id,name',
            'incidents.reporter:id,name',
            'incidents.events.user:id,name',
        ]);

        $this->careScope->constrainShifts($query, $user);
        $this->applyFilters($query, $filters);

        $total = (clone $query)->count();
        $shifts = $query->orderBy('starts_at')->offset($offset)->limit($limit)->get();

        return [
            'data' => $shifts->map(fn (Shift $shift): array => $this->row($user, $shift))->values(),
            'meta' => [
                'total' => $total,
                'limit' => $limit,
                'offset' => $offset,
            ],
        ];
    }

    /**
     * @param  Builder<Shift>  $query
     * @param  array<string, mixed>  $filters
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['patient_id'])) {
            $query->where('patient_id', $filters['patient_id']);
        }

        if (! empty($filters['caregiver_id'])) {
            $query->where('caregiver_id', $filters['caregiver_id']);
        }

        if (! empty($filters['shift_id'])) {
            $query->whereKey($filters['shift_id']);
        }

        if (! empty($filters['from'])) {
            $query->where('starts_at', '>=', $filters['from']);
        }

        if (! empty($filters['to'])) {
            $query->where('starts_at', '<=', $filters['to']);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function row(User $user, Shift $shift): array
    {
        $row = [
            'shift' => $shift->toCareArray(),
        ];

        if ($user->can(PermissionTypes::SHIFTS_VIEW)) {
            $row['attendance'] = [
                'check_in_at' => $shift->check_in_at?->toIso8601String(),
                'check_out_at' => $shift->check_out_at?->toIso8601String(),
                'locations' => $shift->locations
                    ->map(fn (ShiftLocation $location): array => $location->toCareArray())
                    ->values()
                    ->all(),
            ];
        }

        if ($user->can(PermissionTypes::CARE_TASKS_VIEW)) {
            $row['tasks'] = $shift->taskRecords
                ->map(fn (CareTaskRecord $record): array => [
                    ...$record->toCareArray(),
                    'title' => $record->task?->title,
                ])
                ->values()
                ->all();
        }

        if ($user->can(PermissionTypes::CARE_LOGS_VIEW)) {
            $row['logs'] = $shift->careLogs
                ->map(fn (CareLog $log): array => $log->toCareArray())
                ->values()
                ->all();
        }

        if ($user->can(PermissionTypes::HEALTH_RECORDS_VIEW)) {
            $row['health_records'] = $shift->healthRecords
                ->map(fn (HealthRecord $record): array => $record->toCareArray())
                ->values()
                ->all();
        }

        if ($user->can(PermissionTypes::INCIDENTS_VIEW)) {
            $row['incidents'] = $shift->incidents
                ->map(fn (Incident $incident): array => $incident->toCareArray())
                ->values()
                ->all();
        }

        return $row;
    }
}
