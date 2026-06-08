# 📊 Dynamic Tax & Discount Configuration & Product Sales Analytics

Dokumentasi lengkap fitur-fitur yang telah diimplementasikan untuk sistem cafe berco.

## ✨ Fitur-Fitur yang Telah Diimplementasikan

### 1. **Dynamic Tax & Discount Configuration** 💰

#### A. Konfigurasi Pajak (PB1)
Admin dapat mengkonfigurasi persentase pajak untuk semua transaksi penjualan.

**Fitur:**
- Buat, edit, dan hapus konfigurasi pajak
- Tentukan persentase pajak (misalnya 10%, 5%)
- Atur tanggal berlaku konfigurasi
- Aktifkan/nonaktifkan konfigurasi
- Hanya satu konfigurasi yang bisa aktif di waktu yang sama

**Routes:**
```
GET     /admin/tax                    - Lihat daftar konfigurasi pajak
GET     /admin/tax/create             - Form buat konfigurasi baru
POST    /admin/tax                    - Simpan konfigurasi baru
GET     /admin/tax/{tax}/edit         - Form edit konfigurasi
PUT     /admin/tax/{tax}              - Update konfigurasi
DELETE  /admin/tax/{tax}              - Hapus konfigurasi
POST    /admin/tax/{tax}/set-active   - Aktifkan konfigurasi
```

**Model: `TaxConfiguration`**
```php
- id_tax_config: int (PK)
- name: string
- tax_percentage: decimal(5,2)
- description: text
- is_active: boolean
- effective_from: datetime
- effective_until: datetime
- id_user: foreignId (Admin yang membuat)
- timestamps
```

---

#### B. Skema Diskon Promo
Admin dapat membuat berbagai skema diskon dengan aturan yang fleksibel.

**Fitur:**
- Buat diskon dengan tipe persentase atau nominal tetap
- Tentukan minimum pembelian
- Atur batas maksimal diskon (untuk persentase)
- Tentukan jumlah maksimal penggunaan
- Atur periode berlaku diskon
- Lacak penggunaan diskon

**Routes:**
```
GET     /admin/discount               - Lihat daftar skema diskon
GET     /admin/discount/create        - Form buat skema baru
POST    /admin/discount               - Simpan skema baru
GET     /admin/discount/{discount}/edit - Form edit skema
PUT     /admin/discount/{discount}    - Update skema
DELETE  /admin/discount/{discount}    - Hapus skema
GET     /api/discounts/available      - API untuk dapatkan diskon tersedia
```

**Model: `DiscountScheme`**
```php
- id_discount_scheme: int (PK)
- code: string (unique) - e.g., "PAGI_SPESIAL"
- name: string
- description: text
- discount_type: enum(percentage, fixed)
- discount_value: decimal(10,2)
- min_purchase: decimal(10,2) - Nullable
- max_discount: decimal(10,2) - Nullable (untuk tipe %)
- max_uses: integer - Nullable (unlimited jika null)
- times_used: integer (track penggunaan)
- is_active: boolean
- valid_from: datetime
- valid_until: datetime
- id_user: foreignId (Admin yang membuat)
- timestamps
```

**Contoh Skema Diskon yang Sudah Dibuat (Seeder):**
1. **PAGI_SPESIAL** - 15% diskon untuk pembelian minimal Rp 50.000 (maks Rp 30.000)
2. **SIANG_HEMAT** - Diskon tetap Rp 20.000 untuk pembelian minimal Rp 100.000
3. **WEEKEND_MERIAH** - 20% diskon untuk akhir pekan (min Rp 75.000)
4. **LOYALITAS10** - 10% untuk member setia (maks Rp 100.000)

---

### 2. **Integrasi Tax & Discount di Checkout** 🛒

#### Order Model Enhancement
Order model telah ditambahkan field untuk melacak tax dan discount:

**New Fields di Tabel `orders`:**
```php
- subtotal: decimal(12,2)          // Subtotal sebelum pajak & diskon
- tax_amount: decimal(10,2)        // Jumlah pajak
- discount_amount: decimal(10,2)   // Jumlah diskon
- final_total: decimal(12,2)       // Total akhir (subtotal + tax - discount)
- id_tax_config: foreignId
- id_discount_scheme: foreignId  
- cost_of_goods: decimal(10,2)     // COGS untuk kalkulasi margin
- profit_margin: decimal(10,2)     // Keuntungan = final_total - cost_of_goods
```

#### Service: OrderCalculationService
Service untuk menghitung total order dengan pajak dan diskon:

```php
// Menghitung total order
$calculation = app(OrderCalculationService::class)
    ->calculateOrderTotal($subtotal, $taxConfigId, $discountSchemeId, $costOfGoods);

// Hasil: [
//     'subtotal' => ...,
//     'tax_amount' => ...,
//     'discount_amount' => ...,
//     'final_total' => ...,
//     'profit_margin' => ...
// ]

// Menerapkan tax & discount ke order
app(OrderCalculationService::class)
    ->applyTaxAndDiscount($order, $taxConfigId, $discountSchemeId, $costOfGoods);
```

---

### 3. **Product Sales Analytics & Insights** 📈

#### A. Dashboard Analytics Lengkap
Admin/Owner dapat melihat analisis penjualan komprehensif dengan visualisasi data.

**Fitur Dashboard:**
- **Today's Sales Summary:**
  - Jumlah transaksi hari ini
  - Total pendapatan
  - Total keuntungan
  - Total pajak yang terkumpul
  - Rata-rata nilai transaksi

- **Charts & Graphs:**
  - Grafik tren pendapatan vs keuntungan
  - Grafik jumlah transaksi
  - Data dapat difilter per jam (harian), hari (bulanan), atau bulan (tahunan)

- **Top Products Ranking:**
  - Produk terlaris dengan ranking (🥇 🥈 🥉)
  - Jumlah terjual per produk
  - Jumlah pesanan
  - Total revenue per produk
  - Margin keuntungan per produk

- **Purchase History:**
  - Daftar semua transaksi hari ini
  - Detail produk yang dibeli
  - Subtotal, pajak, diskon, dan total
  - Keuntungan per transaksi

**Routes:**
```
GET /admin/analytics/                  - Dashboard analytics utama
GET /admin/analytics/products          - Detail laporan produk
GET /admin/analytics/export            - Export data ke CSV
```

#### B. Analisis Performa Produk (Periode)
Data tersegmentasi berdasarkan periode:

**Harian (Daily):**
- Data per jam (00:00 - 23:00)
- Melihat waktu puncak penjualan
- Produk terlaris per jam

**Bulanan (Monthly):**
- Data per hari dalam bulan
- Tren penjualan sepanjang bulan
- Hari-hari dengan penjualan tertinggi

**Tahunan (Yearly):**
- Data per bulan dalam tahun
- Tren penjualan tahunan
- Bulan dengan performa terbaik

#### C. Metrics & KPI
Setiap produk menampilkan:
- **Total Sold**: Jumlah unit terjual
- **Times Ordered**: Berapa kali produk dipesan
- **Total Revenue**: Total pendapatan dari produk
- **Avg Price**: Harga rata-rata
- **Number of Orders**: Jumlah transaksi yang mencakup produk ini
- **Total Margin**: Margin keuntungan keseluruhan

#### D. Export Data
Admin dapat mengexport data analitik ke CSV untuk analisis lebih lanjut di Excel/Spreadsheet.

---

## 🛠️ Teknologi & Tools

### Backend
- **Framework**: Laravel 11
- **Database**: SQLite (compatibility), MySQL (production)
- **Models**: TaxConfiguration, DiscountScheme, Order (enhanced)
- **Controllers**: 
  - `TaxConfigurationController` - Kelola pajak
  - `DiscountSchemeController` - Kelola diskon
  - `AnalyticsController` - Analytics & Reports
- **Services**: `OrderCalculationService` - Kalkulasi order

### Frontend
- **Template Engine**: Blade
- **Charts**: Chart.js (untuk visualisasi)
- **Styling**: Tailwind CSS + Dark Mode
- **Components**: Layout components yang reusable

---

## 📝 Cara Penggunaan

### 1. **Admin - Konfigurasi Pajak**

**Langkah:**
1. Login sebagai Admin
2. Navigasi ke `/admin/tax`
3. Klik "Tambah Konfigurasi"
4. Isi form:
   - Nama Konfigurasi (e.g., "PB1 - Pajak 10%")
   - Persentase Pajak (e.g., 10)
   - Deskripsi (optional)
   - Tanggal berlaku (optional)
5. Centang "Aktifkan sebagai konfigurasi default" jika ingin langsung aktif
6. Klik "Simpan Konfigurasi"

**Tips:** Hanya satu konfigurasi yang bisa aktif. Mengaktifkan konfigurasi baru akan otomatis menonaktifkan yang lama.

---

### 2. **Admin - Buat Skema Diskon**

**Langkah:**
1. Login sebagai Admin
2. Navigasi ke `/admin/discount`
3. Klik "Tambah Skema Diskon"
4. Isi form:
   - **Kode**: PAGI_SPESIAL (unik)
   - **Nama**: Promo Pagi Spesial
   - **Deskripsi**: Diskon 15% untuk pembelian pagi
   - **Tipe Diskon**: Pilih Persentase atau Nominal
   - **Nilai Diskon**: 15 (jika %) atau 20000 (jika Rp)
   - **Min. Pembelian**: 50000 (opsional)
   - **Max Diskon**: 30000 (untuk tipe %, opsional)
   - **Max Uses**: 100 (opsional)
   - **Berlaku Dari/Hingga**: Tentukan rentang periode
5. Centang "Aktifkan" untuk langsung aktif
6. Klik "Simpan Skema"

---

### 3. **Owner - Lihat Analytics Dashboard**

**Langkah:**
1. Login sebagai Admin/Owner
2. Navigasi ke `/admin/analytics/`
3. Lihat summary hari ini:
   - Jumlah transaksi
   - Pendapatan total
   - Keuntungan
   - Pajak terkumpul
4. Filter berdasarkan periode:
   - **Harian**: Lihat per jam, input tanggal spesifik
   - **Bulanan**: Lihat per hari dalam bulan
   - **Tahunan**: Lihat per bulan dalam tahun
5. Lihat grafik tren dan top products
6. Klik "Export CSV" untuk download data

**Insights yang Bisa Didapat:**
- Waktu penjualan puncak
- Produk paling laris
- Margin keuntungan per produk
- Trend penjualan
- Average transaction value
- Customer purchase patterns

---

### 4. **Kasir - Pengalaman Checkout** (Future Enhancement)

Kasir akan melihat:
- Subtotal otomatis
- Tax diterapkan otomatis dari konfigurasi aktif
- Opsi untuk memilih diskon yang tersedia
- Preview total akhir dan keuntungan
- Detail pajak & diskon breakdown

---

## 🔐 Access Control

| Fitur | Admin | Staff | Guest | Customer |
|-------|-------|-------|-------|----------|
| Konfigurasi Pajak | ✅ | ❌ | ❌ | ❌ |
| Kelola Diskon | ✅ | ❌ | ❌ | ❌ |
| Dashboard Analytics | ✅ (Owner) | ❌ | ❌ | ❌ |
| Lihat Diskon Tersedia | ✅ | ✅ | ✅ | ✅ |
| Apply Tax & Discount | ✅ | ✅ | ❌ | ❌ |

---

## 📊 Database Schema

### tax_configurations table
```
id_tax_config (PK)
name
tax_percentage
description
is_active
effective_from
effective_until
id_user (FK)
created_at
updated_at
```

### discount_schemes table
```
id_discount_scheme (PK)
code (UNIQUE)
name
description
discount_type (ENUM)
discount_value
min_purchase
max_discount
max_uses
times_used
is_active
valid_from
valid_until
id_user (FK)
created_at
updated_at
```

### orders table (modified)
```
[Previous fields...]
subtotal
tax_amount
discount_amount
final_total
id_tax_config (FK) nullable
id_discount_scheme (FK) nullable
cost_of_goods
profit_margin
```

---

## 🚀 Testing Features

### Test Konfigurasi Pajak
```php
// Dapatkan pajak aktif
$activeTax = TaxConfiguration::getActiveConfiguration();

// Hitung pajak
$tax = $activeTax->calculateTax(100000); // Rp 10.000 jika 10%
```

### Test Diskon
```php
// Dapatkan diskon tersedia
$discounts = DiscountScheme::getValidSchemes(100000);

// Validasi diskon
$scheme = DiscountScheme::find(1);
$isValid = $scheme->isValid(100000);

// Hitung diskon
$discountAmount = $scheme->calculateDiscount(100000);
```

### Test Order Calculation
```php
// Calculate with tax & discount
$calculation = app(OrderCalculationService::class)
    ->calculateOrderTotal(
        subtotal: 100000,
        taxConfigId: 1,
        discountSchemeId: 1,
        costOfGoods: 60000
    );

// Result: [
//     'subtotal' => 100000,
//     'tax_amount' => 10000,
//     'discount_amount' => 16500, // 15% dari 110000 max 30000
//     'final_total' => 93500,
//     'profit_margin' => 33500
// ]
```

---

## 📚 API Endpoints

### Get Available Discounts
```
GET /api/discounts/available?subtotal=100000
```

Response:
```json
{
  "data": [
    {
      "id": 1,
      "code": "PAGI_SPESIAL",
      "name": "Promo Pagi Spesial",
      "discount_value": 15,
      "discount_type": "percentage",
      "discount_amount": 15000,
      "description": "..."
    }
  ]
}
```

---

## ✅ Checklist - Fitur Sudah Diimplementasikan

- ✅ Migrations untuk tax_configurations, discount_schemes
- ✅ Models: TaxConfiguration, DiscountScheme, Order (enhanced)
- ✅ Controllers: TaxConfigurationController, DiscountSchemeController, AnalyticsController
- ✅ Service: OrderCalculationService
- ✅ Views: Tax config (CRUD), Discount schemes (CRUD), Analytics dashboard
- ✅ Routes: Admin tax, discount, analytics endpoints
- ✅ Seeders: TaxDiscountSeeder dengan data test
- ✅ API endpoint untuk dapatkan diskon tersedia
- ✅ Charts & visualisasi dengan Chart.js
- ✅ Export to CSV functionality

---

## 🔄 Next Steps (Future Enhancements)

1. **Checkout UI Integration**: Update checkout page untuk show tax & discount
2. **Kasir Dashboard**: Dashboard khusus untuk kasir mengelola transaksi
3. **Real-time Notifications**: Notifikasi saat diskon kadaluarsa atau mencapai limit
4. **Bulk Operations**: Import/export skema diskon via Excel
5. **Advanced Analytics**: Predictive analytics, forecasting
6. **Discount Codes**: Customers bisa input kode diskon di checkout
7. **Tax Reporting**: Generate tax reports untuk department keuangan
8. **Audit Trail**: Log semua perubahan konfigurasi pajak & diskon

---

## 📞 Support

Untuk pertanyaan atau issues, silahkan hubungi team development.

---

**Last Updated**: 2026-06-07
**Version**: 1.0
