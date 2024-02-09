<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    public const ADMIN_ROLE_ALIAS = "A";
    public const MANAGER_ROLE_ALIAS = "MAN";
    public const COLLABORATER_ROLE_ALIAS = "COL";

    public const ROLE_ALIASES = [
        self::ADMIN_ROLE_ALIAS,
    ];

    public const ADMINS_ROLE_ALIASES = [
        self::ADMIN_ROLE_ALIAS,
        self::MANAGER_ROLE_ALIAS,
    ];
    public $table = 'roles';

    protected $fillable = [
        'title',
        'description',
        'alias',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, "permission_role")->withPivot(['is_active']);
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('panel.datetime_format'));
    }
}
