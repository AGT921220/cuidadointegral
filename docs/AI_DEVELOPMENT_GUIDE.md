# Guía de desarrollo — Cuidado Integral

## CRUD

Plantilla: usuarios.

1. `app/Features/Users/Application/CreateUser.php`
2. `app/Http/Controllers/Dashboard/UserController.php`
3. `app/Http/Requests/Admin/StoreUserRequest.php`
4. `resources/views/users/{index,create,edit,_form}.blade.php`
5. Rutas en `routes/web.php`

El controller autoriza, llama la Feature y redirige con flash. No consulta el listado.

## Listado

1. `SearchUserHeaders` + `BuilderFilter::paginate`
2. `Admin\UserHeadersController` (offset, limit, draw, lista blanca de `order_by`)
3. `resources/js/modules/users/index.js` con `createDataTable`
4. La vista deja el `<tbody>` vacío y pone `data-url`

El `name` de la columna JS es la columna SQL (`users.name`), no la etiqueta visible.

## Permiso

1. Constante en `PermissionTypes`
2. `PermissionHandler::getAllPermissions`, traducción y `getModuleLabels`
3. `CreateRolesCommand` si el rol Admin debe tenerlo
4. `permissions:create` y `roles:create`
5. `authorize()` y `@can`

## Feature

Una clase, un caso. `__invoke`. Eloquent dentro. Si hay que rechazar el caso, `ValidationException::withMessages`.

## Job

`app/Features/Notifications/Jobs/SendTestNotificationJob.php`.

```php
SendTestNotificationJob::dispatch('hola');
```

La clase ya llama `onQueue('notifications')`. Horizon la consume. Prueba de la cola default: `App\Jobs\TestHorizonJob::dispatch()`.

## API

`routes/api.php`. Controller en `app/Http/Controllers/Api`. JSON plano. Reutiliza la Feature.

Login:

```http
POST /api/auth/login
{ "nickname": "admin", "password": "admin", "device_name": "pixel" }
```

## Cuidado

Plantilla: `CheckInShift` y `ShiftController`.

1. Permiso en `PermissionTypes` y en `PermissionHandler::permissionsForRole`.
2. La Feature llama `CareScope` antes de leer o escribir. El paciente sale del turno, no de un id suelto.
3. Varias filas relacionadas van en `DB::transaction`.
4. Ruta dentro del grupo `auth:sanctum` de `routes/api.php`.
5. Prueba en `tests/Feature/Care/CareApiTest.php`.

Entrada y salida guardan hora y, si llegan, latitud, longitud y precisión. Cada tarea y cada incidencia dejan historial. El reporte pagina con `limit` y `offset`.

## Test

`tests/Feature/Users/UserCrudTest.php`.

```bash
docker exec php-cint php artisan test
```

## Docker

```bash
make up
docker exec php-cint php artisan migrate
docker exec php-cint php artisan permissions:create
docker exec php-cint php artisan roles:create
docker exec php-cint php artisan initial-setup
```

Tras cambiar código de colas: `make clear`.
