<?php

namespace Database\Seeders;

use App\Models\PermitRequestType;
use Illuminate\Database\Seeder;

class PermitRequestTypeSeeder extends Seeder
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
                'name' => 'hot_work',
                'description' => 'Hot Work Permit',
            ],
            [
                'name' => 'excavation',
                'description' => 'Excavation Permit',
            ],
            [
                'name' => 'confined_space_entry',
                'description' => 'Confined Space Entry Permit',
            ],
            [
                'name' => 'electrical_work',
                'description' => 'Electrical Work Permit',
            ],
            [
                'name' => 'work_at_height',
                'description' => 'Work at Height Permit',
            ],
            [
                'name' => 'general',
                'description' => 'General Work  Permit',
            ],
        ];

        foreach ($options as $option) {
            if (PermitRequestType::where('name', $option['name'])->exists()) {
                continue;
            }
            PermitRequestType::create($option);
        }
    }
}
