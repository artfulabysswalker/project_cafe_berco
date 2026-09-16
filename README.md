# ☕ Cafe Berco - Point of Sale & Cafe Management System

Sistem informasi manajemen kafe modern dan komprehensif berbasis Laravel dan Livewire yang dirancang untuk mendukung operasional kafe secara menyeluruh, mulai dari pemesanan mandiri oleh pelanggan (QR Menu), sistem kasir (Point of Sale), hingga manajemen inventori, HPP resep, shift kasir, dan analitik performa bisnis bagi pemilik kafe / admin.

**Nama Kelompok:** Kelompok 5  
**Nama Repository:** CafeBerco_ManajemenKafe / project_cafe_berco

---

## 🔐 Akses & Akun Demo

Untuk mencoba dan menguji sistem secara lokal, silakan akses halaman login berikut:

- **URL Login Lokal**: [`http://127.0.0.1:8000/login`](http://127.0.0.1:8000/login)

### 📋 Kredensial Akun Default

| Peran (Role) | Email / Username | Password Default | Cakupan Hak Akses |
| :--- | :--- | :--- | :--- |
| **👑 Admin / Owner** | `admin@bercocafe.com`<br>*(username: `admin`)* | `adminberco123` | • Dashboard Analitik & Statistik Penjualan<br>• Manajemen Resep & Kalkulasi HPP (Bahan Baku)<br>• Manajemen Inventori & Stok Real-time<br>• Manajemen Akun Staff & Pegawai<br>• Konfigurasi Pajak (PB1) & Skema Diskon<br>• Pencatatan Pengeluaran (Expenses) & Laporan PDF |
| **💼 Staff / Kasir** | `nikita@bercocafe.com`<br>*(username: `nikita`)* | `123456` | • Sistem Point of Sales (POS)<br>• Manajemen Buka / Tutup Shift Kasir<br>• Pemrosesan & Penyelesaian Pesanan<br>• Preview & Cetak Struk (Receipt) Pelanggan |
| **💼 Kasir Alternatif** | `dery@bercocafe.com`<br>`robin@bercocafe.com` | `123456` | • Akun kasir resmi tambahan untuk pengujian multi-kasir |
| **👤 Customer Demo** | `user1@bercocafe.com`<br>*(username: `user1`)* | `password` | • Pemesanan Menu & Keranjang Belanja<br>• Riwayat Pesanan & Loyalty Quest / Redeem |

> 💡 **Catatan:** Password di atas adalah *placeholder default* hasil seeding database untuk kebutuhan development/demo.

### ⚙️ Cara Generate / Reset Akun Demo

Jika akun belum tersedia atau database di-reset ulang, Anda dapat meng-generate seluruh akun demo di atas dengan menjalankan perintah berikut di terminal:

```bash
# Generate hanya akun pengguna (User & Role)
php artisan db:seed --class=UserSeeder

# Atau jalankan database migration beserta seluruh seeder lengkap:
php artisan migrate:fresh --seed
```

---

## ✨ Fitur-Fitur Utama

### 1. 📊 Modul Admin & Owner
- **Dashboard & Analisis Penjualan**: Ringkasan omzet harian, laba kotor/bersih, rata-rata transaksi, dan produk terlaris.
- **HPP & Resep Produk**: Perhitungan biaya produksi per menu otomatis berdasarkan takaran bahan baku (raw materials & ingredients).
- **Inventori Stok**: Tracking stok bahan baku, stok opname, dan peringatan stok menipis.
- **Manajemen Shift Kasir**: Monitoring modal awal, total kas masuk, selisih kas, dan waktu operasional kasir.
- **Manajemen Pegawai**: Tambah staf, atur role, toggle status aktif/non-aktif, dan reset password.
- **Laporan Finansial**: Ekspor laporan laba rugi, rekap pengeluaran, dan performa penjualan dalam format PDF / Print.

### 2. 💻 Modul Point of Sale (POS) & Kasir
- **Interface Transaksi Cepat**: Katalog menu interaktif dengan filter kategori.
- **Manajemen Shift**: Verifikasi PIN/login sebelum memulai dan menutup sesi kasir.
- **Fleksibilitas Pembayaran**: Tunai, QRIS dinamis/statis, transfer bank, dan e-wallet.
- **Cetak Struk**: Format struk thermal standar dan ekspor digital PDF.

### 3. 📱 Modul Pelanggan (Customer Experience)
- **Digital QR Menu**: Akses menu instan tanpa perlu registrasi rumit (mendukung Guest Mode).
- **Keranjang & Checkout**: Penyesuaian varian menu, opsi tambahan, dan catatan khusus.
- **Loyalty & Gamification**: Daily Quest, perolehan koin/poin, voucher diskon, dan referral rewards.

---

## 🛠️ Tech Stack & Framework

| Komponen | Teknologi |
| :--- | :--- |
| **Backend** | PHP 8.2, Laravel 12 |
| **Frontend** | Blade, Livewire, Alpine.js |
| **Styling** | Vanilla CSS, Tailwind CSS |
| **Build Tool** | Vite |
| **Database** | MySQL / SQLite |
| **Storage** | Laravel Storage (Public Disk) |
| **Authentication** | Laravel Fortify / Custom Role Auth |
| **Testing** | Pest / PHPUnit |
| **Package Manager** | Composer, NPM |

---

## 📦 Panduan Instalasi & Menjalankan Aplikasi

### Prasyarat
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL atau SQLite

### Langkah-Langkah Instalasi

1. **Clone Repository**:
   ```bash
   git clone https://github.com/artfulabysswalker/project_cafe_berco.git
   cd project_cafe_berco
   ```

2. **Install Dependency PHP**:
   ```bash
   composer install
   ```

3. **Install Dependency Frontend**:
   ```bash
   npm install
   ```

4. **Setup Environment Configuration**:
   ```bash
   cp .env.example .env
   ```
   *Sesuaikan konfigurasi database di file `.env`.*

5. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

6. **Setup Database & Seeding**:
   ```bash
   php artisan migrate --seed
   ```

7. **Link Storage (untuk upload foto)**:
   ```bash
   php artisan storage:link
   ```

8. **Jalankan Aplikasi**:
   - **Terminal 1 - Asset Builder:**
     ```bash
     npm run dev
     ```
   - **Terminal 2 - Server Laravel:**
     ```bash
     php artisan serve
     ```
   - Buka browser pada alamat: [`http://127.0.0.1:8000`](http://127.0.0.1:8000)

---

## 📁 Struktur Folder Utama

```
project_cafe_berco/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/            # Controller Admin (Analytics, Category, Expense, HPP)
│   │   └── Customer/         # Controller Customer
│   ├── Livewire/             # Komponen Livewire (SalesToday, Inventory, etc.)
│   ├── Models/               # Model Eloquent (Menu, Order, Shift, RawMaterial, etc.)
│   └── Services/             # Business logic layer (Payment, QRIS, etc.)
├── database/
│   ├── migrations/           # File migrasi database
│   └── seeders/              # Seeder data resmi (UserSeeder, MenuSeeder, etc.)
├── resources/
│   ├── views/
│   │   ├── admin/            # View Dashboard, Menu, HPP, Shift, Expense, Staff
│   │   └── Customerviews/    # View Customer, Cart, Checkout, Loyalty
│   ├── css/                  # Styling
│   └── js/                   # JavaScript & Alpine.js
├── routes/
│   ├── web.php               # Web routing (Admin, Customer, POS, Auth)
│   └── api.php               # API routing
└── vite.config.js            # Konfigurasi Vite
```

---

## 👥 Anggota Kelompok

| No | Nama | Role |
| :--: | :--- | :--- |
| 1 | **Taruna Isra** | Product Owner |
| 2 | **Baruna Akbar Rizki** | Scrum Master |
| 3 | **Matthew Herdiansyah** | Developer |
| 4 | **Ahmad Bachtiar Raflyansyah** | Developer |

---

## 📄 Lisensi

Project ini dikembangkan untuk kebutuhan operasional Cafe Berco. Seluruh hak cipta dilindungi.
