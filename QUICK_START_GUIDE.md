# 🚀 Quick Start Guide - Sales Dashboard

Panduan cepat untuk mulai menggunakan fitur-fitur dashboard penjualan baru.

---

## 📌 3 Fitur Utama

### 1️⃣ Penjualan Hari Ini (Sales Today)
**Tujuan**: Lihat ringkasan penjualan hari ini dengan detail transaksi  
**Akses**: `/admin/sales-today` atau Sidebar > Penjualan Hari Ini  
**Tampilan**: 4 kartu metrik + tabel history  

**Fitur Utama**:
- 📊 4 Kartu Summary: Transaksi, Pendapatan, Profit, Charge
- 🛒 Tabel History: Detail setiap transaksi hari ini
- 📅 Date Picker: Lihat penjualan hari lain
- 🔄 Refresh: Update data terbaru

---

### 2️⃣ Penjualan Produk (Product Analytics)
**Tujuan**: Analisis performa produk dengan filter periode  
**Akses**: `/admin/product-analytics` atau Sidebar > Penjualan Produk  
**Tampilan**: Period selector + 4 stat cards + top products + all products  

**Fitur Utama**:
- 📅 Filter: Harian / Bulanan / Tahunan
- 📈 4 Statistik: Total terjual, Revenue, Orders, Rata-rata
- 🏆 Top 10: Produk terlaris dengan medal 🥇🥈🥉
- 📊 All Products: Lengkap semua produk penjualan

---

### 3️⃣ Konfigurasi Pajak & Diskon
**Tujuan**: Atur tax config dan discount schemes untuk order  
**Akses**: `/admin/config/tax-discount` atau Sidebar > Pajak & Diskon  
**Tampilan**: Tax list + Discount list dengan form add/edit  

**Fitur Utama**:
- 💰 Manage Pajak: Add/Edit/Delete/Activate tax config
- 🏷️ Manage Diskon: Add/Edit/Delete/Activate discount schemes
- 📋 Active Status: Visual indicator pajak/diskon mana yang aktif
- ✏️ Form Validation: Real-time validation input

---

## ⚡ Quick Actions

### Lihat Penjualan Hari Ini
```
1. Login → Admin Dashboard
2. Sidebar → 📅 Penjualan Hari Ini
3. Lihat summary kartu di atas
4. Scroll ke bawah lihat history
```

### Analisis Produk Terlaris Bulan Ini
```
1. Sidebar → 📊 Penjualan Produk
2. Click "📆 Bulanan"
3. Pilih bulan di date picker
4. Scroll → 🏆 Produk Terlaris bagian
```

### Aktifkan Tax Baru
```
1. Sidebar → ⚙️ Pajak & Diskon
2. Scroll → 💰 Konfigurasi Pajak section
3. Click "✨ Tambah Pajak"
4. Isi: Nama pajak, %, Deskripsi
5. Click "💾 Simpan"
6. Click "Aktifkan" untuk gunakan
```

### Buat Diskon Promo
```
1. Sidebar → ⚙️ Pajak & Diskon
2. Scroll → 🏷️ Skema Diskon section
3. Click "✨ Tambah Diskon"
4. Isi: Code (PROMO20), Nama, Tipe (%), Value
5. Isi: Min purchase, Max diskon (optional)
6. Click "💾 Simpan"
7. Click "Aktifkan"
```

---

## 🎯 Typical Workflows

### Workflow 1: Monitor Penjualan Harian
**Waktu**: 5 menit setiap pagi  

```
Pagi:
├─ Buka Sales Today dashboard
├─ Cek: Berapa transaksi semalam?
├─ Cek: Total revenue kemarin?
├─ Cek: History untuk problematic orders?
└─ Decision: Apa perlu action?

Petang/Malam:
├─ Update date ke hari ini
├─ Monitor real-time penjualan
├─ Cek performa setiap jam
└─ Sesuaikan strategi jika perlu
```

### Workflow 2: Analisis Produk Terjual
**Waktu**: 1x seminggu/bulan  

```
Mingguan:
├─ Buka Product Analytics
├─ Filter: Harian → lihat top sales
├─ Filter: Minggu ini → trend
├─ Lihat: Produk mana yang trending?
├─ Lihat: Produk mana yang slow?
└─ Action: Adjust promotion/menu

Bulanan:
├─ Filter: Bulanan → lihat bulan lalu
├─ Bandingkan dengan bulan sebelumnya
├─ Identifikasi seasonal trends
└─ Planning: Apa menu untuk bulan depan?
```

### Workflow 3: Setup Pajak & Diskon
**Waktu**: 1x setup awal, 2-3x perubahan per bulan  

```
Setup Awal:
├─ Admin > Pajak & Diskon
├─ Setup Pajak PB1 15%
├─ Setup Pajak PB1 10% (jika ada)
├─ Aktifkan pajak yang sesuai
└─ Setup standard discounts

Saat Promo:
├─ Tambah diskon baru: "HARVEST2025"
├─ Set value, min purchase, periode
├─ Aktifkan saat promo dimulai
├─ Monitor usage di order history
└─ Nonaktifkan saat promo selesai
```

---

## 🎨 UI/UX Tips

### Warna Indicators
- 🟢 **Green**: Revenue, Profit, Success
- 🔵 **Blue**: Info, Actions, Metrik
- 🟠 **Orange**: Tax, Warning
- 🟣 **Purple**: Discount
- 🔴 **Red**: Negative, Delete

### Icons Meaning
- 📅 Harian (Daily)
- 📆 Bulanan (Monthly)
- 📊 Tahunan (Yearly)
- 🏆 Terlaris (Top/Ranked)
- 🛒 History (Shopping)
- 💰 Pajak (Tax/Money)
- 🏷️ Diskon (Discount/Tag)
- ⚙️ Konfigurasi (Settings/Config)
- ✨ Tambah (Add/New)
- 💾 Simpan (Save)

### Navigation Patterns

**Dari Sales Today ke Detail**:
- Sales Today → lihat total → click detail row → lihat detail order

**Dari Product Analytics ke Promo Decision**:
- Product Analytics → lihat top products → buat discount → aktifkan → check usage di history

---

## 🔌 Integration Points

### POS/Checkout System
1. **Tax Applied**: Auto-apply active tax saat checkout
2. **Discount Code**: Input code di POS, validate otomatis
3. **Final Calculation**: System auto-hitung: subtotal + tax - discount

### Order Management
1. **Order Creation**: System auto-assign id_tax_config dan id_discount_scheme
2. **Profit Calculation**: Auto-hitung profit_margin dari order

### Analytics
1. **Real-time Update**: Dashboard update otomatis saat ada new order
2. **Historical Data**: Semua data tersimpan untuk analysis

---

## ❓ FAQ

**Q: Mengapa pajak saya tidak muncul di checkout?**  
A: Kemungkinan: (1) Pajak belum diaktifkan, (2) POS belum diintegrasikan, (3) Cache belum clear.

**Q: Bisa punya banyak pajak aktif?**  
A: Tidak - hanya 1 pajak bisa aktif sekaligus. Activate pajak baru otomatis non-aktifkan yang lama.

**Q: Berapa lama data history disimpan?**  
A: Selamanya (permanent database record). Bisa analyze history kapan saja.

**Q: Diskon bisa unlimited usage?**  
A: Ya - set "Max Penggunaan" ke 0 = unlimited.

**Q: Export data ke Excel?**  
A: Feature soon. Saat ini: screenshot atau manual copy dari table.

---

## 📊 Data Insights

### Dari Sales Today, lihat:
- ✅ Berapa banyak transaksi? (customer volume)
- ✅ Total revenue? (daily target achievement)
- ✅ Profit margin? (business health)
- ✅ Problem orders? (lihat di history)

### Dari Product Analytics, lihat:
- ✅ Top 3 best sellers? (focus products)
- ✅ Slow-moving products? (needs promotion)
- ✅ Revenue vs quantity? (price vs volume)
- ✅ Seasonal trends? (compare periods)

### Dari Tax/Discount Config, lihat:
- ✅ Current active tax? (what's applied now)
- ✅ Active promos? (what's running)
- ✅ Expired schemes? (cleanup old ones)

---

## 🛠️ Common Setup Scenarios

### Scenario 1: Coffee Shop (Standard Setup)
```
Tax:
- PB1 Standard: 10%

Discounts:
- Member Discount: 10% (min 50k)
- Off-Peak Promo: Rp 5,000 (3-5pm)
- Bundle Discount: Rp 10,000 (min 100k)
```

### Scenario 2: Fast Food (High Volume)
```
Tax:
- PB1 Regular: 15%

Discounts:
- Lunch Combo: 15% (11am-2pm)
- Bulk Order: 10% (min 200k)
- Happy Hour: Rp 3,000 (per item)
```

### Scenario 3: Fine Dining (Premium)
```
Tax:
- Service Charge: 15%
- Luxury Tax: 20% (untuk menu premium)

Discounts:
- VIP Member: 20%
- Anniversary Special: 25% (specific dates)
- Volume Discount: 10% per 100k spent
```

---

## 🚀 Next Steps

1. **Setup Initial Config**:
   - Set active tax to current rate
   - Setup monthly promotion discount

2. **Monitor Daily**:
   - Check Sales Today pagi & malam
   - Monitor product performance

3. **Monthly Review**:
   - Analyze Product Analytics
   - Decide promo untuk bulan depan
   - Adjust menu strategy

4. **Optimization**:
   - Use insights untuk improve profitability
   - Adjust discount strategy based on data
   - Plan seasonal promotions

---

## 📞 Need Help?

- **Documentation**: SALES_DASHBOARD_DOCUMENTATION.md
- **Implementation**: IMPLEMENTATION_GUIDE.md  
- **Support Team**: [contact info]
- **Issue Report**: [issue tracker]

---

**Happy Analyzing! 🎉**  
*Gunakan data untuk keputusan bisnis yang lebih baik.*

---

**Quick Start Version**: 1.0  
**Last Updated**: 2025-01-20
