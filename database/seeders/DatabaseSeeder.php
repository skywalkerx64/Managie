<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AppConfigurationSeeder::class,
            RoleSeeder::class,
            UserSeeder::class,
            UserRoleSeeder::class,
            PermissionSeeder::class,
            PermissionRoleSeeder::class,
            UserPermissionSeeder::class,
        ]);

        if(env('APP_ENV') == "local")
        {
            $this->call([
                PostCategorySeeder::class,
                PostSeeder::class,
            ]);
        }
    }
}
