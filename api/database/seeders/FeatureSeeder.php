<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Seeder;

class FeatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $plans = [
            [
                'name' => 'team_member',
                'cost' => 0,
                'parent_id' => null,
                'default' => true,
                'description' => 'Team Member',
            ],
            [
                'name' => 'inbox',
                'cost' => 0,
                'parent_id' => null,
                'default' => true,
                'description' => 'Inbox',
            ],
            [
                'name' => 'notifications',
                'cost' => 0,
                'parent_id' => null,
                'default' => true,
                'description' => 'Notifications',
            ],
            [
                'name' => 'observations',
                'cost' => 0,
                'parent_id' => null,
                'default' => true,
                'description' => 'Observations',
            ],
            [
                'name' => 'actions',
                'cost' => 0,
                'parent_id' => null,
                'default' => false,
                'description' => 'Actions',
            ],
            [
                'name' => 'create_actions',
                'cost' => 10,
                'parent_id' => 5,
                'default' => false,
                'description' => 'Create And Assign Action',
            ],
            [
                'name' => 'hse_statistics',
                'cost' => 0,
                'parent_id' => null,
                'default' => false,
                'description' => 'HSE Statistics',
            ],
            [
                'name' => 'prepare_hse_statistics',
                'cost' => 10,
                'parent_id' => 7,
                'default' => false,
                'description' => 'Prepare statistics and Download reports',
            ],
            [
                'name' => 'hse_investigation',
                'cost' => 0,
                'parent_id' => null,
                'default' => false,
                'description' => 'HSE Investigation',
            ],
            [
                'name' => 'internal_hse_investigation',
                'cost' => 10,
                'parent_id' => 9,
                'default' => false,
                'description' => 'Carry out internal investigations, download and share reports',
            ],
            [
                'name' => 'external_hse_investigation',
                'cost' => 10,
                'parent_id' => 9,
                'default' => false,
                'description' => 'Carry out external investigations, download and share reports',
            ],
            [
                'name' => 'internal_hse_reinvestigation',
                'cost' => 10,
                'parent_id' => 9,
                'default' => false,
                'description' => 'Carry out internal re-investigations, download and share reports',
            ],
            [
                'name' => 'external_hse_reinvestigation',
                'cost' => 10,
                'parent_id' => 9,
                'default' => false,
                'description' => 'Carry out external re-investigations, download and share reports',
            ],
            [
                'name' => 'external_witness',
                'cost' => 10,
                'parent_id' => 9,
                'default' => false,
                'description' => 'Invite External witness for questionnaire and interview',
            ],
            [
                'name' => 'audit_management',
                'cost' => 0,
                'parent_id' => null,
                'default' => false,
                'description' => 'Audit Management',
            ],
            [
                'name' => 'environment_audit_internal',
                'cost' => 10,
                'parent_id' => 15,
                'default' => false,
                'description' => 'Internal Environmental Audit- ISO 14001:2015',
            ],
            [
                'name' => 'quality_audit_internal',
                'cost' => 10,
                'parent_id' => 15,
                'default' => false,
                'description' => 'Internal Quality Audit- ISO 9001:2015',
            ],
            [
                'name' => 'hse_audit_internal',
                'cost' => 10,
                'parent_id' => 15,
                'default' => false,
                'description' => 'Internal HSE Audit- ISO 45001:2018',
            ],
            [
                'name' => 'environment_audit_external',
                'cost' => 10,
                'parent_id' => 15,
                'default' => false,
                'description' => 'External Environmental Audit- ISO 14001:2015',
            ],
            [
                'name' => 'quality_audit_external',
                'cost' => 10,
                'parent_id' => 15,
                'default' => false,
                'description' => 'External Quality Audit- ISO 9001:2015',
            ],
            [
                'name' => 'hse_audit_external',
                'cost' => 10,
                'parent_id' => 15,
                'default' => false,
                'description' => 'External HSE Audit- ISO 45001:2018',
            ],
            [
                'name' => 'osha_compliance',
                'cost' => 0,
                'parent_id' => null,
                'default' => false,
                'description' => 'OSHA Compliance',
            ],
            [
                'name' => 'recordkeeping_osha_compliance',
                'cost' => 0,
                'parent_id' => 22,
                'default' => false,
                'description' => 'OSHA Compliance Record Keeping',
            ],
            [
                'name' => 'inspection',
                'cost' => 0,
                'parent_id' => null,
                'default' => false,
                'description' => 'Facility Inspection',
            ],
            [
                'name' => 'internal_inspection',
                'cost' => 10,
                'parent_id' => 24,
                'default' => false,
                'description' => 'Internal Facility Inspection',
            ],
            [
                'name' => 'external_inspection',
                'cost' => 10,
                'parent_id' => 24,
                'default' => false,
                'description' => 'External Facility Inspection',
            ],
            [
                'name' => 'permit_to_Work',
                'cost' => 0,
                'parent_id' => null,
                'default' => false,
                'description' => 'Permit to Work System',
            ],
            [
                'name' => 'jha_permit_to_Work',
                'cost' => 10,
                'parent_id' => 27,
                'default' => false,
                'description' => 'Job Hazard Analysis',
            ],
            [
                'name' => 'internal_permit_to_Work',
                'cost' => 0,
                'parent_id' => 27,
                'default' => false,
                'description' => 'Internal Permit to Work System',
            ],
            [
                'name' => 'external_permit_to_Work',
                'cost' => 0,
                'parent_id' => 27,
                'default' => false,
                'description' => 'External Permit to Work System',
            ],
        ];

        foreach ($plans as $item) {
            if (Feature::where('name', $item['name'])->exists()) {
                continue;
            }
            Feature::create($item);
        }

    }
}
