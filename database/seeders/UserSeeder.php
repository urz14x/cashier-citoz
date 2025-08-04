<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@citozsportcenter.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

         // Pastikan role sudah ada
         $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

         // Assign role ke user
         $user->assignRole($role);
    }
}
