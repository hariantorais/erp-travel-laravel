<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Kategori: Umum
            [
                'key' => 'app_name',
                'value' => 'ERP Biro Travel Umroh',
                'group' => 'general',
                'type' => 'string'
            ],
            // Kategori: Keuangan (Strict Ledger Parameter)
            [
                'key' => 'min_dp_amount',
                'value' => '5000000', // Minimal DP 5 Juta Rupiah per Pax
                'group' => 'finance',
                'type' => 'integer'
            ],
            [
                'key' => 'currency_code',
                'value' => 'IDR',
                'group' => 'finance',
                'type' => 'string'
            ],
            // Kategori: Notifikasi & Antrean
            [
                'key' => 'wa_gateway_retry_limit',
                'value' => '3',
                'group' => 'whatsapp',
                'type' => 'integer'
            ]
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']],
                [
                    'value' => $setting['value'],
                    'group' => $setting['group'],
                    'type' => $setting['type'],
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );
        }
    }
}
