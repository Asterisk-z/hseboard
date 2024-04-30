<?php

namespace Database\Seeders;

use App\Models\InspectionTemplateType;
use Illuminate\Database\Seeder;

class InspectionTemplateTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $audit_types = [
            [
                'name' => 'inspection_type_one',
                'sample' => '/public/sample/one.xlsx',
                'description' => 'Inspection Type One',
            ],
            [
                'name' => 'inspection_type_two',
                'sample' => '/public/sample/two.xlsx',
                'description' => 'Inspection Type Two',
            ],
            [
                'name' => 'inspection_type_three',
                'sample' => '/public/sample/three.xlsx',
                'description' => 'Inspection Type Three',
            ],
        ];

        foreach ($audit_types as $audit_type) {
            if (InspectionTemplateType::where('name', $audit_type['name'])->exists()) {
                continue;
            }
            InspectionTemplateType::create($audit_type);
        }

    }
}
