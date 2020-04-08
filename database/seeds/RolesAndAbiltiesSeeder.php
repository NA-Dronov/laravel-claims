<?php

use App\Models\Ability;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesAndAbiltiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $manager = Role::create([
            'name' => 'manager',
            'description' => 'Менеджер'
        ]);

        $user = Role::create([
            'name' => 'client',
            'description' => 'Клиент'
        ]);

        $assignClaim = Ability::create([
            'name' => 'assign_claim',
            'description' => 'Взять завяку на выполнение'
        ]);

        $createClaim = Ability::create([
            'name' => 'create_claim',
            'description' => 'Создать завяку'
        ]);

        $manager->allowTo($assignClaim);
        $user->allowTo($createClaim);
    }
}
