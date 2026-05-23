<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        Organization::create([
            'code' => 'ORG000001',
            'name' => 'Demo Organization',
            'description' => 'Test organization',
            'status' => 'ACTIVE',
        ]);
    }
}
