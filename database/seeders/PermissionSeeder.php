<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
  public function run()
  {
    $everybody = Role::ROLE_ALIASES;
    $admins = Role::ADMINS_ROLE_ALIASES;

    $admin = Role::ADMIN_ROLE_ALIAS;
    $manager = Role::MANAGER_ROLE_ALIAS;
    $collaborater = Role::COLLABORATER_ROLE_ALIAS;

    $permissions = [

      ...$this->createPermissions('user_management', ['access', 'list'], $admins),

      ...$this->createPermissions('otp', ['can_use' => "Pouvoir utiliser la connexion OTP"], $everybody),
      ...$this->createPermissions('password', ['can_change' => "Pouvoir changer son mot de passe"], $everybody),

      ...$this->createPermissions('app_configuration', ['create', 'edit', 'delete', 'access'], $admins),
      ...$this->createPermissions('app_configuration', ['show', 'search', 'list'], $everybody),

      ...$this->createPermissions('permission', ['create', 'edit', 'delete', 'manage'], $admins),
      ...$this->createPermissions('permission', ['show', 'access', 'search', 'list'], $everybody),

      ...$this->createPermissions('role', ['create', 'edit', 'delete', 'manage'], $admins),
      ...$this->createPermissions('role', ['show', 'access', 'search', 'list'], $everybody),

      ...$this->createPermissions('user', ['create', 'edit', 'delete', 'history_access'], $admins),
      ...$this->createPermissions('user', ['show', 'access', 'search', 'list'], $everybody),

      ...$this->createPermissions('project', ['create', 'edit', 'delete'], $admins),
      ...$this->createPermissions('project', ['show', 'access', 'search', 'list'], $everybody),

      ...$this->createPermissions('mail', ['create'], $everybody),
      ...$this->createPermissions('mail', ['show' => "Voir le contenu du mail", 'edit' => "Editer le contenu du mail", 'delete' => "Supprimer le contenu du mail", 'access' => "Accès au contenu du mail", 'search' => "Rechercher un mail", 'list' => "Lister les mails"], $admins, "Mail"),

    ];

    Permission::insert($permissions);
  }

  public function createPermissions($resource, $permissions, $default_roles = [], $module = null)
  {
    $result = array_map(function ($permission, $description) use (&$resource, &$default_roles, &$module) {
      if(gettype($permission) == "integer")
      {
        $permission = $description;
        $description = null;
      }
      $item = [
        "title" => $resource . '_' . $permission,
        "resource" => $resource,
        "module" => $module,
        "description" => $description,
        "action" => $permission,
        "default_roles" => json_encode($default_roles)
      ];
      return $item;
    }, array_keys($permissions),   $permissions);

    return $result;
  }
}
