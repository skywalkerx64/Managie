<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;

class Mail extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    const TYPE_COMMON = "common";
    const TYPE_INVESTISSEUR = "investisseur";
    const TYPE_SENT = 'sent';
    const TYPES = [
        self::TYPE_COMMON,
        self::TYPE_INVESTISSEUR,
        self::TYPE_SENT
    ];
    const FILES_COLLECTION_NAME = "mails_files";

    protected $casts = [
        "created_at" => 'datetime',
        "updated_at" => 'datetime',
        "deleted_at" => 'datetime',
        "cc" => 'array',
        "bcc" => 'array',
    ];

    protected $fillable = [
        "fullname",
        "subject",
        "object",
        "content",
        "email",
        "contact",
        "cc",
        "bcc",
        "type",
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('panel.datetime_format'));
    }
}
