<?php

namespace App\Features\Care\Application;

use App\Features\Permissions\Constants\RoleTypes;
use App\Models\PatientLink;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Builder;

final class CareScope
{
    public function seesAll(User $user): bool
    {
        return $user->hasRole(RoleTypes::ADMIN);
    }

    public function constrainShifts(Builder $query, User $user): void
    {
        if ($this->seesAll($user)) {
            return;
        }

        $patientIds = PatientLink::query()
            ->where('user_id', $user->id)
            ->pluck('patient_id');

        $query->where(function (Builder $inner) use ($user, $patientIds): void {
            $inner->where('caregiver_id', $user->id)
                ->orWhereIn('patient_id', $patientIds);
        });
    }

    public function assertCanViewPatient(User $user, int $patientId): void
    {
        if ($this->seesAll($user)) {
            return;
        }

        $linked = PatientLink::query()
            ->where('user_id', $user->id)
            ->where('patient_id', $patientId)
            ->exists();

        if ($linked) {
            return;
        }

        $assigned = Shift::query()
            ->where('caregiver_id', $user->id)
            ->where('patient_id', $patientId)
            ->exists();

        if ($assigned) {
            return;
        }

        throw new AuthorizationException('No puedes consultar este paciente.');
    }

    public function assertCanViewShift(User $user, Shift $shift): void
    {
        if ($this->seesAll($user) || $shift->caregiver_id === $user->id) {
            return;
        }

        $this->assertCanViewPatient($user, (int) $shift->patient_id);
    }

    public function assertCanOperateShift(User $user, Shift $shift): void
    {
        if ($this->seesAll($user) || $shift->caregiver_id === $user->id) {
            return;
        }

        throw new AuthorizationException('Este turno no está asignado a tu usuario.');
    }

    public function assertCanAttendIncident(User $user, int $patientId): void
    {
        if ($this->seesAll($user)) {
            return;
        }

        $supervises = PatientLink::query()
            ->where('user_id', $user->id)
            ->where('patient_id', $patientId)
            ->where('link_type', PatientLink::SUPERVISOR)
            ->exists();

        if ($supervises) {
            return;
        }

        throw new AuthorizationException('No puedes actualizar esta incidencia.');
    }
}
