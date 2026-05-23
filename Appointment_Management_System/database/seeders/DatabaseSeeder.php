<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            OrganizationSeeder::class,
            BusinessSeeder::class,
        ]);

        $organization = Organization::first();
        $business = \App\Models\Business::first();

        User::create([
            'code' => 'USR000001',

            'organization_code' => $organization->code,
            'business_code' => $business->code,

            'name' => 'Super Admin',
            'email' => 'superadmin@gmail.com',
            'password' => Hash::make('super@dmin'),

            'user_type' => 'SUPER_ADMIN',
            'status' => 'ACTIVE',
        ]);
    }
}
