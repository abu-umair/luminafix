<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'id_project_firebase',
        'progress',
        'budget',
        'phase',
        'status',
        'workload',
        'concept',
        'development',
        'documentation',
        'commissioning',
        'leader_id',
        'totalinvoice'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'pivot',
        'leader_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'progress'      => 'integer',
        'budget'        => 'integer',
        'workload'      => 'integer',
        'concept'       => 'integer',
        'development'   => 'integer',
        'documentation' => 'integer',
        'commissioning' => 'integer',
        'created_at'    => 'datetime:U',
        'updated_at'    => 'datetime:U',
    ];

    /**
     * Accumulates all paid invoices for the project.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    public function paidInvoices(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $this->invoices->reduce(function (int $carry, Invoice $invoice) {
                    return $carry + $invoice->amount;
                }, 0);
            }
        );
    }

    /**
     * A project has many invoices
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Every project has one (belongs to) project `leader`
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    /**
     * Every person (User) involved in a project is counted as its participant
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function participants(): BelongsToMany
    {
        return $this
            ->belongsToMany(User::class, 'project_members')
            ->withPivot('role');
    }

    /**
     * Participants who have the `member` role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function members(): BelongsToMany
    {
        return $this
            ->participants()
            ->wherePivot('role', 'member');
    }

    /**
     * Participants who have the `BA` role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bas(): BelongsToMany
    {
        return $this
            ->participants()
            ->wherePivot('role', 'ba');
    }

    /**
     * Participants who have the `LD` role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function lds(): BelongsToMany
    {
        return $this
            ->participants()
            ->wherePivot('role', 'ld');
    }

    /**
     * Participants who have the `DA` role
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function das(): BelongsToMany
    {
        return $this
            ->participants()
            ->wherePivot('role', 'da');
    }

    public function scopeFilter(Builder $query, array $filters)
    {

        // Filter by `status`
        $query->when(
            $filters['status'] ?? false,
            fn (Builder $query, $status) =>
            $query->where(
                fn (Builder $query) =>
                $query->where('status', $status)
            )
        );

        // Filter by `progress`
        // $query->when(
        //     $filters['progress'] ?? false,
        //     fn (Builder $query, $progress) => $query->where('progress', $progress)
        // );
        $query->when(
            $filters['minProgress'] ?? false,
            fn (Builder $query, $minProgress) => 
            $query->when($filters['maxProgress'] ?? false,
                    fn (Builder $query, $maxProgress)  => 
                    $query->whereBetween('progress', [$minProgress,$maxProgress])
            )
        );

        // Filter by `invoices`
        $query->when(
            $filters['minPaidInvoices'] ?? false,
            fn (Builder $query, $minPaidInvoices) => 
            $query->when($filters['maxPaidInvoices'] ?? false,
                    fn (Builder $query, $maxPaidInvoices)  => 
                    $query->whereBetween('totalinvoice', [$minPaidInvoices,$maxPaidInvoices])
            )
        );

        // Filter by `invoices`
        // $query->when(
        //     $filters['minPaidInvoices'] ?? false,
        //     fn (Builder $query, $minPaidInvoices) => $query->where('totalinvoice', $minPaidInvoices)

        //fn (Builder $query, $minPaidInvoices, $maxPaidInvoices) => $query->whereBetween('totalinvoice', [$minPaidInvoices,$maxPaidInvoices])
        // );
        
        
    }
}
