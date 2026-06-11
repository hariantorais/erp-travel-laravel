<?php

namespace Database\Seeders;

use App\Models\User;
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
            CompanyProfileSeeder::class,
            SystemStatusSeeder::class,
            BranchSeeder::class,
            RolePermissionSeeder::class,
            SettingSeeder::class,
            HotelSeeder::class,
            AirlineSeeder::class
        ]);
    }
}
