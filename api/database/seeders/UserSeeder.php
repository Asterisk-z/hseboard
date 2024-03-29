<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

    }
}
