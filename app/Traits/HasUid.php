<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasUid
{
    public static function booted()
    {
        static::creating(function (Model $model) {
            $prop = $this->uid_attribute ?? 'uid';

            $model->{$prop} = Str::orderedUuid();
        });
    }
}
