<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UsersSeeder::class,
            ProjectsSeeder::class,
            CostsSeeder::class
        ]);

        Setting::firstOrCreate(
            ['key' => 'variables'],
            // If not created yet then create using default values
            ['value' => config('lumina.variables.default')]
        );
    }
}
