<?php

namespace App\Models;

use DateTimeInterface;
use App\Traits\HasStatuses;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, InteractsWithMedia, HasStatuses, SoftDeletes;

    public $table = 'projects';

    public $casts = [
        'tags' => 'array'
    ];

    public $appends = [
        'attached_files',
        'status'
    ];

    public const ATTACHED_FILES_MEDIA_COLLECTION = "pieces_jointes";

    public const STATUS_PENDING = "pending";
    public const STATUS_STARTED = "started";
    public const STATUS_FINISHED = "finished";

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_STARTED,
        self::STATUS_FINISHED,
    ];

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'tags',
        'created_by_id',
    ];

    public function getAttachedFilesAttribute()
    {
      return $this->media()->where('collection_name', self::ATTACHED_FILES_MEDIA_COLLECTION)->get();
    }

    public function getStatusAttribute()
    {
      return $this->status()?->name;
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('panel.datetime_format'));
    }
}
