<?php

namespace Database\Seeders;

use App\Models\AuditTemplate;
use Illuminate\Database\Seeder;

class AuditDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $audit_types = [
            [
                'audit_type_id' => 1,
                'title' => 'HSE Management System Audit',
                'description' => 'HSE Management System Audit',
            ],
            [
                'audit_type_id' => 2,
                'title' => 'Environmental Management System',
                'description' => 'Environmental Management System',
            ],
            [
                'audit_type_id' => 3,
                'title' => 'Quality Management Audit',
                'description' => 'Quality Management Audit',
            ],
        ];

        foreach ($audit_types as $audit_type) {
            if (AuditTemplate::where('title', $audit_type['title'])->exists()) {
                continue;
            }
            AuditTemplate::create($audit_type);
        }

    }
}
