<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'amount',
        'paid_on',
        'project_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount'  => 'integer',
        'paid_on' => 'date:Y-m-d'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'updated_at',
    ];

    public function scopeFilter(Builder $query, array $filters)
    {
        // Filter by `project_id`
        $query->when(
            $filters['project_id'] ?? false,
            fn (Builder $query, $project_id) =>
            $query->where(
                fn (Builder $query) =>
                $query->where('project_id', $project_id)
            )
        );

        // Filter by month
        $query->when(
            $filters['month'] ?? false,
            fn (Builder $query, $month) => $query->whereMonth('paid_on', $month)
        );

        // Filter by year
        $query->when(
            $filters['year'] ?? false,
            fn (Builder $query, $year) => $query->whereYear('paid_on', $year)
        );
    }

    /**
     * Parses payment date of the invoice and converts it into eye-friendly
     * format
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function prettyPaymentDate(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes) {
                $paid_on = new Carbon($attributes['paid_on']);
                return $paid_on->format('jS F Y');
            }
        );
    }

    /**
     * An invoice is created for a project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
