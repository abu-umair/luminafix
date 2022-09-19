<?php

namespace Database\Seeders;

use App\Models\Cost;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CostsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cost::factory()
            ->count(random_int(12, 15)) // Must have records for current year
            ->make()
            ->each(function (Cost $cost, int $index) {
                // No record for current month!
                $cost->billing_month = now()->subMonth($index + 1)->format('Y-m-01');
                $cost->save();
            });
    }
}
