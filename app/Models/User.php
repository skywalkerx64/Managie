<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Role;
use DateTimeInterface;
use App\Models\Permission;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, InteractsWithMedia;

    public const CAN_USE_OTP_CONF = 'login.configuration.enable_otp';

    public $everybody = Role::ROLE_ALIASES;
    public $admins = Role::ADMINS_ROLE_ALIASES;

    public const AVATAR_COLLECTION_NAME = "avatar";

    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'is_active',
        'can_login',
        'otp',
        'identity',
        'email_verified_at'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = [
      'fullname',
      'avatar',
  ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'otp' => 'hashed',
    ];

    public function getAvatarAttribute()
    {
      return $this->getFirstMedia(User::AVATAR_COLLECTION_NAME);
    }

    public function roles()
    {
      return $this->belongsToMany(Role::class);
    }
  
    public function permissions()
    {
      return $this->belongsToMany(Permission::class, 'permission_user')
        ->withPivot('is_active')
        ->withTimestamps();
    }

    public function getFullnameAttribute()
    {
      return $this->firstname . ' ' . $this->lastname;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
      return $date->format(config('panel.datetime_format'));
    }

    public function HasRoles(array $roles)
    {
      if(!empty(array_intersect($this->roles->pluck('alias')->toArray(), $roles)))
      {
        return true;
      }
      return false;
    }
}
