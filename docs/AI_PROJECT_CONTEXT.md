# Contexto — Cuidado Integral

Backend de una app móvil. Trae el login, la administración de usuarios y roles, y el dominio de cuidado (pacientes, turnos, tareas, bitácora, salud e incidencias). No hay pantallas de cuidado.

## Stack

- PHP 8.4-fpm, Laravel 13, MySQL 8.4, Redis 8
- Fortify (sesión web), Sanctum (token Bearer), Spatie Permission, Horizon
- Blade, Tabler, Bootstrap 5, jQuery, DataTables, Vite 8
- PHPUnit 12, sqlite en memoria

## Dominio actual

| Concepto | Qué es |
| --- | --- |
| Usuario | Persona que entra al panel o a la app. `nickname` único, soft delete. |
| Rol Admin | Opera el panel y Horizon. Tiene todos los permisos. |
| Rol Usuario | Entra a la app. No tiene permisos de panel. |
| Rol Cuidador | Opera los turnos que tiene asignados. |
| Rol Familiar | Consulta los pacientes vinculados a su cuenta. |
| Rol Supervisor | Consulta esos pacientes y atiende incidencias. |
| Paciente | Persona cuidada. La app solo ve datos de cuidado. |
| Vínculo | `patient_links`: caregiver, family o supervisor. |
| Turno | Servicio con horario, domicilio, estado y asistencia. |
| Permiso | Constante `{modulo}.{accion}` guardada con Spatie, guard `web`. |

El panel exige el permiso `dashboard.view`. La app exige un rol de `RoleTypes::mobile()` y un `device_name`.

## Relaciones

- `User` many-to-many `Role` (Spatie `model_has_roles`)
- `Role` many-to-many `Permission`
- `User` has many `personal_access_tokens`
- `Patient` has many `PatientLink`, `Shift`, `CareTask`
- `Shift` pertenece a `Patient` y a un cuidador (`users`)
- `Shift` has many `ShiftEvent`, `ShiftLocation`, `CareLog`, `HealthRecord`, `Incident`
- `CareTask` has many `CareTaskRecord`
- `Incident` has many `IncidentEvent`

## Árbol

```text
app/Features/Auth/Application
app/Features/Permissions
app/Features/Roles/Application
app/Features/Shared/Query
app/Features/Users/Application
app/Features/Notifications/Jobs
app/Features/Care/Application
app/Features/Care/Jobs
app/Features/Care/Mail
app/Http/Controllers/Admin
app/Http/Controllers/Api
app/Http/Controllers/Api/Auth
app/Http/Controllers/Dashboard
app/Http/Requests/Admin
app/Jobs/TestHorizonJob.php
routes/web.php
routes/api.php
routes/admin.php
routes/console.php
resources/views/{auth,users,roles,layouts,partials,components}
resources/js/{admin.js,app.js,shared,modules}
docker/
docs/
```

## Glosario

| Término | Significado |
| --- | --- |
| Headers | JSON de un index DataTable (`draw`, `recordsTotal`, `recordsFiltered`, `data`) |
| BanHttp | Cliente axios del panel (`resources/js/admin.js`) |
| nickname | Usuario de acceso, en minúsculas |

## Dónde está cada cosa

| Pieza | Ruta |
| --- | --- |
| Login web | Fortify + `resources/views/auth/login.blade.php` |
| Login API | `Api\Auth\LoginController` |
| Usuarios | `Features/Users` + `Dashboard\UserController` |
| Roles | `Features/Roles` + `Dashboard\RoleController` |
| Permisos | `Features/Permissions` |
| Consultas de listado | `Features/Shared/Query` |
| Cola de notificaciones | `Features/Notifications/Jobs/SendTestNotificationJob` y `Features/Care/Jobs/NotifyIncidentJob` |
| Cuidado | `Features/Care` + controllers `Api\ShiftController`, `PatientController`, `CareTaskController`, `CareLogController`, `HealthRecordController`, `IncidentController`, `CareReportController` |
| Geocerca | `config/care.php`. Vacío = desactivada |
| Empresa | `config/business.php` (`BUSINESS_NAME`, `BUSINESS_LEGAL_NAME`) |
