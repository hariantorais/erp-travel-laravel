<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Bersihkan Cache Spatie di awal secara mutlak
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 2. Daftar Hak Akses (Permissions) Baku
        $permissions = [
            'manage_settings',
            'manage_branches',
            'view_finance',
            'approve_payments',
            'manage_documents',
            'create_bookings',
        ];

        // Injeksi langsung ke database menggunakan query builder untuk menghindari masalah latensi objek
        foreach ($permissions as $permission) {
            DB::table('permissions')->updateOrInsert(
                ['name' => $permission, 'guard_name' => 'web'],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }

        // 3. Definisi Jabatan (Roles) dan Pemetaan Hak Aksesnya
        $rolesStructure = [
            'super_admin' => $permissions,
            'branch_manager' => ['view_finance', 'approve_payments', 'manage_documents', 'create_bookings'],
            'finance' => ['view_finance', 'approve_payments'],
            'document_staff' => ['manage_documents'],
            'sales_agent' => ['create_bookings'],
        ];

        foreach ($rolesStructure as $roleName => $rolePermissions) {
            // Buat atau cari Role
            $role = Role::findOrCreate($roleName, 'web');

            // Singkronisasikan permission langsung menggunakan array nama, aman dari crash objek
            $role->syncPermissions($rolePermissions);
        }

        // 4. Pembuatan Akun Simulasi Pengujian
        $usersData = [
            [
                'name' => 'Super Administrator',
                'email' => 'admin@travel.com',
                'role' => 'super_admin'
            ],
            [
                'name' => 'Manager Cabang Batam',
                'email' => 'manager.btm@travel.com',
                'role' => 'branch_manager'
            ],
            [
                'name' => 'Staf Keuangan Pusat',
                'email' => 'finance@travel.com',
                'role' => 'finance'
            ],
            [
                'name' => 'Staf Dokumen & Kemenag',
                'email' => 'document@travel.com',
                'role' => 'document_staff'
            ],
            [
                'name' => 'Agen Sales Lapangan',
                'email' => 'sales@travel.com',
                'role' => 'sales_agent'
            ],
        ];

        foreach ($usersData as $data) {
            $user = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password123'),
                ]
            );

            $user->syncRoles([$data['role']]);
        }
    }
}
