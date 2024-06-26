<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call(UserSeeder::class);
        $this->call(AccountTypeSeeder::class);
        $this->call(AccountRoleSeeder::class);
        $this->call(ObservationTypeSeeder::class);
        $this->call(PrioritySeeder::class);
        $this->call(RegionSeeder::class);
        $this->call(CountrySeeder::class);
        $this->call(IndustrySeeder::class);
        $this->call(InvestigationQuestion::class);
        $this->call(AuditTypeSeeder::class);
        $this->call(AuditDocumentSeeder::class);
        $this->call(AuditTemplateQuestionSeed::class);
        $this->call(AuditOptionSeed::class);
        $this->call(CurrencySeeder::class);
        $this->call(SubsciptionPlanSeeder::class);
        $this->call(FeatureSeeder::class);
        $this->call(InspectionTypeSeeder::class);
        $this->call(InspectionTemplateSeeder::class);
        $this->call(InspectionTemplateTypeSeeder::class);
        $this->call(InspectionTemplateQuestionSeeder::class);
        $this->call(PermitTypeSeeder::class);
        $this->call(PermitRequestTypeSeeder::class);

    }
}
