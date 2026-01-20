<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            'Acme Kft.',
            'Teszt Solutions Zrt.',
            'Demo Soft Bt.',
        ];

        foreach ($companies as $company) {
            Company::firstOrCreate(['company_name' => $company]);
        }
    }
}
