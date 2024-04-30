<?php

namespace Database\Seeders;

use App\Models\PermitType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $options = [
            [
                'name' => 'Internal',
                'description' => 'Internal',
            ],
            [
                'name' => 'Contractor',
                'description' => 'Contractor',
            ],
        ];

        foreach ($options as $option) {
            if (PermitType::where('name', $option['name'])->exists()) {
                continue;
            }
            PermitType::create($option);
        }

    }
}
