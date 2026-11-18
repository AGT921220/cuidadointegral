# Cuidado Integral

Backend de la app móvil y panel de administración (usuarios, roles y login).

## Arranque local

```bash
make up
make install
docker exec php-cint php artisan key:generate
docker exec php-cint php artisan migrate
docker exec php-cint php artisan permissions:create
docker exec php-cint php artisan roles:create
docker exec php-cint php artisan initial-setup
npm install
npm run build
```

Panel: http://localhost:8094  
Usuario inicial: `admin` / `admin`  
phpMyAdmin: http://localhost:8095  
MySQL en el host: puerto `4334` (dentro de Docker el host es `mysql` y el puerto `3306`).

## Variables de Docker

`DB_HOST=mysql`, `REDIS_HOST=redis`, `QUEUE_CONNECTION=redis`, `CACHE_STORE=redis`.

Horizon usa Redis. No arranques `queue:work` al mismo tiempo. El dashboard está en `/horizon` y solo entra el rol Admin.

## Módulo nuevo

1. Migración y modelo con `$fillable` real.
2. `Create`, `Update`, `Delete`, `SearchHeaders` en `app/Features/{Modulo}/Application`.
3. Controller de páginas en `Dashboard` y headers en `Admin`.
4. Rutas en `routes/web.php`.
5. Vistas `index`, `create`, `edit`, `_form` y JS en `resources/js/modules/{modulo}/index.js`.
6. Permisos en `PermissionTypes`, `PermissionHandler` y `roles:create`.
7. Test Feature.

La plantilla es usuarios. La receta está en `docs/AI_DEVELOPMENT_GUIDE.md`.
