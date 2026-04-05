# Laravel ISP Billing System

Sistem billing ISP built with Laravel 10, Blade, Bootstrap, MySQL/MariaDB, dan integrasi FreeRADIUS.

## Fitur Utama

- Autentikasi pengguna admin dan teknisi
- Role-based middleware (`admin`, `technisi`)
- CRUD customer ISP dengan sinkronisasi ke FreeRADIUS (`radcheck`, `radreply`)
- CRUD paket internet dengan perhitungan `Mikrotik-Rate-Limit`
- Monitoring trafik dari tabel `radacct`
- Generate tagihan bulanan dan histori pembayaran
- Cetak invoice PDF dengan Laravel DomPDF
- Responsive admin panel menggunakan Bootstrap
- Chart.js untuk visualisasi trafik
- Seeder data awal dan migration lengkap

## Struktur Database

- `users` (admin/teknisi)
- `customers` (pelanggan ISP)
- `packages`
- `invoices`
- `payments`
- `radcheck`, `radreply`, `radacct`

## Setup

1. Salin environment file:
   ```bash
   cp .env.example .env
   ```

2. Ubah konfigurasi database di `.env`:
   ```ini
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=isp_billing
   DB_USERNAME=root
   DB_PASSWORD=

   RADIUS_DB_CONNECTION=radius
   RADIUS_DB_HOST=127.0.0.1
   RADIUS_DB_PORT=3306
   RADIUS_DB_DATABASE=radius
   RADIUS_DB_USERNAME=root
   RADIUS_DB_PASSWORD=
   ```

3. Install dependency Composer:
   ```bash
   composer install
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Jalankan migrasi dan seeder:
   ```bash
   php artisan migrate --seed
   ```

6. Mulai server development:
   ```bash
   php artisan serve
   ```

## User Default

- Admin: `admin@isp.local` / `password`
- Teknisi: `tech@isp.local` / `password`

## Integrasi FreeRADIUS

- Konfigurasi koneksi FreeRADIUS berada di `config/database.php` pada koneksi `radius`
- Service sinkronisasi ada di `app/Services/FreeRadiusService.php`
- Ketika pelanggan dibuat atau diubah, sistem otomatis memperbarui `radcheck` dan `radreply`
- Saat pelanggan dinonaktifkan, sistem membuat atribut `Auth-Type := Reject`

## Integrasi Mikrotik

- Tersedia skeleton service di `app/Services/MikrotikService.php`
- Gunakan library RouterOS API PHP atau socket jika ingin mengaktifkan CoA / disconnect pengguna

## Routes Penting

- `/login` - halaman login
- `/dashboard` - dashboard statistik
- `/customers` - manajemen pelanggan
- `/packages` - manajemen paket
- `/invoices` - daftar tagihan
- `/monitoring` - monitoring trafik

## Catatan

- Pastikan koneksi FreeRADIUS sudah tersedia jika ingin menggunakan fitur sinkronisasi
- Untuk penggunaan produksi, gunakan SSL dan pengaturan keamanan tambahan
