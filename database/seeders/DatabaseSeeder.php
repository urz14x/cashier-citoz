<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Member;
use App\Models\Product;
use App\Models\StockAdjustment;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            PackageSeeder::class,
            MemberStatusTestSeeder::class
            // RoleSeeder::class
        ]);
        // Employee::factory(5)->create();
        // Member::factory(50)->create();
        // Product::factory(50)->create();
        // StockAdjustment::factory(50)->create();

    }
}
