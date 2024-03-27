<?php

namespace Database\Seeders;

use App\Models\Industry;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $industries = [
            [
                'name' => 'Agriculture',
                'slug' => Str::slug('Agriculture', '_'),
            ],
            [
                'name' => 'Manufacturing ',
                'slug' => Str::slug('Manufacturing ', '_'),
            ],
            [
                'name' => 'Mining and Quarrying',
                'slug' => Str::slug('Mining and Quarrying', '_'),
            ],
            [
                'name' => 'Electricity and Energy',
                'slug' => Str::slug('Electricity and Energy', '_'),
            ],
            [
                'name' => 'Waste Management',
                'slug' => Str::slug('Waste Management', '_'),
            ],
            [
                'name' => 'Oil and Gas',
                'slug' => Str::slug('Oil and Gas', '_'),
            ],
            [
                'name' => 'Engineering and Construction',
                'slug' => Str::slug('Engineering and Construction', '_'),
            ],
            [
                'name' => 'Telecommunication',
                'slug' => Str::slug('Telecommunication', '_'),
            ],
            [
                'name' => 'Information and Communication Technology',
                'slug' => Str::slug('Information and Communication Technology', '_'),
            ],
            [
                'name' => 'Banking and Finance',
                'slug' => Str::slug('Banking and Finance', '_'),
            ],
            [
                'name' => 'Trade and Investment',
                'slug' => Str::slug('Trade and Investment', '_'),
            ],
            [
                'name' => 'Pharmaceuticals',
                'slug' => Str::slug('Pharmaceuticals', '_'),
            ],
            [
                'name' => 'Health Care',
                'slug' => Str::slug('Health Care', '_'),
            ],
            [
                'name' => 'Academic',
                'slug' => Str::slug('Academic', '_'),
            ],
            [
                'name' => 'Steel and Metalworks',
                'slug' => Str::slug('Steel and Metalworks', '_'),
            ],
            [
                'name' => 'Forestry',
                'slug' => Str::slug('Forestry', '_'),
            ],
            [
                'name' => 'Religious Activities',
                'slug' => Str::slug('Religious Activities', '_'),
            ],
            [
                'name' => 'Logistics and Transportation',
                'slug' => Str::slug('Logistics and Transportation', '_'),
            ],
            [
                'name' => 'Maritime',
                'slug' => Str::slug('Maritime', '_'),
            ],
            [
                'name' => 'Military',
                'slug' => Str::slug('Military', '_'),
            ],
            [
                'name' => 'Aviation',
                'slug' => Str::slug('Aviation', '_'),
            ],
            [
                'name' => 'Food and Beverage ',
                'slug' => Str::slug('Food and Beverage ', '_'),
            ],
            [
                'name' => 'Automobile',
                'slug' => Str::slug('Automobile', '_'),
            ],
            [
                'name' => 'Chemical and Process',
                'slug' => Str::slug('Chemical and Process', '_'),
            ],
            [
                'name' => 'Others',
                'slug' => Str::slug('Others', '_'),
            ],
        ];

        foreach ($industries as $industry) {
            if (Industry::where('slug', $industry['slug'])->exists()) {
                continue;
            }
            Industry::create($industry);
        }

    }
}
