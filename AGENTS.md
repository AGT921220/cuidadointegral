# Cuidado Integral

Backend de la app móvil y panel de administración. El repositorio es la fuente de verdad. El código reciente que cumple estas reglas gana sobre un patrón genérico de Laravel. El legacy que las viole no es plantilla.

## Reglas

1. PHP 8.4, Laravel 13, MySQL 8.4, Redis 8, Horizon, Fortify, Sanctum, Spatie Permission.
2. Panel: Blade, Tabler, Bootstrap 5, jQuery, Vite. Sin React, Vue, Livewire ni Inertia.
3. Negocio en `app/Features/{Modulo}/Application` (clases `__invoke`). Sin repositorios, DTOs ni Policies.
4. `Domain/` e `Infrastructure/` solo si hay una regla o un servicio externo real.
5. Controllers delgados. El panel valida en `app/Http/Requests/Admin`. La API valida en el controller. Eloquent directo en la Feature.
6. Páginas CRUD en `Dashboard` y `routes/web.php`. JSON de tablas en `Admin\*HeadersController`. API móvil en `routes/api.php`.
7. No registres resources nuevos en `routes/admin.php`.
8. El index es DataTable server-side: `Search*Headers` + `BuilderFilter::paginate` + `createDataTable`. Sin paginator Blade ni `@forelse`.
9. Permisos solo como constantes de `PermissionTypes`. El servidor autoriza siempre; `@can` es solo la interfaz.
10. Roles canónicos: `Admin`, `Usuario`, `Cuidador`, `Familiar` y `Supervisor`. No se renombran ni se borran. `Admin` tampoco cambia de permisos desde el panel.
11. Login web: Fortify, usuario `nickname`, home `/dashboard`. Registro público apagado.
12. Login móvil: `POST /api/auth/login` exige `nickname`, `password` y `device_name`. Entran los roles de `RoleTypes::mobile()`.
13. `User`: `HasApiTokens`, `HasRoles`, `SoftDeletes`. `nickname` en minúsculas.
14. Colas: Redis + Horizon. Colas `default` y `notifications`. No corras `queue:work` junto a Horizon.
15. Jobs en `app/Features/{Modulo}/Jobs`. `TestHorizonJob` queda en `app/Jobs`.
16. Scheduler: `routes/console.php`. El contenedor ya corre `schedule:work`.
17. Setup por comandos, en orden: `permissions:create`, `roles:create`, `initial-setup`.
18. Tests: PHPUnit, sqlite en memoria, cola `sync`. Un Feature test por CRUD.
19. Código simple. Evita `else`: guard clauses y return temprano. SOLID sin capas de más.
20. No inventes módulos, columnas ni pantallas. `$fillable` y relaciones salen de la migración.
21. No uses `env()` dentro de `app/`. No expongas secretos en docs.
22. Idioma de la UI y de las respuestas: español.
23. Antes de implementar: módulo → regla `.cursor/rules` → código equivalente en este repo (usuarios para CRUD, usuarios/roles para DataTables) → cambio mínimo → tests.
24. Actualiza `AGENTS.md`, la regla del módulo y `docs/AI_*` solo si cambia arquitectura, dominio, colas, Docker, API o permisos.
25. Artisan, migraciones y tests del día a día corren dentro de Docker: `docker exec php-cint php artisan ...`.
26. Cuidado vive en `app/Features/Care`. El alcance lo decide `CareScope` con la asignación real. No uses el id que manda el cliente para abrir un paciente, turno o familiar.
27. Geocerca solo si `config/care.php` tiene metros (`CARE_GEOFENCE_METERS`). Vacío significa que no hay límite.
28. Una incidencia avisa por el mailer ya configurado y deja un log en la cola `notifications`. No agregues push, SMS ni adjuntos si el repo no los tiene.
29. La API de cuidado responde `{ data }` o `{ data, meta: { total, limit, offset } }`.
30. No hay sincronización offline ni pantallas de cuidado.
