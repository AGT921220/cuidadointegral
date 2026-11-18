# Decisiones de arquitectura — Cuidado Integral

## ADR-001 — Features invocables

Estado: activo.

El negocio vive en `app/Features/{Modulo}/Application` con clases `__invoke`. Controllers, FormRequests y modelos se quedan en las carpetas de Laravel. No hay repositorios.

## ADR-002 — Panel y API separados

Estado: activo.

El panel usa sesión Fortify (`nickname`, home `/dashboard`). La app usa Sanctum (`POST /api/auth/login` con `device_name`). No hay `/api/v1` ni API Resources.

El registro público está apagado. Los usuarios se crean en el CRUD.

## ADR-003 — DataTables server-side

Estado: activo.

Todo index usa `Search*Headers`, `BuilderFilter::paginate` y `createDataTable`. El HTML de acciones se arma en el JS. El controller de headers solo acepta columnas de una lista blanca.

## ADR-004 — Permisos Spatie sin Policies

Estado: activo.

Constantes en `PermissionTypes`. Alta por `permissions:create` y `roles:create`. Autorización en FormRequest, controller y `@can`.

Roles de arranque: `Admin` y `Usuario`.

## ADR-005 — Redis y Horizon

Estado: activo.

Colas `default` y `notifications`. Horizon solo para el rol `Admin`. El scheduler despacha jobs; no ejecuta trabajo pesado en línea. Tests usan cola `sync`.

## ADR-006 — Docker como entorno

Estado: activo.

PHP 8.4-fpm, MySQL 8.4 y Redis 8 en Compose. Staging no incluye MySQL y usa la red externa `appnetwork`. El contenedor PHP incluye `pdo_sqlite` para PHPUnit.

Puertos de host: 8094 (nginx), 4334 (MySQL), 6384 (Redis), 8095 (phpMyAdmin).

## ADR-007 — Dominio de cuidado en la API

Estado: activo.

Pacientes, vínculos, turnos, tareas, bitácora, signos y incidencias viven en `app/Features/Care`. No hay Policies ni pantallas. La autorización combina el permiso Spatie con `CareScope`.

La geocerca es opcional (`CARE_GEOFENCE_METERS`). La ubicación se guarda en el turno y en `shift_locations`. No hay proveedor de mapas.

Una incidencia se notifica con el mailer configurado y un log en la cola `notifications`. No hay canal push, SMS ni adjuntos. No hay sincronización offline.
