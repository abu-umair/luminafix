<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'updated_at' => 'date:U',
        'value'      => 'array',
    ];

    /**
     * Parses date of `updated_at` or last updated datetime and converts it
     * into eye-friendly value.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function lastUpdated(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes): int|null {
                $lastUpdated = new Carbon($attributes['updated_at']);
                return (int) $lastUpdated->format('jS M y \a\t g:i:s A');
            }
        );
    }
}
