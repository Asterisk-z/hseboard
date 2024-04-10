<?php

namespace Database\Seeders;

use App\Models\Priority;
use Illuminate\Database\Seeder;

class PrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $priorities = [
            [
                'name' => 'low',
                'description' => 'Low',
            ],
            [
                'name' => 'medium',
                'description' => 'Medium',
            ],
            [
                'name' => 'high',
                'description' => 'High',
            ],
            [
                'name' => 'very_high',
                'description' => 'Very High',
            ],
            [
                'name' => 'extreme',
                'description' => 'Extreme',
            ],
        ];

        foreach ($priorities as $priority) {
            if (Priority::where('name', $priority['name'])->exists()) {
                continue;
            }
            Priority::create($priority);
        }

    }
}
