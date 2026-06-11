<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Rute Publik
Volt::route('/', 'welcome')->name('home');

// 2. Rute Tamu (Guest)
Route::middleware('guest')->group(function () {
    Volt::route('/login', 'auth.login')->name('login');
});

// 3. Rute Terproteksi Intern ERP (Autentikasi Multi-Tenant)
Route::middleware(['auth', 'role:super_admin'])->group(function () {

    // Redireksi fallback dashboard luar ke rute dashboard admin utama
    Route::redirect('/dashboard', '/admin/dashboard');

    // Hub Kontrol Terpusat Admin ERP Sahara Travel
    Route::prefix('admin')->name('admin.')->group(function () {

        // M1: Administrasi & Fondasi Core ERP
        Volt::route('/dashboard', 'admin.dashboard')->name('dashboard');
        Volt::route('/branches', 'admin.branches.index')->name('branches.index');
        Volt::route('/company-profile', 'admin.company-profile.index')->name('company-profile.index');

        // M2: Tata Kelola Produk, Manifes Maskapai & Logistik Kamar
        Volt::route('/packages', 'admin.packages.index')->name('packages.index');
        Volt::route('/packages/{slug}/itinerary', 'admin.packages.itinerary')->name('packages.itinerary');
        Volt::route('/departures', 'admin.departures.index')->name('departures.index');
        Volt::route('/departures/{id}', 'admin.departures.show')->name('departures.show');
        Volt::route('/flights', 'admin.flights.index')->name('flights.index');
        Volt::route('/airlines', 'admin.airlines.index')->name('airlines.index');

        // Modul Logistik Kamar Hotel & Kendaraan Bus Arab Saudi
        Volt::route('/saudi-hotels', 'admin.saudi-logistics.hotels')->name('saudi-hotels.index');
        Volt::route('/saudi-buses', 'admin.saudi-logistics.buses')->name('saudi-buses.index');

        // M3: Operasional Registrasi & Manajemen CRM Jemaah
        Volt::route('/bookings', 'admin.bookings.index')->name('bookings.index');
        Volt::route('/documents', 'admin.documents.index')->name('documents.index');
        Volt::route('/manifests', 'admin.manifests.index')->name('manifests.index');

        // M4: Akuntansi Keuangan & Pembukuan Kas Masuk/Keluar
        Volt::route('/invoices', 'admin.invoices.index')->name('invoices.index');
        Volt::route('/expenses', 'admin.expenses.index')->name('expenses.index');
        Volt::route('/reports/finance', 'admin.reports.finance')->name('reports.finance');
    });

    // Operasi Pemutusan Akses Sistem (Sesi Keamanan Mandatori)
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    })->name('logout');
});
