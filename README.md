# Cafe Berco - Sistem Manajemen Kafe & Menu

## 📋 Deskripsi Proyek

**Cafe Berco** adalah sistem informasi manajemen kafe yang komprehensif, dirancang untuk memudahkan pengelolaan menu, pesanan, dan laporan penjualan. Aplikasi ini menyediakan panel admin yang intuitif untuk mengelola katalog menu dengan foto, diskon, dan pesanan pelanggan, serta halaman pelanggan yang user-friendly untuk browsing menu dan memberikan review.

**Nama Kelompok:** Cafe Berco Project Group  
**Nama Repository:** CafeBerco_ManajemenKafe

---

## ✨ Fitur-Fitur Utama

### Fitur Admin
- ✅ Panel dashboard untuk melihat ringkasan penjualan dan pesanan
- ✅ Manajemen menu (CRUD): tambah, edit, hapus menu kafe
- ✅ Upload dan manajemen foto menu dengan penggantian gambar
- ✅ Pengaturan diskon per menu
- ✅ Riwayat pesanan lengkap dengan detail pelanggan
- ✅ Cetak struk/invoice pesanan
- ✅ Laporan penjualan dan statistik

### Fitur Pelanggan
- ✅ Katalog menu dengan deskripsi lengkap dan foto
- ✅ Filter menu berdasarkan harga dan kategori
- ✅ Sistem rating dan review menu
- ✅ Riwayat pesanan pribadi
- ✅ Autentikasi user yang aman

---

## 🛠️ Tech Stack & Framework

| Komponen | Teknologi |
|----------|-----------|
| **Backend** | PHP 8.2, Laravel 12 |
| **Frontend** | Blade, Livewire, Alpine.js |
| **Styling** | Tailwind CSS |
| **Build Tool** | Vite |
| **Database** | MySQL / SQLite |
| **Storage** | Laravel Storage (Public Disk) |
| **Authentication** | Laravel Fortify |
| **Testing** | Pest |
| **Package Manager** | Composer, NPM |

**Language Composition:**
- PHP: 57.5%
- Blade: 22.5%
- Hack: 19.4%
- Other: 0.6%

---

## 📦 Panduan Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL atau SQLite

### Langkah-Langkah Instalasi

#### 1. Clone Repository
```bash
git clone https://github.com/artfulabysswalker/project_cafe_berco.git
cd project_cafe_berco
```

#### 2. Install Dependency PHP
```bash
composer install
```

#### 3. Install Dependency Frontend
```bash
npm install
```

#### 4. Setup Environment Configuration
```bash
cp .env.example .env
```
Sesuaikan konfigurasi database di file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cafe_berco
DB_USERNAME=root
DB_PASSWORD=
```

#### 5. Generate Application Key
```bash
php artisan key:generate
```

#### 6. Setup Database
```bash
# Jalankan migrasi
php artisan migrate

# (Optional) Jalankan seeder untuk data dummy
php artisan db:seed
```

#### 7. Link Storage (untuk upload foto)
```bash
php artisan storage:link
```

#### 8. Build Frontend Assets
```bash
# Mode production
npm run build

# Atau mode development dengan watch
npm run dev
```

---

## 🚀 Cara Menjalankan Aplikasi

### Development Mode

**Terminal 1 - Jalankan Server Laravel:**
```bash
php artisan serve
```

**Terminal 2 - Jalankan Build Tools (jika menggunakan npm run dev):**
```bash
npm run dev
```

### Akses Aplikasi
- **URL Aplikasi:** `http://127.0.0.1:8000`
- **Aplikasi dapat diakses di browser** setelah server Laravel berjalan

---

## 👤 Akun Demo

### Admin Account
| Field | Value |
|-------|-------|
| Email | admin1@email.com |
| Password | password |

> **Catatan:** Ubah password ini setelah login untuk keamanan yang lebih baik.

---

## 📁 Struktur Folder Penting

```
project_cafe_berco/
├── app/                      # Kode backend utama
│   ├── Http/Controllers/     # Controller untuk menangani request
│   ├── Models/               # Model Eloquent untuk database
│   └── Services/             # Business logic layer
├── config/                   # File konfigurasi Laravel
├── database/
│   ├── migrations/           # File migrasi database
│   ├── seeders/              # File seeder untuk data dummy
│   └── factories/            # Model factory untuk testing
├── public/                   # Aset publik dan entry point (index.php)
├── resources/
│   ├── views/                # Template Blade
│   ├── css/                  # Styling Tailwind
│   └── js/                   # JavaScript & Alpine.js
├── routes/                   # Definisi route aplikasi
│   ├── web.php               # Web routes
│   └── api.php               # API routes (jika ada)
├── storage/                  # Storage local untuk upload file
├── tests/                    # Unit dan feature tests dengan Pest
├── .env.example              # Template environment variables
├── composer.json             # Dependencies PHP
├── package.json              # Dependencies JavaScript
└── vite.config.js            # Konfigurasi Vite
```

---

## ℹ️ Informasi Tambahan

### Konfigurasi Penting

1. **Database Configuration:**
   - Sesuaikan `.env` dengan database yang digunakan (MySQL/SQLite)
   - Pastikan credentials database benar

2. **Storage Configuration:**
   - Untuk upload foto menu, pastikan `php artisan storage:link` sudah dijalankan
   - File foto akan disimpan di `storage/app/public/`

3. **Mail Configuration (Optional):**
   - Jika ingin notifikasi email, update `MAIL_*` di `.env`

### Development Tips

- **Reload Assets:** Gunakan `npm run dev` untuk auto-reload saat development
- **Database Reset:** `php artisan migrate:refresh` untuk reset database
- **Tinker Shell:** `php artisan tinker` untuk interactive debugging
- **Database Seeding:** `php artisan db:seed` untuk generate data dummy

### Repository Visibility

Repository ini dapat diatur sebagai **Private** untuk keamanan. Pastikan semua anggota tim sudah memiliki akses dengan role yang sesuai:
- 👤 **Admin:** Full access
- 👥 **Developer:** Read + Write access
- 👁️ **Viewer:** Read only access

---

## 👥 Anggota Kelompok

| No | Nama | Role |
|:--:|------|------|
| 1 | Taruna Isra | Product Owner |
| 2 | Baruna Akbar Rizki | Scrum Master |
| 3 | Matthew Herdiansyah | Developer |
| 4 | Ahmad Bachtiar Raflyansyah | Developer |

---

## 📝 Catatan Penting

- ✅ Pastikan repository ini sudah dibuat sesuai format: `NamaKelompok_JudulProyek`
- ✅ Semua anggota tim harus memiliki akses ke repository
- ✅ Gunakan `.gitignore` yang tepat (vendor, node_modules, .env)
- ✅ Commit secara teratur dengan pesan yang jelas
- ✅ Jalankan tests sebelum push: `php artisan test` atau `npm run test`
- ✅ Repository dapat diatur sebagai Private untuk keamanan data

---

## 🔗 Links Berguna

- [Laravel Documentation](https://laravel.com/docs)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [Livewire Documentation](https://livewire.laravel.com)
- [Alpine.js Documentation](https://alpinejs.dev)

---

**Last Updated:** 2026-06-30  
**Status:** ✅ Production Ready
