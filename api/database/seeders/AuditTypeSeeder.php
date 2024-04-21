<?php

namespace Database\Seeders;

use App\Models\AuditType;
use Illuminate\Database\Seeder;

class AuditTypeSeeder extends Seeder
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
                'name' => 'hse_management_system_audit',
                'sample' => '/public/sample/hse.xlsx',
                'description' => 'HSE Management System Audit',
            ],
            [
                'name' => 'environmental_management_system',
                'sample' => '/public/sample/env.xlsx',
                'description' => 'Environmental Management System',
            ],
            [
                'name' => 'quality_management_audit',
                'sample' => '/public/sample/qua.xlsx',
                'description' => 'Quality Management Audit',
            ],
        ];

        foreach ($audit_types as $audit_type) {
            if (AuditType::where('name', $audit_type['name'])->exists()) {
                continue;
            }
            AuditType::create($audit_type);
        }

    }
}
