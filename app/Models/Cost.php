<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use App\Traits\CalculatesFeeIndex;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cost extends Model
{
    use HasFactory, CalculatesFeeIndex;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'amount',
        'balance',
        'billing_month',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount'        => 'integer',
        'balance'       => 'integer',
        'billing_month' => 'date:U',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<string>
     */
    protected $hidden = [
        'updated_at',
    ];

    /**
     * Creates a new cost for current month an year (un-persisted)
     */
    public static function currentMonth(): self
    {
        return new self(['billing_month' => sprintf('%s-%s-01', date('y'), date('m'))]);
    }

    /**
     * Applies `month` and `year` filters (if passed) or applies current month
     * and year.
     *
     * @param \Illuminate\Database\Eloquent\Builder  $query
     * @param array<string, mixed>  $filters
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $query, array $filters)
    {
        // Filter by year
        $query->when(
            $filters['year'] ?? false,
            fn (Builder $query, $year) => $query->whereYear('billing_month', $year)
        );

        // Filter by month
        $query->when(
            $filters['month'] ?? false,
            fn (Builder $query, $month) => $query->whereMonth('billing_month', $month)
        );

        return $query;
    }

    /**
     * Parses billing month of the cost and converts it into eye-friendly
     * format.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function prettyMonth(): Attribute
    {
        return Attribute::make(
            get: function ($value, $attributes): string {
                $month = new Carbon($attributes['billing_month']);
                return $month->format('M Y');
            }
        );
    }

    /**
     * Parses the `billing_month` attribute and extracts month number from it.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function month(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes): int {
                return (int) $this->getMonthAndYear()['month'];
            }
        );
    }

    /**
     * Parses the `billing_month` attribute and extracts year from it.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function year(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes): int {
                return (int) $this->getMonthAndYear()['year'];
            }
        );
    }

    /**
     * Checks wether the calculations can be performed on current cost or not
     * by making sure the month is greater then March.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function isCalculable(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): bool {
                return $this->month >= 4;
            }
        );
    }

    /**
     * Checks if the cost is created for first month? (April)
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function isFirstMonth(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): bool {
                return $this->month === 4;
            }
        );
    }

    /**
     * Checks if the cost is created for last month? (December)
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function isLastMonth(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): bool {
                return $this->month() === 12;
            }
        );
    }

    /**
     * Gets the last month record. (if exists)
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function lastMonth(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): Cost|null {
                return Cost::whereYear('billing_month', $this->year)
                    ->whereMonth('billing_month', $this->month - 1)
                    ->first();
            }
        );
    }

    /**
     * Gets the next month record. (if exists)
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function nextMonth(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): Cost|null {
                return Cost::whereYear('billing_month', $this->year)
                    ->whereMonth('billing_month', $this->month + 1)
                    ->first();
            }
        );
    }

    /**
     * Gets the invoices created for the month of current cost.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function invoices(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): Collection {
                // Get all invoices of costs's month
                return Invoice::whereMonth('paid_on', $this->month)
                    ->whereYear('paid_on', $this->year)
                    ->get();
            }
        );
    }

    /**
     * Gets sum of all paid invoices in cost's month.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function inv(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): int {
                return (int) $this
                    ->invoices
                    ->reduce(
                        fn (int $carry, Invoice $invoice) => $carry + $invoice->amount,
                        0
                    );
            }
        );
    }

    /**
     * Gets the `amount` field of current cost (if exists) or gets the
     * `balance` field of last month's cost.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function cost(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): int {
                return $attributes['amount'] ?? (int) $this->lastMonth->balance;
            }
        );
    }

    /**
     * Gets last 3 months of costs for current one.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function previousMonths(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): Collection {
                return Cost::whereYear('billing_month', $this->year)
                    ->where(
                        fn (Builder $builder) => $builder
                            ->whereMonth('billing_month', '<', $this->month)
                            ->whereMonth('billing_month', '>', $this->month - 4)
                    )
                    ->orderBy('billing_month', 'asc')
                    ->limit(3)
                    ->get();
            }
        );
    }

    /**
     * Accumulates and returns the average `INV` of past 3 months.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function averageInv(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): int {
                $previousRecords = $this->previousMonths;

                $inv = (int) $previousRecords->reduce(
                    fn (int $carry, Cost $cost) => $carry + $cost->inv,
                    0
                ) ?: 0;

                // Take average by dividing with total number of costs or by one
                // (to prevent division by zero error)
                return intval(round($inv / ($previousRecords->count() ?: 1), 0));
            }
        );
    }

    /**
     * Accumulates and returns the average `COST` of past 3 months.
     *
     * @return \Illuminate\Database\Eloquent\Casts\Attribute
     */
    protected function averageCost(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): int {
                $previousRecords = $this->previousMonths;

                $cost = (int) $previousRecords->reduce(
                    fn (int $carry, Cost $cost) => $carry + $cost->cost,
                    0
                ) ?: 0;

                // Take average by dividing with total number of costs or by one
                // (to prevent division by zero error)
                return intval(round($cost / ($previousRecords->count() ?: 1), 0));
            }
        );
    }

    // protected function

    // month           Int
    // year            Int
    //
    // isFirstMonth       Bool
    // isLastMonth        Bool
    // previousMonth      Cost?
    // nextMonth          Cost?
    // isCalculable       Bool
    //
    //
    // invoices            Invoice[]
    // inv                 Int
    // cost                Int
    //
    // previousMonths      Cost[]
    //
    // averageInv          Int
    // averageCost         Int
}
