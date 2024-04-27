<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubsciptionPlanSeeder extends Seeder
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
                'name' => 'one_month',
                'no_of_months' => 1,
                'description' => 'Monthly',
            ],
            [
                'name' => 'quarterly',
                'no_of_months' => 3,
                'description' => 'Quarterly',
            ],
            [
                'name' => 'semiannual',
                'no_of_months' => 6,
                'description' => 'Semi Annual',
            ],
            [
                'name' => 'yearly',
                'no_of_months' => 12,
                'description' => 'Annual',
            ],
        ];

        foreach ($plans as $item) {
            if (SubscriptionPlan::where('name', $item['name'])->exists()) {
                continue;
            }
            SubscriptionPlan::create($item);
        }

    }
}
