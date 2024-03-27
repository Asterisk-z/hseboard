<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $regions = [
            [
                "name" => "Africa",
                "slug" => "africa",
                "code" => "AF",
            ],
            [
                "name" => "North America",
                "slug" => "north_america",
                "code" => "NA",
            ],
            [
                "name" => "Oceania",
                "slug" => "oceania",
                "code" => "OC",
            ],
            [
                "name" => "Antarctica",
                "slug" => "antarctica",
                "code" => "AN",
            ],
            [
                "name" => "Asia",
                "slug" => "asia",
                "code" => "AS",
            ],
            [
                "name" => "Europe",
                "slug" => "europe",
                "code" => "EU",
            ],
            [
                "name" => "South America",
                "slug" => "south_america",
                "code" => "SA",
            ],
        ];

        foreach ($regions as $region) {
            if (Region::where('code', $region['code'])->where('slug', $region['slug'])->exists()) {
                continue;
            }
            Region::create($region);
        }

    }
}
