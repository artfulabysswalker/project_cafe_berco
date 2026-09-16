# ☕ Cafe Berco - Point of Sale & Cafe Management System

Sistem manajemen kafe modern berbasis Laravel dan Livewire yang dirancang untuk mendukung operasional kafe secara menyeluruh, mulai dari pemesanan mandiri oleh pelanggan (QR Menu), sistem kasir (Point of Sale), hingga manajemen inventori, HPP resep, shift kasir, dan analitik performa bisnis bagi pemilik kafe / admin.

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

---

### ⚙️ Cara Generate / Reset Akun Demo

Jika akun belum tersedia atau database di-reset ulang, Anda dapat meng-generate seluruh akun demo di atas dengan menjalankan perintah berikut di terminal:

```bash
# Generate hanya akun pengguna (User & Role)
php artisan db:seed --class=UserSeeder

# Atau jalankan database migration beserta seluruh seeder lengkap:
php artisan migrate:fresh --seed
```

---

## 🚀 Fitur Utama Sistem

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

## 🛠️ Panduan Instalasi Lokal

1. **Clone Repositori**:
   ```bash
   git clone https://github.com/artfulabysswalker/project_cafe_berco.git
   cd project_cafe_berco
   ```

2. **Install Dependensi Backend & Frontend**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Sesuaikan konfigurasi koneksi database MySQL pada file `.env`.*

4. **Migrasi Database & Seeder**:
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**:
   ```bash
   # Terminal 1 - Asset Builder
   npm run dev

   # Terminal 2 - Server Laravel
   php artisan serve
   ```
   Buka browser pada alamat: `http://127.0.0.1:8000`

---

## 📄 Lisensi

Project ini dikembangkan untuk kebutuhan operasional Cafe Berco. Seluruh hak cipta dilindungi.
