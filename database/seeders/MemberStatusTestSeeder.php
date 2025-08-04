<?php

namespace Database\Seeders;

use App\Models\Member;
use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MemberStatusTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $package = Package::first() ?? Package::create([
            'name' => 'Paket 1 Bulan',
            'duration_in_day' => 30,
            'price' => 150000,
        ]);

        // 1. Aktif (joined < now, expired > now, belum diperpanjang)
        Member::create([
            'name' => 'Member Aktif',
            'address' => 'Jl. Aktif No.1',
            'phone' => '0811111111',
            'social_media' => '@aktif',
            'gender' => 'M',
            'package_id' => $package->id,
            'joined' => now()->subDays(10),
            'expired' => now()->addDays(20),
            'created_at' => now()->subDays(10),
            'updated_at' => now()->subDays(10),
        ]);

        // 2. Kadaluarsa (expired < now)
        Member::create([
            'name' => 'Member Kadaluarsa',
            'address' => 'Jl. Expired No.2',
            'phone' => '0822222222',
            'social_media' => '@expired',
            'gender' => 'F',
            'package_id' => $package->id,
            'joined' => now()->subDays(40),
            'expired' => now()->subDays(10),
            'created_at' => now()->subDays(40),
            'updated_at' => now()->subDays(40),
        ]);

        // 3. Perpanjang (expired > now, updated_at > created_at)
        Member::create([
            'name' => 'Member Perpanjang',
            'address' => 'Jl. Perpanjang No.3',
            'phone' => '0833333333',
            'social_media' => '@perpanjang',
            'gender' => 'M',
            'package_id' => $package->id,
            'joined' => now()->subDays(40),
            'expired' => now()->addDays(20),
            'created_at' => now()->subDays(40),
            'updated_at' => now()->subDays(5), // ada perubahan → perpanjang
        ]);
    }
}
