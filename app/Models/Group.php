<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    public $table = 'groups';

    public const USERS_GROUP_TITLE = "Utilisateurs";
    public const INTERN_USERS_GROUP_TITLE = "Utilisateurs Internes";
    public const EXTERN_USERS_GROUP_TITLE = "Utilisateurs Externes";
    public const ADMINS_GROUP_TITLE = "Administrateurs";
    public const INTERN_ADMINS_GROUP_TITLE = "Administrateurs Internes";
    public const EXTERN_ADMINS_GROUP_TITLE = "Administrateurs Externes";

    public const GROUP_TITLES = [
        self::USERS_GROUP_TITLE,
        self::INTERN_USERS_GROUP_TITLE,
        self::EXTERN_ADMINS_GROUP_TITLE,
        self::ADMINS_GROUP_TITLE,
        self::INTERN_ADMINS_GROUP_TITLE,
        self::EXTERN_ADMINS_GROUP_TITLE
    ];

    protected $fillable = [
        'title',
        'description',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('panel.datetime_format'));
    }
}
