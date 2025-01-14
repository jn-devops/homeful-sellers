<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Homeful\Properties\Models\Project;
use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()
//            ->hasAttached(Project::factory(50)->count(50), ['seller_commission_code' => fake()->domainWord()])
            ->create([
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'seller_commission_code' => 'AA-317'
        ]);

//        $i = 0;
        Project::factory(50)
            ->create()
            ->each(function ($project) use ($user) {
                $user->projects()->attach($project, [
                    'seller_commission_code' => fake()->domainWord()
                ]);
            });
    }
}
