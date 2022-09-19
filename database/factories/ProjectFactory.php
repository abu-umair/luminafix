<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name'          => ucfirst($this->faker->words(random_int(2, 5), true)),
            'progress'      => random_int(0, 100),
            'budget'        => random_int(100, 10000),
            'phase'         => ucfirst($this->faker->sentences(random_int(1, 3), true)),
            'status'        => $this->faker->randomElement(array_keys(config('lumina.project.status'))),
            'workload'      => random_int(0, 100),
            'concept'       => random_int(0, 100),
            'development'   => random_int(0, 100),
            'documentation' => random_int(0, 100),
            'commissioning' => random_int(0, 100),
            'leader_id'     => User::factory()
        ];
    }
}
