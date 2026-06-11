# ERP Umroh Development Roadmap & Checklist

Role: Senior Software Engineer & Software Architect.
Task: Analisis file PRD yang saya lampirkan, lalu pandu saya secara bertahap (incremental) untuk membangun proyek ini dari nol (0) menggunakan pendekatan Best Practice, Clean Code, dan DRY (Don't Repeat Yourself).

### CONTEXT & ENVIRONMENT

- OS Development: Windows (Pastikan semua perintah terminal/CLI, path, dan tools kompatibel dengan PowerShell/CMD Windows ).
- Tech Stack & Spesifikasi: Deteksi dan ikuti sepenuhnya parameter teknologi, database, dan arsitektur yang tertulis di dalam file PRD terlampir.

### STRATEGI EKSEKUSI & PRINSIP

1. Hirarki Berkelanjutan: Pecah proyek menjadi beberapa Milestone besar yang logis berdasarkan fitur/modul di PRD (contoh: Inisialisasi -> Autentikasi -> Modul Inti -> dst).
2. Pendekatan Komprehensif: Jelaskan konsep fundamental atau arsitektur dari modul yang akan dibuat sebelum masuk ke penulisan kode.
3. Struktur Sub-Tahapan (Checkpoint): Setiap Milestone harus dipecah menjadi sub-tahapan kecil yang mandiri (atomic). Setiap sub-tahapan harus berfungsi sebagai checkpoint yang dapat diuji (testable) sebelum lanjut ke tahap berikutnya.

### MEKANISME INTERAKSI (PENTING)

Jangan berikan seluruh kode proyek dari awal sampai akhir dalam satu jawaban tunggal. Berjalanlah secara ITERATIF:

- Langkah Pertama: Berikan ringkasan Analisis Arsitektur dan Roadmap Proyek (Milestone & Sub-tahapan) secara keseluruhan berdasarkan PRD untuk saya setujui.
- Langkah Kedua: Setelah saya setuju, berikan detail instruksi HANYA untuk "Milestone 1 - Sub-Tahapan 1" terlebih dahulu.
- Di akhir setiap respon, berikan instruksi pengujian (manual via Postman/Browser atau Unit Test) untuk memastikan kode berhasil.
- Berhenti dan tunggu konfirmasi/umpan balik dari saya ("Berhasil" atau "Error") sebelum memandu saya ke sub-tahapan berikutnya.

### FORMAT OUTPUT & BAHASA

- Gunakan Bahasa Indonesia yang profesional, logis, dan mudah dimengerti.
- Gunakan Heading (##) untuk Milestone dan Subheading (###) untuk rincian tugas.
- Gunakan tabel untuk membandingkan opsi/data jika diperlukan, dan bullet points untuk langkah prosedural.
- Setiap potong kode wajib disertai penjelasan baris demi baris secara struktural dan objektif.

## 🗺️ Gambaran Umum Arsitektur & Strategi

- **Arsitektur:** TALL Stack (Laravel 13, Livewire 4 Volt, Alpine.js, Tailwind CSS) dengan Flux UI.
- **Pola Desain:** Service & Repository Pattern untuk memisahkan Logika Bisnis yang kompleks dari Komponen UI.
- **Integritas Keuangan:** Mekanisme Double-Entry Strict Ledger (`InnoDB` Foreign Keys, DB Transactions, dan Jurnal Otomatis via Eloquent Observers).
- **Antrean (Queue):** Redis Queue + Laravel Horizon untuk penanganan tugas berat (Export Excel Siskopatuh, Rendering PDF, WA Blast).
- **Environment CLI:** Windows PowerShell / CMD kompatibel.

---

## 🛠️ Milestone & Checklist Progres

### [ ] Milestone 1: Foundation & Master Data (Target: 2 Minggu)

_Fokus: Membangun tulang punggung sistem, kontrol akses, dan entitas dasar perusahaan._

- [x] **M1.1: Inisialisasi Proyek**
    - [x] Instalasi Laravel 13 (`laravel new erp-umroh --git`).
    - [x] Konfigurasi `.env` untuk database lokal (MySQL/PostgreSQL) & setup testing env.
    - [x] Instalasi Livewire 4 + Volt Extension.
    - [x] Instalasi Flux UI dan konfigurasi Tailwind CSS.
    - _Checkpoint:_ Menjalankan `php artisan serve`, halaman welcome terbuka dengan komponen Flux UI reaktif tanpa error.

- [x] **M1.2: Desain Database Inti & Konfigurasi**
    - [x] Skema tabel `company_profiles` (Detail biro, nomor izin Kemenag/PPIU).
    - [x] Skema tabel `branches` (Manajemen kantor cabang/pusat).
    - [x] Skema tabel `settings` & `statuses` (Master status untuk booking, visa, dan paspor).
    - [x] Pembuatan Database Seeders untuk data bawaan (wilayah, status standar).
    - _Checkpoint:_ Eksekusi `php artisan migrate:fresh --seed` berjalan sukses tanpa error relational constraint di PowerShell.

- [x] **M1.3: Autentikasi & RBAC (Role-Based Access Control)**
    - [x] Instalasi Spatie Laravel-Permission.
    - [x] Registrasi & konfigurasi middleware Spatie.
    - [x] Pembuatan Seeder untuk Role awal: `Super Admin`, `Manajemen`, `Finance`, `Marketing`, `Handling/Operasional`.
    - [x] Implementasi login view standar menggunakan komponen Flux UI.
    - _Checkpoint:_ Login berhasil, user diarahkan ke dashboard sesuai Role, dan hak akses rute terproteksi ketat.

- [x] **M1.4: CRUD Master Data Perusahaan**
    - [x] Pembuatan Komponen Livewire Volt untuk CRUD Cabang (`branches`).
    - [x] Pembuatan Komponen Livewire Volt untuk Profil Perusahaan (`company_profiles`).
    - _Checkpoint:_ Admin dapat menambah, mengubah, dan menghapus data cabang dengan validasi form yang muncul secara reaktif.

---

### 📅 Milestone 2: Logistik Vendor & Manajemen Produk (Target: 2 Minggu)

_Fokus: Membangun pasokan komponen independen, perakitan paket template, dan pembukaan jadwal fisik._

- [x] **M2.1: Modul Master Vendor & Aset [PONDASI UTAMA]**
    - [x] Skema tabel master `airlines` (Kode, nama, logo), `hotels` (Kota, rating bintang, jarak ke Haram, alamat, PIC), dan `flights` (Rute penerbangan statis, E.g. CGK-JED, BTH-MED).
    - [x] UI Form Input komponen logistik vendor dengan skema latar belakang kontras tinggi.
    - _Checkpoint:_ Berhasil mengunci master data hotel di Makkah/Madinah dan maskapai penerbangan sebagai bahan baku sistem.

- [x] **M2.2: Modul Katalog Paket Umroh & Itinerary**
    - [x] Skema tabel `packages` (Mengunci `airline_id`, `hotel_madinah_id`, dan `hotel_makkah_id` dari M2.1) dan `package_itineraries` (`day_no`, `city`, `title`, `activity`, `meals`).
    - [x] Logika Service untuk pembuatan paket multi-itinerary dalam satu transaksi database.
    - [x] UI Form Pembuat Paket dengan input dinamis komponen Volt dan visualisasi timeline.
    - _Checkpoint:_ Sukses menyimpan 1 paket induk beserta template rincian makan (B, L, D) harian tanpa error relasi.

- [x] **M2.3: Modul Jadwal Keberangkatan (Departures) & Pricings**
    - [x] Skema tabel `departures` (Tanggal berangkat, tanggal pulang, total kuota, sisa kuota, kantor cabang pelaksana, status) dan `departure_pricings` (Matriks harga per tipe kamar: Quad, Triple, Double, Single untuk jemaah langsung dan agen kemitraan).
    - [] Relasi alokasi kursi penerbangan (`departure_flights`) untuk PNR grup dan alokasi blok kamar (`departure_hotels`) pada tanggal keberangkatan terkait.
    - [] Validasi tanggal: Tanggal keberangkatan wajib lebih besar dari tanggal hari ini dan tanggal pulang `>=` tanggal berangkat.
    - _Checkpoint:_ Detail jadwal keberangkatan berhasil menampilkan info maskapai rute pergi/pulang, durasi malam, serta hotel di Makkah/Madinah secara presisi.

---

### 📅 Milestone 3: CRM, Kemitraan & Transaksi Booking (Target: 3 Minggu)

_Fokus: Manajemen data pelanggan, jaringan keagenan B2B, alur penjualan inti, dan pendaftaran jemaah multi-pax._

- [ ] **M3.1: Manajemen Data Jamaah & Keagenan (CRM)**
    - [ ] Skema tabel `customers` (Data personal, NIK, jenis kelamin, status rekam medis, alamat), `passports` (Nomor paspor, tanggal rilis, tanggal expired, tempat rilis, url scan), dan `agents` (Kode agen, nama, tipe komisi: persen/nominal tetap, nilai komisi).
    - [ ] Instalasi Spatie Laravel-Medialibrary untuk manajemen upload berkas dokumen (Scan Paspor, KTP, Foto) ke local storage / S3 secara aman.
    - _Checkpoint:_ File scan paspor berhasil terunggah dan terasosiasi langsung ke record jemaah serta data agen B2B terkunci di sistem.

- [ ] **M3.2: Implementasi Business Logic Validator**
    - [ ] Pembuatan `PassportValidatorService` untuk memeriksa masa berlaku paspor secara otomatis.
    - [ ] Sistem peringatan dini (_early warning_) jika masa berlaku paspor kurang dari 6 bulan dari tanggal keberangkatan umroh rombongan.
    - _Checkpoint:_ Pengujian unit test memberikan status _Warning/Invalid_ saat memasukkan paspor yang kedaluwarsa di bawah 6 bulan dari jadwal departure.

- [ ] **M3.3: Transaksi Booking Multi-Pax**
    - [ ] Skema tabel `bookings` (Kode booking/invoice unik, branch_id, departure_id, agent_id, marketing_id, total_jamaah, status_id) dan `booking_details` (Pivot jamaah yang ikut dalam 1 transaksi booking, pilihan tipe kamar, harga riil).
    - [ ] `BookingService` untuk kalkulasi harga dinamis (menghitung otomatis berdasarkan jenis kamar yang dipilih tiap pax jemaah).
    - _Checkpoint:_ Satu transaksi booking dapat memasukkan banyak jamaah sekaligus (E.g. 1 keluarga) dengan hitungan total nilai manifest invoice yang presisi.

- [ ] **M3.4: Audit Trail Kemenag & Status Log**
    - [ ] Skema tabel `booking_status_logs` untuk mencatat rekam jejak perkembangan berkas dokumen jemaah.
    - [ ] Pemicuan event log otomatis setiap kali status dokumen/booking berubah.
    - _Checkpoint:_ Perubahan status dari "Booking Baru" -> "Paspor Diterima" -> "Visa Issued" tercatat lengkap dengan kode timestamp dan aktor staf pelaksana.

---

### 📅 Milestone 4: Keuangan Double-Entry & Akuntansi Otomatis (Target: 3 Minggu)

_Fokus: Menjaga integritas finansial biro, pembukuan balance, otomatisasi kuitansi, komisi agen, dan jurnal pembalik._

- [ ] **M4.1: Setup Chart of Accounts (CoA) & Struktur Jurnal**
    - [ ] Skema tabel `accounts` (Kode akun/CoA, nama akun, tipe: Asset/Liability/Equity/Income/Expense, is_cash, saldo), `journals` (Nomor jurnal, tanggal, ref_type, ref_id, deskripsi), dan `journal_entries` (Detail debit/kredit per baris akun).
    - [ ] Aturan Validasi Finansial Mandatori: `SUM(debit) == SUM(credit)` pada setiap posting jurnal entri.
    - _Checkpoint:_ Percobaan posting jurnal tidak seimbang melalui DB transaction diblokir otomatis oleh core service dengan melempar `AccountingException`.

- [ ] **M4.2: Modul Invoicing & Pembayaran**
    - [ ] Skema tabel `invoices` (Nomor invoice, booking_id, subtotal, diskon, pajak, total, paid_total, status) dan `invoice_payments` (Jumlah bayar, metode: cash/transfer/edc, bank_id, bukti bayar, status reversed).
    - [ ] Generator Nomor Invoice Otomatis (Format: `INV/YYYYMM/XXXX`) dan Nomor Kuitansi (Format: `KW/YYYY/MM/XXXX`).
    - [ ] Integrasi Spatie PDF untuk cetak kuitansi resmi pembayaran ber-QR Code tanda tangan digital perusahaan.
    - _Checkpoint:_ Staf kasir bisa menginput cicilan/pelunasan dan jemaah bisa mengunduh PDF kuitansi resmi dengan layout rapi.

- [ ] **M4.3: Automasi Jurnal Finansial & Komisi Agen via Eloquent Observers**
    - [ ] Pembuatan `InvoicePaymentObserver` untuk menangkap status pembayaran _approved_.
    - [ ] Mekanisme Auto-Jurnal: Sistem otomatis mendebit akun Kas/Bank dan mengkredit akun Piutang Jamaah Umroh tanpa input manual akuntan.
    - [ ] Skema tabel `agent_commissions`, `agent_commission_payments`, dan `agent_commission_payment_details`. Sistem otomatis mencatat hutang komisi kepada agen ketika booking jemaah bawaannya dinyatakan lunas.
    - _Checkpoint:_ Memasukkan data pembayaran cicilan otomatis memunculkan baris entri jurnal yang balance di buku besar dan menghitung piutang komisi agen secara _real-time_.

- [ ] **M4.4: Modul Pembatalan, Reschedule & Auto-Reverse Jurnal**
    - [ ] Logika pembatalan booking (Fitur _Cancel/Reschedule_) dengan potongan biaya pembatalan administrasi secara fleksibel.
    - [ ] Pembuatan mekanisme Jurnal Pembalik (_Auto-Reverse_) untuk memulihkan saldo akun piutang/pendapatan diterima di muka.
    - _Checkpoint:_ Transaksi batal terproses, sistem mencatat jurnal pembalik negatif/kebalikan, dan status piutang keuangan jemaah kembali bersih.

---

### 📅 Milestone 5: Handling, Kelompok Manifest & Regulasi Kemenag (Target: 3 Minggu)

_Fokus: Operasional lapangan, pembagian grup logistik, ekspor data regulasi pemerintah, dan otomatisasi notifikasi._

- [ ] **M5.1: Smart Assign Manifest Manager (Bus & Kamar)**
    - [ ] Skema tabel `manifests` (Kloter/rombongan), `manifest_groups` (Tipe: bus/room, nomor bus/kamar, kapasitas, ketua grup), dan `manifest_group_members` (Anggota bus/kamar, nomor seat).
    - [ ] Komponen antarmuka manajemen manifestasi penempatan jemaah menggunakan fitur drag-and-drop (`livewire/sortable`) secara _Lazy Loading_ untuk mencegah memori jebol.
    - _Checkpoint:_ Pengguna bisa menyeret nama jamaah ke dalam grup Bus A atau Kamar 101, kuota terupdate secara real-time tanpa reload halaman.

- [ ] **M5.2: Pemrosesan Komunal Kelompok Visa (Visa Batching)**
    - [ ] Skema tabel `visa_batches` (Pengajuan visa per tanggal batch) dan `visa_batch_items` (Status visa per jemaah, nomor visa, nomor MOFA, rejected_reason).
    - [ ] Fitur update massal status visa (Approved/Rejected) per batch pengajuan.
    - _Checkpoint:_ Mengubah status Batch Visa otomatis memperbarui status kesiapan berkas imigrasi seluruh jemaah yang terdaftar di dalam batch tersebut.

- [ ] **M5.3: Ekspor Format Kemenag Siskopatuh via Queue**
    - [ ] Integrasi pustaka `phpoffice/phpspreadsheet` ke dalam sistem.
    - [ ] Pembuatan Background Job `ExportSiskopatuhJob` untuk menyusun data manifestasi jemaah sesuai kolom template Excel resmi Kemenag PIU.
    - _Checkpoint:_ Klik tombol "Ekspor Siskopatuh" memicu background process, memunculkan tautan unduh file `.xlsx` yang lolos struktur kolom sandbox Kemenag.

- [ ] **M5.4: Notifikasi Otomatis via API WhatsApp Gateway**
    - [ ] Pembuatan driver integrasi microservice WhatsApp Gateway (Node.js + Baileys library).
    - [ ] Notifikasi Otomatis: Mengirim pesan WA otomatis saat booking baru terbuat, kuitansi cicilan terbit, atau pengingat kekurangan berkas paspor (< 30 hari sebelum berangkat).
    - _Checkpoint:_ Mengubah status dokumen jemaah menjadi "Kurang Dokumen" langsung memicu pengiriman pesan WA pengingat ke nomor handphone jemaah yang bersangkutan.

---

### 📅 Milestone 6: UAT, Hardening, & Handover (Target: 1 Minggu)

_Fokus: Pengujian akhir menyeluruh, optimalisasi query database, dan persiapan deployment produksi._

- [ ] **M6.1: Keamanan & Pengujian Beban Query (Optimization)**
    - [ ] Audit N+1 query menggunakan Laravel Telescope / Clockwork untuk mencegah kebocoran performa server.
    - [ ] Penambahan index database fisik pada kolom pencarian intensif (`passport_number`, `invoice_no`, `nik`, `name`).
- [ ] **M6.2: Final UAT & Handover Deployment**
    - [ ] Menjalankan full suite testing (Unit & Feature Tests).
    - [ ] Ekspor skema database final lengkap dengan struktur data seeder produksi awal.
    - _Checkpoint:_ Seluruh tes berstatus _Green/Passed_, aplikasi siap dideploy ke Ubuntu VPS produksi menggunakan Docker atau CyberPanel secara aman.

---

## 🚀 Petunjuk

1. PRINSIP ARSITEKTUR (TALL STACK V4)

- Presentation Layer: Volt & Form Object (state visual, navigasi, validasi).
- Domain Layer: Service Class dengan DB::transaction & Query Builder murni.
- Bahasa: Internal (Backend/DB) menggunakan Bahasa Inggris; UI (User-Facing) menggunakan Bahasa Indonesia profesional.

2. REGISTRASI KOMPONEN REUSABLE
   A. Global Premium Toast

- Layout: <x-ui.toast />
- Trigger: $this->dispatch('toast', type: 'success', title: '...', message: '...');

B. Premium Confirm Modal

- Komponen: <x-ui.confirm-modal name="..." title="..." variant="...">
- Fitur: Anti-double submission, asinkron (async/await $wire.call), & x-bind:loading.

C. Polymorphic Table Actions

- Komponen: <x-ui.table-actions :id="..." />
- Fitur: Otomatis deteksi modal atau pindah halaman (route) via :editUrl.

3. INTEGRASI FORM & VALIDASI

- Form Object: Wajib memisahkan rules validasi dari komponen view.
- Proteksi Form: Wajib gunakan wire:submit.prevent untuk mencegah page reload.

# 🎨 Standarisasi Antarmuka Komponen Tabel (Multi-Tenant ERP Engine)

Dokumen ini mengunci standardisasi visual dan struktural untuk seluruh komponen tabel di dalam ekosistem projek ini. Wajib mematuhi arsitektur ini untuk menjamin konsistensi 100% di semua modul operasional dan menjaga kompatibilitas skalabilitas multi-vendor.

---

## 🏗️ 1. Hierarki Visual Tata Letak (Layout Hierarchy)

Setiap halaman manajemen data yang berbasis tabel harus dibungkus menggunakan komponen kartu terstandarisasi dengan pola susunan sebagai berikut:

1. Pembungkus Utama (flux:card): Menggunakan padding p-6, latar belakang putih, bayangan tipis, border slate halus, dan radius sudut besar (rounded-2xl).
2. Kontrol Atas (x-ui.table-controls): Komponen tunggal reusable untuk bilah pencarian data dan tombol pemicu tambah data.
3. Wrapper Scrollbar (overflow-x-auto): Wajib membungkus tabel dengan radius rounded-xl dan border tipis untuk mengantisipasi layar responsif/mobile.

---

## 📐 2. Aturan Emas Penulisan Kolom & Sel (Grid Cell Rules)

Untuk menjaga kerapian baris data di seluruh tenant agensi, klasifikasi tata letak teks pada kolom diatur dengan disiplin ketat:

- Identifikasi: Menggunakan font-mono, tebal, teks indigo, dan latar belakang indigo lembut.
- Teks Utama: Ukuran teks sm, tebal, dan warna slate gelap.
- Lencana Status: Selalu rata tengah (text-center) menggunakan komponen flux:badge.
- Data Finansial: Selalu rata kanan (text-end) dengan format number_format mata uang lokal/internasional tebal.
- Aksi Kontrol: Selalu rata kanan (text-end) menggunakan x-ui.table-actions.

## 🏗️ 3. Arsitektur Arus Data Modul (Volt v3 + Form Object + Service Layer)

Setiap modul fitur baru di dalam sistem ERP ini wajib dipecah menjadi 4 komponen terisolasi untuk menjaga kode tetap bersih, mudah diuji, dan aman dari korupsi data operasional lintas tenant:

- [Antarmuka UI: Volt Index] -> Memicu Event lewat Event Listener
- [Formulir Modal: Volt Form] -> Tempat binding input dan pemanggilan validasi
- [Form Object] -> Tempat mengisolasi properti state, rules, dan fungsi prepareForValidation (sanitasi masker angka)
- [Master Service Class] -> Tempat eksekusi query DB murni yang dibungkus DB::transaction

### 💎 A. Standar Aturan Universal Pembuatan Service Layer (Aplikasi Global)

Setiap berkas Service di dalam direktori `app/Services/` bertindak sebagai pengendali logika bisnis utama sistem. Aturan ini wajib diterapkan secara seragam di seluruh modul ERP:

1. Kepatuhan Prosedur Atomik (Atomic Transaction)
   Setiap metode Service yang melakukan manipulasi ke lebih dari satu tabel database wajib dibungkus di dalam blok DB::transaction(function () { ... });. Jika salah satu proses gagal, seluruh rangkaian manipulasi wajib dibatalkan (rollback) secara otomatis untuk mencegah korupsi data parsial.

2. Pola Payload Tunggal (Single Parameter Pattern)
   Fungsi create dan update hanya boleh menerima satu parameter utama berupa array payload utuh (E.g., array $data atau array $payload). Jangan memecah input form menjadi parameter primitif terpisah di dalam method signature.

- Benar: public function createInvoice(array $data): void
- Salah: public function createInvoice(int $customerId, int $amount, array $items): void

3. Isolasi Otomatisasi Sisi Server (Server-Side Automation Isolation)
   Seluruh logika bisnis yang bersifat kalkulasi, otomatisasi, dan penentuan nilai turunan wajib diselesaikan di dalam Service Layer sebelum data masuk ke database. Lapisan UI/Form hanya bertugas mengirimkan data mentah.

- Contoh: Penomoran dokumen otomatis (Nomor Invoice, Kode Registrasi internal tenant), perhitungan otomatis nilai penyesuaian tarif/diskon, dan kalkulasi batasan tenggat waktu jatuh tempo berdasarkan parameter sistem.

4. Pembaruan Data Non-Destruktif (Non-Destructive Update Pattern)
   Untuk menjaga keutuhan riwayat audit (audit trail) data keuangan dan operasional masing-masing penyewa sistem, dilarang keras menggunakan pola Delete-Insert (menghapus baris data lama lalu memasukkan baris baru dengan ID berbeda) pada tabel relasi anak. Gunakan metode update tertarget atau perintah upsert bawaan database untuk memodifikasi data tanpa merusak integritas Foreign Key.

### 📦 B. Standar Aturan Livewire v3 Form Object (app/Livewire/Forms/)

1. Enkapsulasi Properti: Seluruh variabel wire:model harus dideklarasikan di dalam kelas Form ini.
2. Sanitasi Masker Nilai (prepareForValidation): Bersihkan karakter pemisah string masker teks sebelum aturan validasi dijalankan menggunakan pola regex yang aman.
3. Pemberian Nilai Default (clear): Mengembalikan status formulir ke kondisi ideal standar sistem saat modal ditutup.

### 🎨 C. Standar Aturan Komponen Volt Formulir & Indeks

1. Wajib menggunakan makro form(TemplateForm::class) di layer Volt Form.
2. Validasi wajib dipanggil lewat properti object: $this->form->validate().
3. Penyegaran data tabel indeks cukup menggunakan makro reaktif singkat: 'table-updated' => '$refresh'.
