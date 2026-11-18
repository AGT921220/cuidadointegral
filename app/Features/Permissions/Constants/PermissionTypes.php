<?php

namespace App\Features\Permissions\Constants;

final class PermissionTypes
{
    public const DASHBOARD_VIEW = 'dashboard.view';

    public const USERS_VIEW = 'users.view';

    public const USERS_CREATE = 'users.create';

    public const USERS_UPDATE = 'users.update';

    public const USERS_DELETE = 'users.delete';

    public const ROLES_VIEW = 'roles.view';

    public const ROLES_CREATE = 'roles.create';

    public const ROLES_UPDATE = 'roles.update';

    public const ROLES_DELETE = 'roles.delete';

    public const PATIENTS_VIEW = 'patients.view';

    public const PATIENTS_CREATE = 'patients.create';

    public const PATIENT_LINKS_CREATE = 'patient_links.create';

    public const SHIFTS_VIEW = 'shifts.view';

    public const SHIFTS_CREATE = 'shifts.create';

    public const SHIFTS_CHECK_IN = 'shifts.check_in';

    public const SHIFTS_CHECK_OUT = 'shifts.check_out';

    public const SHIFT_LOCATIONS_CREATE = 'shift_locations.create';

    public const CARE_TASKS_VIEW = 'care_tasks.view';

    public const CARE_TASKS_CREATE = 'care_tasks.create';

    public const CARE_TASKS_RECORD = 'care_tasks.record';

    public const CARE_LOGS_VIEW = 'care_logs.view';

    public const CARE_LOGS_CREATE = 'care_logs.create';

    public const HEALTH_RECORDS_VIEW = 'health_records.view';

    public const HEALTH_RECORDS_CREATE = 'health_records.create';

    public const INCIDENTS_VIEW = 'incidents.view';

    public const INCIDENTS_CREATE = 'incidents.create';

    public const INCIDENTS_UPDATE = 'incidents.update';

    public const REPORTS_VIEW = 'reports.view';
}
