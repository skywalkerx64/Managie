<?php

namespace App\Models;

use DateTimeInterface;
use App\Traits\HasStatuses;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasStatuses, SoftDeletes;

    public $table = 'posts';

    public $casts = [
        'tags' => 'array'
    ];

    public $appends = [
        'cover',
        'attached_files',
        'status'
    ];

    public const ATTACHED_FILES_MEDIA_COLLECTION = "pieces_jointes";
    public const COVER_MEDIA_COLLECTION = "cover";

    public const BLOG_TYPE = "blog";
    public const STATS_TYPE = "stats";
    public const CONCOURS_TYPE = "concours";
    public const INVESTORS_TYPE = "investors";
    public const RULES_TYPE = "rules";

    public const TYPES = [
        self::BLOG_TYPE,
        self::STATS_TYPE,
        self::CONCOURS_TYPE,
        self::RULES_TYPE,
        self::INVESTORS_TYPE,
    ];

    public const STATUS_DRAFTED = "drafted";
    public const STATUS_PUBLISHED = "published";
    public const STATUS_DELETED = "deleted";

    public const STATUSES = [
        self::STATUS_DRAFTED,
        self::STATUS_PUBLISHED,
        self::STATUS_DELETED,
    ];

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'secteur_id',
        'post_category_id',
        'type',
        'tags',
        'link',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getCoverAttribute()
    {
      return $this->media()->where('collection_name', self::COVER_MEDIA_COLLECTION)->first()?->original_url;
    }

    public function getAttachedFilesAttribute()
    {
    //   return $this->media()->where('collection_name', self::ATTACHED_FILES_MEDIA_COLLECTION)->get()?->pluck('original_url');
      return $this->media()->where('collection_name', self::ATTACHED_FILES_MEDIA_COLLECTION)->get();
    }

    public function post_category()
    {
        return $this->belongsTo(PostCategory::class);
    }

    public function getStatusAttribute()
    {
      return $this->status()?->name;
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('panel.datetime_format'));
    }
}
