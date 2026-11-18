up:
	chmod -R a+rwX storage bootstrap/cache
	docker compose down
	docker compose up -d

install:
	docker exec php-cint composer install

enter:
	docker exec -it php-cint /bin/bash

nginx:
	docker exec -it nginx-cint /bin/sh

clear:
	docker exec php-cint php artisan optimize:clear
	docker exec php-cint php artisan config:cache
	docker exec php-cint php artisan route:cache
	docker exec php-cint php artisan horizon:terminate
	docker exec php-cint php artisan queue:restart
