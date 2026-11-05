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
| **Payment Gateway** | Midtrans Core API (QRIS, Sandbox) |
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

## 💳 Integrasi Pembayaran QRIS (Midtrans Sandbox)

Aplikasi terhubung ke **Midtrans Core API** untuk pembayaran QRIS. Pelanggan checkout metode `qris` → dibuatkan QR dinamis → setelah pelanggan bayar, Midtrans mengirim notifikasi ke webhook (`POST /webhooks/midtrans`) yang otomatis menandai order **LUNAS** dan menampilkannya sebagai **Log Transfer QRIS** di dashboard kasir.

### Konfigurasi Environment

Tambahkan variabel berikut di `.env`:

```env
MIDTRANS_SERVER_KEY=SB-xxxxxxxxxxxxxxxx
MIDTRANS_CLIENT_KEY=SB-xxxxxxxxxxxxxxxx
MIDTRANS_IS_PRODUCTION=false
MIDTRANS_QRIS_ACQUIRER=gopay
```

- **Server Key** & **Client Key** diambil dari [dashboard Midtrans Sandbox](https://dashboard.sandbox.midtrans.com/settings/account_info) (awalan `SB-` = sandbox).
- `MIDTRANS_QRIS_ACQUIRER` adalah acquirer cadangan jika QRIS utama gagal dibuat.
- Untuk menghubungkan webhook, daftarkan URL publik (mis. via **ngrok**) di [dashboard Midtrans](https://dashboard.sandbox.midtrans.com/settings/configuration) → *Payment Notification*:
  ```
  https://<url-anda>/webhooks/midtrans
  ```

### Alur Singkat

1. Pelanggan memilih metode `qris` saat checkout.
2. Backend memanggil **Midtrans Core API** (`POST /charge` QRIS) → menerima `qr_code_url`.
3. QR ditampilkan pada `/order/{id}/qris` (sisa waktu tampil sebagai countdown).
4. Midtrans mengirim **notifikasi webhook** (verifikasi **signature SHA512** → aman dari pemalsuan).
5. Order otomatis ditandai lunas, log tampil di dashboard kasir.

### Menjalankan Webhook Secara Lokal (ngrok)

Midtrans tidak bisa menjangkau `localhost`, jadi webhook perlu URL publik.

```bash
# terminal 1 — aplikasi
php artisan serve

# terminal 2 — tunnel publik
ngrok http 8000
```

Kemudian daftarkan URL ngrok di dashboard Midtrans Sandbox:
`Settings → Payment → Notification URL` → `https://<subdomain-ngrok>/webhooks/midtrans`

> Gunakan `php artisan serve --host=0.0.0.0` bila HP pelanggan juga harus mengakses halaman QR.

### Perintah Bantu (artisan)

```bash
php artisan qris:test              # buat order + charge QRIS uji coba
php artisan qris:check {order_id}  # cek status pembayaran suatu order
php artisan qris:debug {order_id}  # telusuri transaksi & QR yang tersimpan
php artisan qris:test-webhook {order_id}  # simulasikan notifikasi pembayaran
```

### Pengujian (Postman)

Koleksi siap pakai: [`postman/berco-midtrans-qris.postman_collection.json`](postman/berco-midtrans-qris.postman_collection.json).

Cara pakai:
1. Impor file `.json` ke Postman.
2. Set variabel koleksi `BASE_URL`, `SERVER_KEY`, dan `CUSTOMER_TOKEN` (cookie setelah login).
3. Jalankan request berurutan (Buat Pesanan → Ambil data payment_log → Webhook Settlement).
4. `signature_key` webhook dihitung otomatis oleh pre-request script.

Skenario yang dicakup: settlement (sukses), idempotency (kirim ulang), signature salah (403), nominal tidak cocok (400), dan expire.

### Pengujian Otomatis

Seluruh skenario (settlement, idempotency, signature salah, nominal tidak cocok, expired, order batal) dicakup oleh tes otomatis:

```bash
php artisan test --filter=MidtransQrisTest      # webhook & alur QRIS
php artisan test --filter=CashierPaymentLogTest # log transfer di dashboard kasir
```

### Troubleshooting

| Gejala | Penyebab & Solusi |
| --- | --- |
| Halaman QRIS menampilkan "QRIS gagal dibuat" | `MIDTRANS_SERVER_KEY` kosong/typo. Ambil ulang dari dashboard Sandbox (awalan `SB-`). |
| QR tidak tampil (gambar rusak) | `qr_code_url` kosong karena respons Midtrans tidak punya action `generate-qr-code`. Cek `php artisan qris:debug {order_id}`. |
| Webhook tidak pernah sampai | Midtrans hanya bisa memanggil URL publik — pakai ngrok, dan pastikan Notification URL sudah diset di dashboard Sandbox. |
| Webhook membalas `403 invalid_signature` | `SERVER_KEY` di server tidak sama dengan yang dipakai dashboard, atau `gross_amount` berbeda. Bandingkan `MIDTRANS_SERVER_KEY` dengan yang ada di `.env`. |
| Webhook membalas `400 amount_mismatch` | Nominal di notifikasi ≠ nominal di `payment_logs`. Order **tidak** ditandai lunas (ini perilaku yang benar). |
| Order belum lunas padahal sudah dibayar | Buka "Cek Status Pembayaran" di halaman QRIS (menarik status langsung dari API Midtrans), atau jalankan `php artisan qris:check {order_id}`. |

### Fallback bila QRIS Sandbox Gagal

QRIS Core API di sandbox kadang tidak mengembalikan QR (akun sandbox belum aktif, atau
acquirer bermasalah). Urutan langkah yang bisa dicoba:

1. **Ganti acquirer** di `.env`:
   ```env
   MIDTRANS_QRIS_ACQUIRER=airpay shopee
   ```
   lalu `php artisan config:clear`.
2. **Gunakan Midtrans Snap Sandbox** (`payment_type` tidak dipaksa `qris`) untuk mendemokan
   alur pembayaran, lalu tampilkan halaman Snap di frontend.
3. **Periksa log** `storage/logs/laravel.log` — pesan error asli dari Midtrans tersimpan di
   sini (baris `Midtrans QRIS charge gagal`).

> Fallback Snap **tidak** menampilkan QR statis; untuk demo QRIS sungguhan, pastikan akun
> sandbox Midtrans sudah aktif dan Server Key benar.

### Catatan Keamanan

- **Server Key hanya dipakai di backend.** Jangan pernah mengirim `MIDTRANS_SERVER_KEY` ke
  Blade/frontend. Client Key (`MIDTRANS_CLIENT_KEY`) aman dipublikasikan.
- Setiap notifikasi webhook diverifikasi lewat `signature_key`
  (`SHA512(order_id + status_code + gross_amount + serverKey)`) sebelum status apa pun diubah.
- Endpoint `POST /webhooks/midtrans` dikecualikan dari CSRF (permintaan berasal dari server
  Midtrans, bukan browser) — verifikasi signature adalah penjaganya.

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
