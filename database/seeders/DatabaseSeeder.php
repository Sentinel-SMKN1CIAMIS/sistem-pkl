<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Essential seeders (run in all environments, including production)
        $this->call([
            DepartmentSeeder::class,
            UserSeeder::class,
        ]);

        // Development dummy seeders
        if (app()->environment('local', 'testing', 'development')) {
            $this->call([
                DummyDataSeeder::class,
            ]);
        }
    }
}
