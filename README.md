# Cafe Berco

Sistem manajemen kafe dan menu berbasis Laravel dengan antarmuka admin untuk mengelola menu, diskon, pesanan, dan laporan.

## Fitur Utama

- Panel admin untuk menambah, mengedit, dan menghapus menu.
- Upload foto menu dan mengganti gambar saat mengedit.
- Pengaturan diskon menu.
- Riwayat pesanan dan cetak struk.
- Halaman pelanggan untuk melihat katalog menu, filter harga, dan review.
- Autentikasi pengguna dengan Laravel Fortify.

## Tech Stack

- Backend: PHP 8.2, Laravel 12
- Frontend: Blade, Livewire, Alpine.js, Tailwind CSS, Vite
- Database: MySQL / SQLite (Laravel dapat dikonfigurasi sesuai environment)
- Storage: Laravel Storage (public disk untuk foto menu)
- Testing: Pest

## Instalasi

1. Clone repository:
   ```bash
   git clone <repo-url>
   cd project_cafe_berco
   ```
2. Install dependency PHP:
   ```bash
   composer install
   ```
3. Install dependency frontend:
   ```bash
   npm install
   ```
4. Salin file environment dan set konfigurasi:
   ```bash
   cp .env.example .env
   ```
5. Buat application key:
   ```bash
   php artisan key:generate
   ```
6. Jalankan migrasi database:
   ```bash
   php artisan migrate
   ```
7. Jalankan build asset atau mode dev:
   ```bash
   npm run build
   ```
   atau
   ```bash
   npm run dev
   ```

## Menjalankan Aplikasi

- Jalankan server Laravel:
  ```bash
  php artisan serve
  ```
- Akses aplikasi di `http://127.0.0.1:8000`

## Struktur Folder Penting

- `app/` - Kode backend aplikasi (Controller, Models, Services)
- `config/` - Konfigurasi Laravel
- `database/` - Migration, seeder, dan factory
- `public/` - Aset publik dan entry point aplikasi
- `resources/views/` - Tampilan Blade
- `routes/` - Definisi route aplikasi
- `storage/` - Storage local dan cache

## Catatan Tambahan

- Silakan sesuaikan `.env` untuk koneksi database dan storage.
- Jika menggunakan `storage` untuk upload foto menu, pastikan menjalankan:
  ```bash
  php artisan storage:link
  ```
- Jika repository diatur private, pastikan hak akses tim sudah diatur.

## Kontak / Anggota

- Taruna Isra (Produck OWner)
- Baruna Akbar Rizki (Schrum Master)
- Matthew Herdiansyah (Developer Tean)
- Ahmad Bachtiar Raflyansyah (Developer team)
