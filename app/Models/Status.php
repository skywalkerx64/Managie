<?php

namespace App\Models;

use \DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\ModelStatus\Status as ModelStatusStatus;

class Status extends ModelStatusStatus
{
    use HasFactory;



    protected $guarded = [];

    protected $table = 'statuses';

    public const COULEUR_STATUS = [
        'S1' => 'grey',
        'S2' => 'green',
        'S3' => 'red',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format(config('panel.datetime_format'));
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public function __toString(): string
    {
        return $this->name;
    }


}





