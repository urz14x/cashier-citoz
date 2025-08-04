<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name' => 'Paket 1 Bulan',
                'duration_in_day' => 30,
                'price' => 150000,
                'description' => 'Akses gym untuk 1 bulan.',
            ],
            [
                'name' => 'Paket 3 Bulan',
                'duration_in_day' => 91,
                'price' => 400000,
                'description' => 'Akses gym selama 1 minggu.',
            ],
        ];

        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
