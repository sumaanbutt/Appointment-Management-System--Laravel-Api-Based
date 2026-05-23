<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        foreach (UserRoleEnum::cases() as $role){
            Role::firstOrCreate([
                'name' => $role->value,
                'guard_name' => 'api',
            ]);
        }
    }
}
