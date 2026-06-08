# UI Dashboard Penjualan & Analytics - Café Berco

Dokumentasi lengkap fitur-fitur dashboard untuk manajemen penjualan, analytics produk, dan konfigurasi pajak/diskon.

---

## 📊 1. Penjualan Hari Ini (Sales Today Dashboard)

### Deskripsi
Dashboard real-time yang menampilkan ringkasan penjualan harian dengan metrik kunci untuk owner/kasir melakukan monitoring harian.

### Fitur Utama

#### Summary Cards (4 Kartu Metrik)
- **Jumlah Transaksi**: Total transaksi terbayar dalam 1 hari
- **Total Pendapatan**: Total harga akhir (final_total) semua transaksi
- **Keuntungan**: Total profit_margin dari semua transaksi
- **Charge (PB1)**: Total pajak (tax_amount) + charge yang dikenakan

#### Purchase History Table
Tabel detail semua transaksi hari ini dengan kolom:
- **Waktu**: Jam transaksi
- **Pelanggan**: Nama pelanggan / "Guest"
- **Produk**: Daftar produk yang dibeli
- **Subtotal**: Harga sebelum pajak/diskon
- **Tax**: Pajak yang dikenakan
- **Diskon**: Diskon yang diterapkan
- **Total**: Harga akhir pembayaran
- **Profit**: Keuntungan dari transaksi

### Access
- **Route**: `/admin/sales-today`
- **Route Name**: `admin.sales.today`
- **Middleware**: `admin` (admin/staff)
- **Component**: `SalesToday.php` (Livewire)

### Data Source
Model: `Order`, `OrderItem`, `Menu`
- Filter: `status_pembayaran = 'paid'` dan `tanggal = tanggal yang dipilih`
- Relation: order -> items -> menu

### Fungsionalitas Interaktif
- **Date Picker**: Pilih tanggal untuk melihat penjualan hari lain
- **Refresh Button**: Reload data terbaru
- **Real-time Calculation**: Semua nilai dihitung secara otomatis dari database

---

## 📈 2. Penjualan Produk & Analytics

### Deskripsi
Dashboard analitik mendetail tentang performa penjualan produk dengan kemampuan filter berdasarkan periode (Harian/Bulanan/Tahunan). Dirancang untuk owner mendapatkan insight bisnis dan mengoptimalkan strategi promosi menu.

### Fitur Utama

#### Period Selector
Pilih periode analisa:
- **Harian (📅)**: Analisis penjualan hari spesifik + date picker
- **Bulanan (📆)**: Analisis penjualan bulan spesifik + date picker
- **Tahunan (📊)**: Analisis penjualan tahun spesifik (tanpa date picker)

#### Summary Statistics (4 Kartu)
- **Total Produk Terjual**: Jumlah seluruh unit produk terjual
- **Total Pendapatan**: Total revenue dari penjualan
- **Total Pesanan**: Berapa banyak transaksi/order
- **Rata-rata per Pesanan**: 
  - Item per pesanan
  - Revenue per pesanan

#### 🏆 Produk Terlaris (Top 10 Products)
Tabel produk dengan performa tertinggi:
- **Peringkat**: #1-3 dengan emoji 🥇🥈🥉, #4+ dengan angka
- **Nama Menu**: Nama produk
- **Terjual**: Berapa unit terjual
- **Pesanan**: Berapa kali produk ini dipesan
- **Rata-rata/Pesanan**: Jumlah unit rata-rata per pesanan
- **Total Revenue**: Total pendapatan dari produk ini

#### 📊 Semua Produk Penjualan
Tabel lengkap semua produk dengan penjualan:
- **Produk**: Nama menu
- **Jumlah Terjual**: Total unit terjual
- **Pesanan**: Berapa kali dipesan
- **Harga Rata-rata**: Average price per unit
- **Total Revenue**: Total pendapatan

### Access
- **Route**: `/admin/product-analytics`
- **Route Name**: `admin.product.analytics`
- **Middleware**: `admin` (admin/staff)
- **Component**: `ProductSalesAnalytics.php` (Livewire)

### Data Source
Model: `OrderItem`, `Order`, `Menu`
- Filter: `order.status_pembayaran = 'paid'`
- Period: Daily/Monthly/Yearly based on `order_items.created_at`

### Fungsionalitas Interaktif
- **Period Buttons**: Switch antara harian/bulanan/tahunan
- **Date Picker**: Ubah tanggal/bulan untuk analisis (otomatis hidden untuk yearly)
- **Sorting**: Otomatis sort by quantity descending (terlaris di atas)

### Business Insights yang Bisa Didapat
1. **Menu Terlaris**: Produk mana yang paling banyak dipesan
2. **Menu Tertinggi Revenue**: Produk mana yang generate revenue paling besar
3. **Produk Konsisten**: Produk dengan ordering frequency tinggi
4. **Optimization Opportunity**: Produk slow-moving untuk perlu promosi
5. **Trend Analysis**: Membandingkan performa daily/monthly/yearly

---

## 🏷️ 3. Konfigurasi Pajak & Diskon (Dynamic Tax & Discount Configuration)

### Deskripsi
Admin panel untuk mengatur konfigurasi pajak (PB1) dan skema diskon yang dapat diterapkan kasir secara instant pada checkout. Mendukung multiple tax configs dan discount schemes dengan validasi dan status management.

### Fitur Utama

#### A. Konfigurasi Pajak (💰 Tax Configuration)

##### View Mode (Default)
Menampilkan:
- **Pajak Aktif Saat Ini**: Card biru yang highlight tax configuration yang sedang aktif
- **Daftar Semua Pajak**: Tabel semua konfigurasi pajak dengan status

##### Setiap Tax Config Menampilkan
- Nama pajak
- Persentase pajak (%)
- Deskripsi
- Status (Aktif/Tidak Aktif)
- Buttons: Aktifkan (jika tidak aktif), Edit, Hapus

##### Edit Mode - Form Tambah/Edit Pajak
Fields:
- **Nama Pajak** (required): Misal "PB1 Umum", "Pajak Khusus"
- **Persentase Pajak** (required): 0-100%, decimal allowed
- **Deskripsi** (optional): Penjelasan tentang pajak ini

Actions:
- **Simpan**: Create or update tax config
- **Batal**: Kembali ke view mode

#### B. Skema Diskon (🏷️ Discount Scheme)

##### View Mode (Default)
Menampilkan daftar semua skema diskon aktif dengan detail:

##### Setiap Discount Scheme Menampilkan
- Kode diskon (badge code)
- Nama skema
- Tipe diskon (% atau Rp)
- Nilai diskon
- Minimum pembelian (jika ada)
- Status (Aktif/Tidak Aktif)
- Periode berlaku (valid_from - valid_until)
- Buttons: Aktifkan/Nonaktifkan, Edit, Hapus

##### Edit Mode - Form Tambah/Edit Diskon
Fields:
- **Kode Diskon** (required): Unique code, misal "DISKON10"
- **Nama Diskon** (required): Deskripsi human-readable, misal "Diskon 10%"
- **Tipe Diskon** (required): Dropdown
  - Persentase (%)
  - Nominal (Rp)
- **Nilai Diskon** (required): Numeric, sesuai tipe
- **Minimum Pembelian** (optional): Rp, diskon hanya berlaku jika subtotal >= ini
- **Max Diskon** (optional): Rp, batas maksimal diskon (untuk % discount)
- **Max Penggunaan** (optional): Berapa kali diskon bisa digunakan (0 = unlimited)

Actions:
- **Simpan**: Create or update discount scheme
- **Batal**: Kembali ke view mode

### Access
- **Route**: `/admin/config/tax-discount`
- **Route Name**: `admin.config.tax-discount`
- **Middleware**: `admin` (admin only, gunakan is_admin jika ingin hanya owner)
- **Component**: `TaxDiscountConfiguration.php` (Livewire)

### Data Source
Models: `TaxConfiguration`, `DiscountScheme`
- Filter: `id_user = Auth::id()` (per user/owner)
- Relationship: User -> Taxes, User -> Discounts

### Database Schema Requirements

#### tax_configurations table
```sql
- id_tax_config (PK)
- name
- tax_percentage (decimal 5,2)
- description (text)
- is_active (boolean)
- effective_from (datetime, nullable)
- effective_until (datetime, nullable)
- id_user (FK)
- created_at, updated_at
```

#### discount_schemes table
```sql
- id_discount_scheme (PK)
- code (string, unique)
- name (string)
- description (text)
- discount_type (enum: 'percentage', 'fixed')
- discount_value (decimal 10,2)
- min_purchase (decimal 12,2, nullable)
- max_discount (decimal 12,2, nullable)
- max_uses (int, nullable)
- times_used (int, default 0)
- is_active (boolean)
- valid_from (datetime)
- valid_until (datetime)
- id_user (FK)
- created_at, updated_at
```

### Fungsionalitas Interaktif
- **Activate/Deactivate**: Toggle status active/inactive
- **Edit**: Ubah konfigurasi existing
- **Delete**: Hapus konfigurasi (with confirmation)
- **Form Validation**: Real-time validation dengan Livewire
- **Success Messages**: Notifikasi setelah operasi berhasil

### Integration Points

#### Untuk Kasir (Checkout Page)
1. **Ambil Pajak Aktif**:
```php
$activeTax = TaxConfiguration::getActiveConfiguration();
$taxAmount = $activeTax->calculateTax($subtotal);
```

2. **Validasi & Terapkan Diskon**:
```php
$discount = DiscountScheme::where('code', $appliedCode)->first();
$validation = $discount->isValid($subtotal);
if ($validation['valid']) {
    $discountAmount = $discount->calculateDiscount($subtotal);
}
```

#### Untuk Order Creation
- `id_tax_config`: Store reference ke tax config yang digunakan
- `id_discount_scheme`: Store reference ke discount scheme (jika ada)
- Auto-calculate: `tax_amount`, `discount_amount`, `profit_margin`

### Business Rules

#### Tax Configuration
- Hanya 1 tax bisa active sekaligus
- Activate tax otomatis deactivate tax lain
- Persentase: 0-100%
- Efektif date (optional): untuk scheduled tax changes

#### Discount Scheme
- Bisa multiple discount active sekaligus
- Validasi: active status, periode, min purchase, max uses
- Priority: jika multiple diskon cocok, ambil diskon terbesar
- Calculation:
  - Percentage: `discount = (subtotal * value) / 100`
  - Fixed: `discount = value` (tapi max = max_discount jika set)

---

## 🔌 Integration dengan Sistem Existing

### 1. Order Model Update
Order sudah punya fields:
```php
- id_tax_config
- id_discount_scheme
- tax_amount
- discount_amount
- profit_margin
- cost_of_goods
```

### 2. Checkout/POS System Integration
Kasir perlu:
1. **Get Active Tax**: Otomatis load tax active current time
2. **Apply Discount**: Input kode, system validate dan hitung
3. **Show Final Total**: 
   - Subtotal
   - Tax (auto dari active config)
   - Discount (jika ada)
   - Final Total = subtotal + tax - discount

### 3. API/AJAX untuk Kasir
```
POST /api/validate-discount
- Code: string
- Subtotal: decimal
Response: { valid: bool, message: string, discount_amount: decimal }
```

---

## 📱 Responsive Design
Semua komponen menggunakan Tailwind CSS dengan:
- **Mobile**: Single column, optimized untuk touch
- **Tablet**: 2-column grid
- **Desktop**: Multi-column layout dengan full details

## 🎨 Visual Hierarchy
- **Icons**: Emoji + Font Awesome untuk visual cues
- **Colors**: 
  - Blue: Info/Actions
  - Green: Success/Profit/Revenue
  - Red: Danger/Negative
  - Orange: Warning/Tax
  - Purple: Diskon
- **Typography**: Bold headings, smaller descriptions

---

## 🛠️ Technical Stack
- **Backend**: Laravel 11, Livewire 3
- **Frontend**: Blade, Tailwind CSS
- **Database**: MySQL
- **Real-time**: Livewire reactive properties

---

## 📋 Checklist Implementasi

- [x] SalesToday Livewire Component
- [x] ProductSalesAnalytics Livewire Component
- [x] TaxDiscountConfiguration Livewire Component
- [x] View templates untuk ketiga fitur
- [x] Routes integration
- [x] Database models validation
- [ ] API endpoint untuk kasir (optional enhancement)
- [ ] Email notification untuk discount activation (optional)
- [ ] Export analytics ke CSV/PDF (optional)

---

## 🚀 Cara Penggunaan

### 1. Akses Penjualan Hari Ini
1. Login sebagai admin/staff
2. Sidebar > Analytics & Finance > Penjualan Hari Ini
3. Atau direct: `/admin/sales-today`
4. Ubah tanggal dengan date picker untuk melihat hari lain
5. Klik Refresh untuk reload data terbaru

### 2. Analisis Produk Penjualan
1. Sidebar > Analytics & Finance > Penjualan Produk
2. Atau direct: `/admin/product-analytics`
3. Pilih periode: Harian/Bulanan/Tahunan
4. Lihat produk terlaris di section Top Products
5. Lihat semua produk di section Semua Produk

### 3. Konfigurasi Pajak & Diskon
1. Admin sidebar > Analytics & Finance > Konfigurasi Pajak & Diskon
2. Atau direct: `/admin/config/tax-discount`

#### Tambah Pajak Baru:
1. Klik "Tambah Pajak"
2. Isi form: Nama, Persentase, Deskripsi
3. Klik Simpan
4. Klik Aktifkan pada pajak yang ingin digunakan

#### Tambah Diskon Baru:
1. Klik "Tambah Diskon"
2. Isi form: Code, Nama, Tipe, Nilai, etc
3. Klik Simpan
4. Klik Aktifkan untuk mengaktifkan

---

## 🐛 Troubleshooting

### Data tidak muncul?
- Check: `status_pembayaran` harus "paid"
- Check: Tanggal harus sesuai
- Check: User harus punya order yang terbayar

### Lifeware tidak responsive?
- Clear browser cache
- Check: `npm run build` sudah dijalankan
- Check: Livewire CSS/JS sudah loaded di layout

### Tax/Discount tidak tersimpan?
- Check: User sudah login (Auth::id())
- Check: Form validation (lihat error messages)
- Check: Database connection

---

## 📞 Support & Enhancements

### Future Enhancements:
1. Chart visualization untuk sales trend
2. Export analytics to PDF/Excel
3. Email notification untuk anomali (low sales, etc)
4. Forecasting berdasarkan historical data
5. Multi-store support
6. Advanced filtering & search

---

**Document Version**: 1.0  
**Last Updated**: 2025-01-20  
**Created for**: Café Berco Admin Dashboard
