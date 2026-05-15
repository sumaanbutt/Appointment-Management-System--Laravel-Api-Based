<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Organization;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    public function run(): void
    {
        $organization = Organization::first();

        Business::create([
            'code' => 'BUS000001',
            'organization_code' => $organization->code,
            'name' => 'Demo Business',
            'email' => 'business@test.com',
            'phone' => '123456789',
            'description' => 'Test business',
            'status' => 'ACTIVE',
        ]);
    }
}
