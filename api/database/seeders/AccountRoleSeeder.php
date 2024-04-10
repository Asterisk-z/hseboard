<?php

namespace Database\Seeders;

use App\Models\AccountRole;
use Illuminate\Database\Seeder;

class AccountRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $roles = [
            [
                'name' => 'member',
                'description' => 'Team member',
            ],
            [
                'name' => 'supervisor',
                'description' => 'HSE Supervisor',
            ],
            [
                'name' => 'manager',
                'description' => 'HSE Manager',
            ],
        ];

        foreach ($roles as $role) {
            if (AccountRole::where('name', $role['name'])->exists()) {
                continue;
            }
            AccountRole::create($role);
        }

    }
}
