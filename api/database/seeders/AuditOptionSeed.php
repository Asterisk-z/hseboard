<?php

namespace Database\Seeders;

use App\Models\AuditOption;
use Illuminate\Database\Seeder;

class AuditOptionSeed extends Seeder
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
                'name' => 'internal',
                'description' => 'Internal',
            ],
            [
                'name' => 'external',
                'description' => 'External',
            ],
        ];

        foreach ($option as $option) {
            if (AuditOption::where('name', $option['name'])->exists()) {
                continue;
            }
            AuditOption::create($option);
        }

    }
}
