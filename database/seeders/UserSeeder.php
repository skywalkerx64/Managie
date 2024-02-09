<?php

namespace Database\Seeders;

use App\Http\Controllers\Auth\AuthController;
use App\Models\Role;
use App\Models\User;
use App\Models\Secteur;
use Illuminate\Support\Str;
use Faker\Generator as Faker;
use Illuminate\Database\Seeder;
use Faker\Provider\fr_FR\Person;
use Faker\Provider\fr_FR\Address;
use Faker\Provider\fr_FR\Company;
use Illuminate\Support\Facades\Hash;
use Faker\Provider\fr_FR\PhoneNumber;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(Faker $faker): void
    {
        $faker->locale = 'fr_FR';

        // Admins
        $admin_users = [
            [
                "firstname" => "John",
                "lastname" => "Doe",
                "email" => "admin@managie.app",
                'password' => Hash::make('password'),
                'can_login' => true,
                'email_verified_at' => now(),

            ],
        ];

        $this->createAdmins($admin_users);

        $manager_users = [
            [
                "firstname" => "Jean",
                "lastname" => "Doe",
                "email" => "restauration@ptdlc.bj",
                'password' => Hash::make('Admin@11'),
                'email_verified_at' => now(),
                'identity' => AuthController::generate_unique_identity(),
                'can_login' => true,
            ],
        ];

        $this->createManagers($manager_users);

        $collaborater_users = [
            [
                "firstname" => "Lucas",
                "lastname" => "Doe",
                "email" => "collaborateur@managie.app",
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'can_login' => true,
            ],
        ];

        $this->createCollaboraters($collaborater_users);


    }

    public function createAdmins(array $data) : array
    {
        $users = [];
        $admin_role = Role::where('alias', Role::ADMIN_ROLE_ALIAS)->first();

        foreach($data as $user_data)
        {
            $user = User::create($user_data);

            $user->roles()->attach($admin_role->id);
            $users[] = $user;
        }
        return $users;
    }

    public function createCollaboraters(array $data) : array
    {
        $users = [];
        $collaborater_role = Role::where('alias', Role::COLLABORATER_ROLE_ALIAS)->first();

        foreach($data as $user_data)
        {
            $user = User::create($user_data);

            $user->roles()->attach($collaborater_role->id);
            $users[] = $user;
        }
        return $users;
    }
    public function createManagers(array $data) : array
    {
        $users = [];
        $admin_role = Role::where('alias', Role::MANAGER_ROLE_ALIAS)->first();

        foreach($data as $user_data)
        {
            $user = User::create($user_data);

            $user->roles()->attach($admin_role->id);
            $users[] = $user;
        }
        return $users;
    }
}
