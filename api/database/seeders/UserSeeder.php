<?php

namespace Database\Seeders;

use App\Models\ActionToken;
use App\Models\Organisation;
use App\Models\OrganisationUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (!User::where('email', 'admin@gmail.com')->exists()) {
            User::create([
                'firstName' => "Admin",
                'lastName' => "Admin",
                'email' => "admin@gmail.com",
                'phone' => "08155555555",
                'account_type_id' => 0,
                'is_admin' => 'yes',
                'password' => Hash::make('password'),
            ]);
        }
        $corporate = [1, 2, 3, 4, 5];

        foreach ($corporate as $key => $item) {

            $name = "test$item";

            if (!User::where('email', $name . '@gmail.com')->exists()) {
                $createUser = User::create([
                    'firstName' => "$name",
                    'lastName' => "$name",
                    'email' => "$name@gmail.com",
                    'phone' => "08166633322$item",
                    'account_role_id' => 3,
                    'account_type_id' => 1,
                    'password' => Hash::make('Pass@1111'),
                    'email_verified_at' => now(),
                ]);

                $organization = Organisation::create([
                    'name' => "BigDream$item",
                    'bio' => "Dreams starts when you belief",
                    'address' => 'No road house un the street',
                    'country_id' => 1,
                    'industry_id' => 1,
                    'user_id' => $createUser->id,
                ]);

                $relation = OrganisationUser::create([
                    'user_id' => $createUser->id,
                    'organization_id' => $organization->id,
                ]);

                $signature = Str::random(50);

                ActionToken::create([
                    "email" => $createUser->email,
                    "signature" => $signature,
                    'type' => ActionToken::types['EV'],
                    'status' => "completed",
                ]);

            }

        }

        $corporate = [6, 7, 8, 9, 10, 11, 12];

        foreach ($corporate as $key => $item) {

            $name = "test$item";

            if (!User::where('email', $name . '@gmail.com')->exists()) {
                $createUser = User::create([
                    'firstName' => "$name",
                    'lastName' => "$name",
                    'email' => "$name@gmail.com",
                    'phone' => "08166633322$item",
                    'account_type_id' => 2,
                    'password' => Hash::make('Pass@1111'),
                    'email_verified_at' => now(),
                ]);

                $signature = Str::random(50);

                ActionToken::create([
                    "email" => $createUser->email,
                    "signature" => $signature,
                    'type' => ActionToken::types['EV'],
                    'status' => "completed",
                ]);

            }

        }

    }
}
