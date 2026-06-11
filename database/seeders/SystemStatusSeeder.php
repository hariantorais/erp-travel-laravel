<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SystemStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            // Group: booking
            ['group' => 'booking', 'code' => 'BK_DP', 'name' => 'Deposit (DP)', 'color' => '#f59e0b', 'order' => 1],
            ['group' => 'booking', 'code' => 'BK_LUNAS', 'name' => 'Lunas', 'color' => '#10b981', 'order' => 2],
            ['group' => 'booking', 'code' => 'BK_BATAL', 'name' => 'Dibatalkan', 'color' => '#ef4444', 'order' => 3],

            // Group: document / passport
            ['group' => 'document', 'code' => 'PP_BELUM', 'name' => 'Belum Menyerahkan', 'color' => '#6b7280', 'order' => 1],
            ['group' => 'document', 'code' => 'PP_TERIMA', 'name' => 'Diterima di Pusat', 'color' => '#3b82f6', 'order' => 2],
            ['group' => 'document', 'code' => 'PP_EXPIRED', 'name' => 'Masa Berlaku Kurang < 6 Bln', 'color' => '#b91c1c', 'order' => 3],

            // Group: visa
            ['group' => 'visa', 'code' => 'VS_PROSES', 'name' => 'Pengajuan MOFA', 'color' => '#d97706', 'order' => 1],
            ['group' => 'visa', 'code' => 'VS_ISSUED', 'name' => 'Visa Issued', 'color' => '#059669', 'order' => 2],
            ['group' => 'visa', 'code' => 'VS_REJECT', 'name' => 'Visa Rejected', 'color' => '#dc2626', 'order' => 3],
        ];

        foreach ($statuses as $status) {
            DB::table('statuses')->updateOrInsert(
                ['group' => $status['group'], 'code' => $status['code']], // Evaluasi keunikan berbasis composite key
                [
                    'name' => $status['name'],
                    'color' => $status['color'],
                    'order' => $status['order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
