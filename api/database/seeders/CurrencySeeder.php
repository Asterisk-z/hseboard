<?php

namespace Database\Seeders;

use App\Models\Currency;
use Illuminate\Database\Seeder;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $currencies = [
            [
                'name' => 'usd',
                'code' => 'USD',
                'symbol' => '$',
                'exchange_to_base' => 1,
                'is_base' => true,
                'description' => 'United State Dollar',
            ],
            [
                'name' => 'eur',
                'code' => 'EUR',
                'symbol' => '€',
                'exchange_to_base' => 0.92,
                'description' => 'European Union',
            ],
            [
                'name' => 'ngn',
                'code' => 'NGN',
                'symbol' => '₦',
                'exchange_to_base' => 1200,
                'description' => 'Nigerian Naira',
            ],
        ];

        foreach ($currencies as $item) {
            if (Currency::where('name', $item['name'])->exists()) {
                continue;
            }
            Currency::create($item);
        }

    }
}
