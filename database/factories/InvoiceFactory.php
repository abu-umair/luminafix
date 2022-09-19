<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'amount'     => random_int(100, 1000),
            'paid_on'    => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'project_id' => Project::factory()
        ];
    }
}
