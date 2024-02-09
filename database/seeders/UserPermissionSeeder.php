<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
        $users = User::all();
        foreach($users as $user)
        {
            $user_permission_ids = $user->roles()->with('permissions')->get()->pluck('permissions.*.id')->flatten();
            // dd($user_permission_ids);
            $user->permissions()->attach($user_permission_ids);
        }
    }
}
