<?php

if (!function_exists('isRouteActive')) {
   /**
    * Memeriksa apakah rute yang sedang dibuka cocok dengan menu navigasi.
    */
   function isRouteActive($routeName)
   {
      if (!$routeName) return false;

      if (request()->routeIs($routeName)) {
         return true;
      }

      // Proteksi sub-rute halaman itinerary agar induknya (Paket Umroh) tetap menyala
      if ($routeName === 'admin.packages.index' && request()->routeIs('admin.packages.itinerary')) {
         return true;
      }

      // Deteksi wildcard untuk sub-rute bertingkat (E.g. admin.departures.* atau admin.flights.*)
      $targetSegments = explode('.', $routeName);
      if (count($targetSegments) >= 2) {
         // Pastikan polanya aman tanpa merusak navigasi utama
         $pattern = $targetSegments[0] . '.' . $targetSegments[1] . '.*';
         return request()->routeIs($pattern);
      }

      return false;
   }
}

if (!function_exists('shouldGroupBeOpen')) {
   /**
    * Menentukan apakah suatu grup menu harus otomatis terbuka (show) sejak awal.
    */
   function shouldGroupBeOpen($group)
   {
      if (!isset($group['items']) || !is_array($group['items'])) {
         return $group['open_by_default'] ?? false;
      }

      foreach ($group['items'] as $item) {
         // KUNCI AMAN: Jika kunci 'route' tidak disuplai di array menu, lewati (jangan di-scan)
         if (isset($item['route']) && isRouteActive($item['route'])) {
            return true;
         }
      }

      return $group['open_by_default'] ?? false;
   }
}


if (!function_exists('navigations')) {
   function navigations()
   {
      return [
         [
            'heading' => 'Dashboard Utama',
            'icon'    => 'home', // Ikon ditaruh di induk
            'open_by_default' => true,
            'items'   => [
               ['title' => 'Dashboard', 'route' => 'admin.dashboard', 'role' => null]
            ],
         ],
         [
            'heading' => 'Logistik & Vendor',
            'icon'    => 'paper-airplane',
            'open_by_default' => false,
            'items'   => [
               // Kunci 'route' dan 'role' tertulis rapi 100%
               ['title' => 'Daftar Maskapai', 'route' => 'admin.airlines.index', 'role' => null],
               ['title' => 'Rute Penerbangan', 'route' => 'admin.flights.index', 'role' => null],
               ['title' => 'Hotel Arab Saudi', 'route' => 'admin.saudi-hotels.index', 'role' => null],
               ['title' => 'Armada Bus Saudi', 'route' => 'admin.saudi-buses.index', 'role' => null],
            ],
         ],
         [
            'heading' => 'Manajemen Produk',
            'icon'    => 'gift',
            'open_by_default' => true,
            'items'   => [
               ['title' => 'Katalog Paket Umroh', 'route' => 'admin.packages.index', 'role' => null],
               ['title' => 'Jadwal Keberangkatan', 'route' => 'admin.departures.index', 'role' => null],
            ],
         ],
         [
            'heading' => 'Penjualan & CRM',
            'icon'    => 'user-group',
            'open_by_default' => true,
            'items'   => [
               ['title' => 'Booking', 'route' => 'admin.bookings.index', 'role' => null],
            ],
         ],
         [
            'heading' => 'Handling & Visa',
            'icon'    => 'document-text',
            'open_by_default' => false,
            'items'   => [
               ['title' => 'Pemberkasan & Visa', 'route' => 'admin.documents.index', 'role' => null],
               ['title' => 'Manifest & Handling', 'route' => 'admin.manifests.index', 'role' => null],
            ],
         ],
         [
            'heading' => 'Keuangan',
            'icon'    => 'banknotes',
            'open_by_default' => false,
            'items'   => [
               ['title' => 'Invoice & Pembayaran', 'route' => 'admin.invoices.index', 'role' => null],
               ['title' => 'Biaya Operasional', 'route' => 'admin.expenses.index', 'role' => null],
               ['title' => 'Laporan Keuangan', 'route' => 'admin.reports.finance', 'role' => null],
            ],
         ],
         [
            'heading' => 'Pengaturan',
            'icon'    => 'building-office-2',
            'open_by_default' => false,
            'items'   => [
               ['title' => 'Kantor Cabang', 'route' => 'admin.branches.index', 'role' => 'super_admin'],
               ['title' => 'Profil Perusahaan', 'route' => 'admin.company-profile.index', 'role' => 'super_admin'],
            ],
         ],
      ];
   }
}
