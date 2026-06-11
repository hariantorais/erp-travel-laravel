# 🕋 Elijabah Travel ERP

> **Status Proyek:** 🚧 _Development Phase (Milestone 2: Logistik & Vendor sedang Berjalan)_

Elijabah Travel ERP adalah platform internal terpusat (_Multi-Tenant Intern_) berkinerja tinggi yang dirancang untuk mengotomatisasi seluruh ekosistem operasional travel Umroh dan Haji Plus. Platform ini mengintegrasikan fondasi administrasi multi-cabang, manajemen produk, logistik manifes di Arab Saudi, CRM jemaah, hingga sistem akuntansi keuangan kas masuk/keluar secara _real-time_.

Sistem ini dirancang dengan arsitektur **Service-Layer murni** menggunakan **SQL murni via DB Facade (Query Builder)** untuk memangkas _overhead_ ORM, memastikan kecepatan eksekusi data tetap stabil pada saat menangani ribuan manifes jemaah secara simultan.

## 🚀 Spesifikasi & Tech Stack Utama

- **Core Framework:** Laravel 11 / 12
- **Frontend Engine:** Livewire Volt v3 _(Pola Fungsional / Ramping)_
- **UI System:** Flux UI Components & Tailwind CSS v4.0
- **Reactive State:** Alpine.js _(Bawaan Livewire)_
- **Database Layer:** MySQL / MariaDB _(Produksi)_, SQLite In-Memory _(Testing)_
- **Testing Suite:** Pest PHP Suite

## 📂 Cakupan Modul & Target Fitur (PRD Compliance)

Sistem ini dibagi menjadi 4 core modul utama yang sedang dan akan dikembangkan secara bertahap:

### 🏢 M1: Administrasi & Fondasi Core ERP (Selesai)

- **Kantor Cabang:** Pengaturan multi-cabang pembantu, manajemen pimpinan cabang, dan restriksi data wilayah.
- **Profil Perusahaan:** Tata kelola legalitas hukum, manajemen tenant tunggal, dan konfigurasi berkas legal korporat.
- **Dashboard Analytics:** Ringkasan statistik kuota keberangkatan, total booking aktif, dan grafik arus kas bulanan.

### ✈️ M2: Tata Kelola Produk, Manifes Maskapai & Logistik Kamar (Sedang Berjalan 🚧)

- **Katalog Paket Umroh (M2.1):** Pembuatan brosur acuan awal standar maskapai, tipe paket (Umroh, Haji, Tour), spesifikasi hotel default, dan durasi hari.
- **Master Vendor & Penerbangan (M2.1):** Pemisahan data entitas antara induk Perusahaan Maskapai (`airlines`) dengan nomor rute penerbangan komersial (`flights`) berbasis kode IATA 3 huruf.
- **Logistik Keberangkatan (M2.3):** Workspace plotting manifes pesawat riil (`departure_flights`) dan alokasi blok kamar hotel (`departure_hotels`) per kloter fisik.
- **Saudi Logistics:** Manajemen inventaris mitra Hotel Arab Saudi dan Vendor Armada Bus di Makkah & Madinah.

### 👥 M3: Operasional Registrasi & Manajemen CRM Jemaah (Backlog 📋)

- **Booking Engine:** Pendaftaran jemaah mandiri atau rombongan, pemilihan tipe kamar (Quad, Triple, Double), dan kalkulasi otomatis harga paket.
- **Pemberkasan & Visa:** Tracking paspor, rekam medik, status kuning vaksin, dan integrasi pelaporan Siskopatuh.
- **Manifest & Handling:** Pengelompokan bus jemaah, pembagian grup pimpinan Tour Leader, dan pencetakan manifes keberangkatan bandara.

### 💰 M4: Akuntansi Keuangan & Pembukuan Kas (Backlog 📋)

- **Invoice & Pembayaran:** Generator invoice otomatis per jemaah/grup, pencatatan termin pembayaran (DP s.d Lunas), dan integrasi kwitansi.
- **Biaya Operasional (Expenses):** Pencatatan pengeluaran riil di lapangan (pembelian tiket grup, pembayaran sisa hotel Saudi, perlengkapan umroh).
- **Laporan Keuangan:** Laporan laba-rugi kotor per kloter keberangkatan, arus kas (Cash Flow), dan rekap piutang jemaah.

## 🎨 Standar Pola Desain Komponen (Volt Architecture)

Untuk menjaga kode tetap ramping dan modular, seluruh manajemen master data (CRUD) wajib dipisahkan menjadi komponen yang terisolasi:

- **Index Component (`index.blade.php`):** Hanya mengelola tampilan tabel data, pencarian didebounce, paginasi, tombol hapus, dan pemanggilan modal.
- **Form Component (`form.blade.php`):** Menangani logika manipulasi data (Create/Update) di dalam modal pop-up dan melempar sinyal reaktif via `$this->dispatch('entity-updated')`.
- **Form Object Class (`Form.php`):** Mengisolasi properti state input data, pengisian data edit dari database (`setEntity`), dan aturan validasi (`rules()`).

### Struktur Direktori Komponen

- `app/Livewire/Forms/Master/` ➔ Tempat Form Object Utility Class
- `app/Services/Master/` ➔ Tempat Logistik & Bisnis Query Builder
- `resources/views/livewire/admin/` ➔ Tempat View Blade Volt

## 🛠️ Langkah Instalasi Pengembangan Lokal

### 1. Kloning Repositori & Install Dependensi

````bash
git clone [https://github.com/hariantorais/erp-travel-laravel.git](https://github.com/hariantorais/erp-travel-laravel.git)
cd erp-travel-laravel
composer install
npm install && npm run dev

### 2. Konfigurasi Environment File
Salin file `.env.example` menjadi `.env` dan sesuaikan koneksi database lokal Anda:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=elijabah_travel_erp
DB_USERNAME=root
DB_PASSWORD=

3. Eksekusi Migrasi & Database Seeder
Jalankan perintah ini di terminal untuk membuat kunci aplikasi dan menyuntikkan data master awal:

Bash
php artisan key:generate
php artisan migrate --seed
4. Jalankan Automated Testing (Pest Suite)
Pastikan seluruh unit testing logistik dan proteksi validasi berstatus PASS sebelum melakukan push atau merge ke branch utama:

Bash
php artisan test
🔒 Hak Akses & Keamanan Sesi (RBAC)
Sistem ini menggunakan kontrol keamanan berbasis peran (Role-Based Access Control) untuk melindungi integritas data multi-cabang:

super_admin: Akses mutlak seluruh sistem, kantor cabang, audit finansial global, dan profil korporat.

operasional_staff: Akses khusus pengelolaan logistik rute penerbangan, manifes keberangkatan kloter, penataan kamar hotel, dan manifes bus.

sales_agent: Terkunci hanya pada modul registrasi jemaah baru, input berkas visa, dan penagihan invoice pembayaran.

🚀 Elijabah Travel ERP — Ketaatan pada PRD, Kecepatan pada Performa.

### 4. Jalankan Automated Testing (Pest Suite)
Pastikan seluruh unit testing logistik dan proteksi validasi berstatus **PASS** sebelum melakukan push atau merge ke branch utama:

```bash
php artisan test

---

### Poin 5: Judul Bagian RBAC
Salin teks di bawah ini tepat di bawah Poin 4:

```markdown
---

## 🔒 Hak Akses & Keamanan Sesi (RBAC)

Sistem ini menggunakan kontrol keamanan berbasis peran (*Role-Based Access Control*) untuk melindungi integritas data multi-cabang:

* **`super_admin`**: Akses mutlak seluruh sistem, kantor cabang, audit finansial global, dan profil korporat.
* **`operasional_staff`**: Akses khusus pengelolaan logistik rute penerbangan, manifes keberangkatan kloter, penataan kamar hotel, dan manifes bus.
* **`sales_agent`**: Terkunci hanya pada modul registrasi jemaah baru, input berkas visa, dan penagihan invoice pembayaran.

---
🚀 **Elijabah Travel ERP** — *Ketaatan pada PRD, Kecepatan pada Performa.*
````
