<?php

namespace Database\Seeders;

use Exception;
use App\Models\User;
use App\Models\Project;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ProjectsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Project::factory()
            ->count(random_int(5, 15))
            ->hasInvoices(random_int(3, 12))
            ->create()
            ->each(function (Project $project) {
                $project
                    ->bas()
                    ->syncWithPivotValues(
                        $this->getRandomUserIDs(random_int(2, User::count())),
                        ['role' => 'ba']
                    );

                $project
                    ->das()
                    ->syncWithPivotValues(
                        $this->getRandomUserIDs(random_int(2, User::count())),
                        ['role' => 'da']
                    );

                $project
                    ->lds()
                    ->syncWithPivotValues(
                        $this->getRandomUserIDs(random_int(2, User::count())),
                        ['role' => 'ld']
                    );
            });
    }

    private function getRandomUserIDs(int $count = 5): array
    {
        $ids = new Collection();
        $users = User::all();

        if (count($users) < $count)
            throw new Exception('There are not enough users please create more or reduce the count!');

        while (count($ids) < $count) : // Fill until required
            $ids->push($users->random()->id);

            $ids = $ids->unique(); // Remove duplicates
        endwhile;

        return $ids->toArray();
    }
}
