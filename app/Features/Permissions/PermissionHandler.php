<?php

namespace App\Features\Permissions;

use App\Features\Permissions\Constants\PermissionTypes;
use App\Features\Permissions\Constants\RoleTypes;

final class PermissionHandler
{
    /**
     * @return list<string>
     */
    public function getAllPermissions(): array
    {
        return [
            PermissionTypes::DASHBOARD_VIEW,
            PermissionTypes::USERS_VIEW,
            PermissionTypes::USERS_CREATE,
            PermissionTypes::USERS_UPDATE,
            PermissionTypes::USERS_DELETE,
            PermissionTypes::ROLES_VIEW,
            PermissionTypes::ROLES_CREATE,
            PermissionTypes::ROLES_UPDATE,
            PermissionTypes::ROLES_DELETE,
            PermissionTypes::PATIENTS_VIEW,
            PermissionTypes::PATIENTS_CREATE,
            PermissionTypes::PATIENT_LINKS_CREATE,
            PermissionTypes::SHIFTS_VIEW,
            PermissionTypes::SHIFTS_CREATE,
            PermissionTypes::SHIFTS_CHECK_IN,
            PermissionTypes::SHIFTS_CHECK_OUT,
            PermissionTypes::SHIFT_LOCATIONS_CREATE,
            PermissionTypes::CARE_TASKS_VIEW,
            PermissionTypes::CARE_TASKS_CREATE,
            PermissionTypes::CARE_TASKS_RECORD,
            PermissionTypes::CARE_LOGS_VIEW,
            PermissionTypes::CARE_LOGS_CREATE,
            PermissionTypes::HEALTH_RECORDS_VIEW,
            PermissionTypes::HEALTH_RECORDS_CREATE,
            PermissionTypes::INCIDENTS_VIEW,
            PermissionTypes::INCIDENTS_CREATE,
            PermissionTypes::INCIDENTS_UPDATE,
            PermissionTypes::REPORTS_VIEW,
        ];
    }

    /**
     * @return list<string>
     */
    public function permissionsForRole(string $role): array
    {
        $read = [
            PermissionTypes::PATIENTS_VIEW,
            PermissionTypes::SHIFTS_VIEW,
            PermissionTypes::CARE_TASKS_VIEW,
            PermissionTypes::CARE_LOGS_VIEW,
            PermissionTypes::HEALTH_RECORDS_VIEW,
            PermissionTypes::INCIDENTS_VIEW,
            PermissionTypes::REPORTS_VIEW,
        ];

        return match ($role) {
            RoleTypes::ADMIN => $this->getAllPermissions(),
            RoleTypes::CUIDADOR => [
                ...$read,
                PermissionTypes::SHIFTS_CHECK_IN,
                PermissionTypes::SHIFTS_CHECK_OUT,
                PermissionTypes::SHIFT_LOCATIONS_CREATE,
                PermissionTypes::CARE_TASKS_RECORD,
                PermissionTypes::CARE_LOGS_CREATE,
                PermissionTypes::HEALTH_RECORDS_CREATE,
                PermissionTypes::INCIDENTS_CREATE,
            ],
            RoleTypes::FAMILIAR => $read,
            RoleTypes::SUPERVISOR => [
                ...$read,
                PermissionTypes::INCIDENTS_UPDATE,
            ],
            default => [],
        };
    }

    /**
     * @return array<string, string>
     */
    public function getPermissionNamesInSpanish(): array
    {
        return [
            PermissionTypes::DASHBOARD_VIEW => 'Ver el panel',
            PermissionTypes::USERS_VIEW => 'Ver usuarios',
            PermissionTypes::USERS_CREATE => 'Crear usuarios',
            PermissionTypes::USERS_UPDATE => 'Editar usuarios',
            PermissionTypes::USERS_DELETE => 'Eliminar usuarios',
            PermissionTypes::ROLES_VIEW => 'Ver roles',
            PermissionTypes::ROLES_CREATE => 'Crear roles',
            PermissionTypes::ROLES_UPDATE => 'Editar roles',
            PermissionTypes::ROLES_DELETE => 'Eliminar roles',
            PermissionTypes::PATIENTS_VIEW => 'Ver pacientes asignados',
            PermissionTypes::PATIENTS_CREATE => 'Registrar pacientes',
            PermissionTypes::PATIENT_LINKS_CREATE => 'Vincular personas a un paciente',
            PermissionTypes::SHIFTS_VIEW => 'Ver turnos',
            PermissionTypes::SHIFTS_CREATE => 'Programar turnos',
            PermissionTypes::SHIFTS_CHECK_IN => 'Registrar entrada de turno',
            PermissionTypes::SHIFTS_CHECK_OUT => 'Registrar salida de turno',
            PermissionTypes::SHIFT_LOCATIONS_CREATE => 'Registrar ubicación del turno',
            PermissionTypes::CARE_TASKS_VIEW => 'Ver tareas de cuidado',
            PermissionTypes::CARE_TASKS_CREATE => 'Crear tareas de cuidado',
            PermissionTypes::CARE_TASKS_RECORD => 'Registrar el estado de una tarea',
            PermissionTypes::CARE_LOGS_VIEW => 'Ver bitácora',
            PermissionTypes::CARE_LOGS_CREATE => 'Escribir en la bitácora',
            PermissionTypes::HEALTH_RECORDS_VIEW => 'Ver registros de salud',
            PermissionTypes::HEALTH_RECORDS_CREATE => 'Capturar registros de salud',
            PermissionTypes::INCIDENTS_VIEW => 'Ver incidencias',
            PermissionTypes::INCIDENTS_CREATE => 'Reportar una incidencia',
            PermissionTypes::INCIDENTS_UPDATE => 'Actualizar el estado de una incidencia',
            PermissionTypes::REPORTS_VIEW => 'Consultar reportes',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function getModuleLabels(): array
    {
        return [
            'dashboard' => 'Panel',
            'users' => 'Usuarios',
            'roles' => 'Roles',
            'patients' => 'Pacientes',
            'patient_links' => 'Vínculos',
            'shifts' => 'Turnos',
            'shift_locations' => 'Ubicación',
            'care_tasks' => 'Tareas',
            'care_logs' => 'Bitácora',
            'health_records' => 'Salud',
            'incidents' => 'Incidencias',
            'reports' => 'Reportes',
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    public function grouped(): array
    {
        $labels = $this->getPermissionNamesInSpanish();
        $modules = $this->getModuleLabels();
        $grouped = [];

        foreach ($this->getAllPermissions() as $permission) {
            $module = strstr($permission, '.', true) ?: $permission;
            $moduleLabel = $modules[$module] ?? $module;
            $grouped[$moduleLabel][$permission] = $labels[$permission] ?? $permission;
        }

        return $grouped;
    }
}
