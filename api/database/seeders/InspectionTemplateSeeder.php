<?php

namespace Database\Seeders;

use App\Models\InspectionTemplate;
use Illuminate\Database\Seeder;

class InspectionTemplateSeeder extends Seeder
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
                'inspection_template_type_id' => 1,
                'title' => 'Inspection One',
                'description' => 'Inspection One',
            ],
            [
                'inspection_template_type_id' => 2,
                'title' => 'Inspection Two',
                'description' => 'Inspection Two',
            ],
            [
                'inspection_template_type_id' => 3,
                'title' => 'Inspection Three',
                'description' => 'Inspection Three',
            ],
        ];

        foreach ($audit_types as $audit_type) {
            if (InspectionTemplate::where('title', $audit_type['title'])->exists()) {
                continue;
            }
            InspectionTemplate::create($audit_type);
        }

    }
}
