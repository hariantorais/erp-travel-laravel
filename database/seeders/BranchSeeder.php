<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::create([
            'code' => 'PST',
            'name' => 'Kantor Pusat',
            'address' => 'Jl. Mekah No. 1, Jakarta Pusat',
            'phone' => '021-1234567',
            'leader_name' => 'H. Ahmad',
        ]);
    }
}
