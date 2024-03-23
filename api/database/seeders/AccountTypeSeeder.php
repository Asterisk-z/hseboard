<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $types = [
            [
                'name' => 'corporate',
                'description' => 'Corporate Account',
            ],
            [
                'name' => 'individual',
                'description' => 'individual Account',
            ],
        ];

        foreach ($types as $type) {
            if (AccountType::where('name', $type['name'])->exists()) {
                continue;
            }
            AccountType::create($type);
        }
    }
}
