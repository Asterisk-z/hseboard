<?php

namespace Database\Seeders;

use App\Models\ObservationType;
use Illuminate\Database\Seeder;

class ObservationTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        $observation_types = [
            [
                'description' => 'Unsafe Act (UA)',
                'name' => 'unsafe_act',
            ],
            [
                'description' => 'Unsafe Condition (UC)',
                'name' => 'unsafe_condition',
            ],
            [
                'description' => 'Near-Miss (NM)',
                'name' => 'near-miss',
            ],
            [
                'description' => 'First Aid Case (FAC)',
                'name' => 'first_aid_case',
                'require_number_affected' => 'yes',
            ],
            [
                'description' => 'Restricted Work Case (RWC)',
                'name' => 'restricted_work_case ',
            ],
            [
                'description' => 'Road Traffic Accident (RTA)',
                'name' => 'road_traffic_accident ',
            ],
            [
                'description' => 'Medical Treatment Case (MTC)',
                'name' => 'medical_treatment_case ',
                'require_number_affected' => 'yes',
            ],
            [
                'description' => 'Lost Workday Case (LWC)',
                'name' => 'lost_workday_case ',
                'require_number_affected' => 'yes',
            ],
            [
                'description' => 'Dangerous Occurrence',
                'name' => 'dangerous_occurrence',
                'require_number_affected' => 'yes',
            ],
            [
                'description' => 'Permanent Partial Disability (PPD)',
                'name' => 'permanent_partial_disability ',
                'require_number_affected' => 'yes',
            ],
            [
                'description' => 'Permanent Total Disability (PTD)',
                'name' => 'permanent_total_disability ',
                'require_number_affected' => 'yes',
            ],
            [
                'description' => 'Fatality (FAT)',
                'name' => 'fatality ',
                'require_number_affected' => 'yes',
            ],
        ];

        foreach ($observation_types as $observation_type) {
            if (ObservationType::where('name', $observation_type['name'])->exists()) {
                continue;
            }
            ObservationType::create($observation_type);
        }

    }
}
