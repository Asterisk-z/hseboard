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
                'sample' => 'sample/inspection.xlsx',
                'description' => 'Inspection',
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
