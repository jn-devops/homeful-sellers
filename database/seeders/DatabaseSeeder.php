<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Homeful\Properties\Models\Project;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Module;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        
        // User::factory(10)->create();
        Module::factory()
        ->create([
            'name' => 'Modules v2',
            'description' => 'All Access',
            'slug' => 'modules',
            'add' => true,
            'edit' => true,
            'delete' => true,
            'view' => true,
            'import' => true,
            'export' => true
        ]
        ); Module::factory()
        ->create([
            'name' => 'Roles v2',
            'description' => 'All Access',
            'slug' => 'roles',
            'add' => true,
            'edit' => true,
            'delete' => true,
            'view' => true,
            'import' => true,
            'export' => true
        ]
        ); Module::factory()
        ->create([
            'name' => 'Users v2',
            'description' => 'All Access',
            'slug' => 'users',
            'add' => true,
            'edit' => true,
            'delete' => true,
            'view' => true,
            'import' => true,
            'export' => true
        ]
        );
        Module::factory()
        ->create([
            'name' => 'Config v2',
            'description' => 'All Access',
            'slug' => 'config',
            'add' => true,
            'edit' => true,
            'delete' => true,
            'view' => true,
            'import' => true,
            'export' => true
        ]
        );
        $modules = Module::all();
        // $module_ids[]=Null;
        foreach ($modules as $module) {
            $module_ids[]=$module->id;
        }
        $userRole = UserRole::factory()
        ->create([
                'name' => 'Super Admin 2',
                'description' => 'All Access',
                'module_id' => $module_ids
        ]);

        // dd($userRole->id);
        $user = User::factory()
//            ->hasAttached(Project::factory(50)->count(50), ['seller_commission_code' => fake()->domainWord()])
            ->create([
            // 'name' => "Super Admin", 
            // 'email' => "admin@admin.ph",
            // 'seller_id' => 'admin123',
            // 'seller_commission_code' => 'admin123',
            // 'password' => "Jng@2025",
            // 'user_role_id' => $userRole->id,
            // 'contact' => Null

            'name' => 'Super Admin Admin',
            'first_name' => 'Super',
            'middle_name' => 'Admin',
            'last_name' => 'Admin',
            'birthdate' => '2000-10-10 12:00:00',
            'password' => 'Jng@2025', 
            'email' => 'admin2@admin.ph',
            'seller_code' => '',
            'contact' => '09122333111',
            'seller_commission_code' => 'S-000000000001',
            'user_role_id' => $userRole->id,//added ggvivar 
            'status' => 1,//added ggvivar
            'change_password' => 1,//added ggvivar
            'active' => 1
        ]);
        //        $i = 0;
        // Project::factory(50)
        //     ->create()
        //     ->each(function ($project) use ($user) {
        //         $user->projects()->attach($project, [
        //             'seller_commission_code' => fake()->domainWord()
        //         ]);
        //     });
    }
}
