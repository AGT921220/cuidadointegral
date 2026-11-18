<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // El alta inicial es por comandos, en este orden:
        // php artisan permissions:create
        // php artisan roles:create
        // php artisan initial-setup
    }
}
