<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create role if not exists
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'admin']);

        $admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('12345678'),
            'status' => 'active',
        ]);

        $admin->assignRole($role);
    }
}
