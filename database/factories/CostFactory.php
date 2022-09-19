<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cost>
 */
class CostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $billing_month = sprintf(
            '%s-01',
            $this
                ->faker
                ->dateTimeBetween('-1 year', 'now')
                ->format('Y-m')
        );

        return [
            'billing_month' => $billing_month,
            'amount'        => random_int(100, 10000),
            'balance'       => random_int(100, 10000),
        ];
    }
}
