<?php

namespace Database\Seeders;

use App\Models\InspectionType;
use Illuminate\Database\Seeder;

class InspectionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $option = [
            [
                'name' => 'in-house',
                'description' => 'In-House',
            ],
            [
                'name' => 'third-party',
                'description' => 'Third Party',
            ],
        ];

        foreach ($option as $option) {
            if (InspectionType::where('name', $option['name'])->exists()) {
                continue;
            }
            InspectionType::create($option);
        }

    }
}
