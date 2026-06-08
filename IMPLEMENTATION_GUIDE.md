# Implementation Guide - Sales Dashboard & Analytics

Panduan lengkap untuk implementasi dan testing fitur-fitur Sales Dashboard terbaru.

---

## ✅ Pre-Implementation Checklist

### Database Migrations
Pastikan semua migrations sudah dijalankan:

```bash
php artisan migrate
```

Verify tables exist:
- ✅ `orders` - dengan fields: `tanggal`, `status_pembayaran`, `profit_margin`, `cost_of_goods`, `tax_amount`, `discount_amount`, `final_total`, `subtotal`, `id_tax_config`, `id_discount_scheme`
- ✅ `order_items` - dengan fields: `id_order`, `id_menu`, `quantity`, `subtotal`
- ✅ `menus` - dengan fields: `id_menu`, `nama_menu`, `harga`, `status_tersedia`
- ✅ `tax_configurations` - dengan fields: `id_tax_config`, `name`, `tax_percentage`, `is_active`, `id_user`
- ✅ `discount_schemes` - dengan fields: `id_discount_scheme`, `code`, `name`, `discount_type`, `discount_value`, `is_active`, `id_user`
- ✅ `users` - untuk authentication

---

## 🚀 Implementation Steps

### Step 1: Copy Files

Ensure all files sudah ter-copy di direktori correct:

```
✅ app/Livewire/SalesToday.php
✅ app/Livewire/ProductSalesAnalytics.php
✅ app/Livewire/TaxDiscountConfiguration.php
✅ resources/views/livewire/sales-today.blade.php
✅ resources/views/livewire/product-sales-analytics.blade.php
✅ resources/views/livewire/tax-discount-configuration.blade.php
✅ resources/views/admin/sales-today.blade.php
✅ resources/views/admin/product-sales-analytics.blade.php
✅ resources/views/admin/tax-discount-config.blade.php
```

### Step 2: Routes Configuration

Routes sudah ditambahkan di `routes/web.php`:

```php
// Penjualan Hari Ini
Route::get('/sales-today', fn() => view('admin.sales-today'))
    ->name('admin.sales.today');

// Penjualan Produk & Analytics
Route::get('/product-analytics', fn() => view('admin.product-sales-analytics'))
    ->name('admin.product.analytics');

// Konfigurasi Pajak & Diskon
Route::get('/config/tax-discount', fn() => view('admin.tax-discount-config'))
    ->name('admin.config.tax-discount');
```

**Routes Protection**: Semua routes protected dengan middleware `admin` (sudah ada di group)

### Step 3: Sidebar Update

Sidebar di `resources/views/dashboard.blade.php` sudah di-update dengan menu links:
- 📅 Penjualan Hari Ini
- 📊 Penjualan Produk
- 📈 Analytics Lengkap
- ⚙️ Pajak & Diskon

### Step 4: Compile Assets

```bash
npm run build
# atau untuk development
npm run dev
```

### Step 5: Clear Cache

```bash
php artisan config:cache
php artisan view:cache
php artisan cache:clear
```

---

## 🧪 Testing Guide

### Test Environment Setup

```bash
# Ensure you have test database
cp .env .env.testing
# Update .env.testing dengan test database credentials

# Run migrations on test database
php artisan migrate --env=testing

# Seed test data
php artisan db:seed
```

### Test Scenario 1: Penjualan Hari Ini

#### Prerequisites
- Login sebagai admin user
- Ada minimal 1 order paid hari ini

#### Test Steps
1. Navigate to `/admin/sales-today`
2. Verify page loads without error
3. Check kartu metrik:
   - Jumlah Transaksi > 0
   - Total Pendapatan > 0
   - Keuntungan visible
   - Charge visible
4. Check Purchase History table populated dengan transactions
5. Test date picker: ubah ke tanggal kemarin, verify data changes
6. Test refresh button: ensure data reloads

#### Expected Outputs
- Summary cards show correct calculation
- History table shows all paid orders for selected date
- All calculations match database values
- No 500 errors or exceptions

### Test Scenario 2: Penjualan Produk & Analytics

#### Prerequisites
- Login sebagai admin user
- Ada minimal 1 order dengan items hari ini

#### Test Steps
1. Navigate to `/admin/product-analytics`
2. Verify page loads
3. Test Period Buttons:
   - Click "Harian" - should show date picker
   - Click "Bulanan" - should show month picker
   - Click "Tahunan" - date picker should hide
4. Verify Summary Stats show:
   - Total Produk Terjual > 0
   - Total Pendapatan > 0
   - Total Pesanan > 0
   - Rata-rata per Pesanan calculated correctly
5. Check Produk Terlaris table:
   - Top 10 products listed
   - Sorted by quantity descending
   - Emoji rankings for #1, #2, #3
6. Check Semua Produk section:
   - All products shown
   - All columns populated
   - Numbers match database

#### Test Edge Cases
- No sales for selected period -> should show empty state
- Switch between periods -> data should update correctly

### Test Scenario 3: Konfigurasi Pajak & Diskon

#### Test 3A: Tax Configuration

##### Add New Tax
1. Navigate to `/admin/config/tax-discount`
2. Click "Tambah Pajak"
3. Fill form:
   - Nama Pajak: "Test Tax 15%"
   - Persentase: 15
   - Deskripsi: "Test tax for Q1"
4. Click Simpan
5. Verify tax appears in list
6. Verify form resets

##### Activate Tax
1. Click "Aktifkan" button on a tax config
2. Verify:
   - "Pajak Aktif Saat Ini" section updates
   - Previously active tax shows "Tidak Aktif"
   - Only 1 tax marked as active

##### Edit Tax
1. Click "Edit" button
2. Modify values
3. Click Simpan
4. Verify changes applied

##### Delete Tax
1. Click "Hapus" button
2. Confirm deletion in modal
3. Verify tax removed from list

#### Test 3B: Discount Configuration

##### Add New Discount
1. Click "Tambah Diskon"
2. Fill form:
   - Kode Diskon: "SUMMER20"
   - Nama Diskon: "Summer Sale 20%"
   - Tipe: "Persentase (%)"
   - Nilai: 20
   - Minimum Pembelian: 50000
3. Click Simpan
4. Verify discount appears in list

##### Activate/Deactivate Discount
1. Click "Aktifkan" on inactive discount
2. Verify status changes to active
3. Click "Nonaktifkan" on active discount
4. Verify status changes to inactive

##### Validate Discount Type Change
1. Create discount with percentage
2. Edit and change to fixed amount
3. Verify form updates correctly

### Test Scenario 4: Livewire Reactivity

#### Date Change Reactivity
1. Open Sales Today page
2. Change date in date picker
3. Verify data updates in real-time (no page refresh)

#### Period Change Reactivity
1. Open Product Analytics
2. Click different period buttons
3. Verify data updates smoothly without reload

#### Form Reactivity
1. In Tax/Discount config, change discount type
2. Verify label updates (% vs Rp)
3. Verify calculations work correctly

### Test Scenario 5: Error Handling

#### Test Unauthenticated Access
1. Logout
2. Try accessing `/admin/sales-today`
3. Should redirect to login

#### Test Unauthorized Access
1. Login sebagai customer
2. Try accessing `/admin/sales-today`
3. Should redirect (403 or dashboard)

#### Test Invalid Data
1. Try to add tax dengan % > 100
2. Should show validation error

#### Test Empty States
1. Clear all orders from today
2. Navigate to sales today
3. Should show 0 transactions
4. History table should show empty state message

---

## 🔍 Integration Testing

### Integration Test 1: Pajak Applied ke Order
1. Create order dengan active tax
2. Verify `tax_amount` calculated correctly
3. Verify `final_total` = `subtotal` + `tax_amount` - `discount_amount`

### Integration Test 2: Diskon Applied ke Order
1. Create order dengan discount code
2. Verify discount applied on checkout
3. Verify `discount_amount` calculated correctly

### Integration Test 3: Analytics Reflect Orders
1. Create new order
2. Verify immediately appears in Sales Today dashboard
3. Verify included in Product Analytics

---

## 🐛 Debugging Tips

### Issue: Data tidak muncul di dashboard

**Checklist:**
```
1. Verify order status_pembayaran = 'paid'
2. Verify tanggal in correct format
3. Check database has data: 
   SELECT COUNT(*) FROM orders WHERE DATE(tanggal) = CURDATE() AND status_pembayaran = 'paid'
4. Clear browser cache
5. Check Livewire component loaded: browser console no errors
```

### Issue: Livewire tidak responsive

**Solution:**
```bash
# Rebuild assets
npm run build

# Clear cache
php artisan cache:clear
php artisan view:cache

# Check Livewire version di composer.json
composer show livewire/livewire
```

### Issue: Route tidak ditemukan

**Checklist:**
```bash
# List all routes
php artisan route:list | grep admin.sales
php artisan route:list | grep admin.product
php artisan route:list | grep admin.config
```

### Issue: 500 error on page load

**Debug:**
```php
// Check logs
tail -f storage/logs/laravel.log

// Check Models exist
php artisan tinker
> Order::count()
> OrderItem::count()
> Menu::count()
```

---

## 📊 Performance Optimization

### Database Query Optimization

#### Sales Today Component
Current queries:
```php
Order::whereDate('tanggal', $today)
    ->where('status_pembayaran', 'paid')
    ->get()
```

**Optimization**: Add indexes
```sql
ALTER TABLE orders ADD INDEX idx_tanggal_status (tanggal, status_pembayaran);
```

#### Product Analytics
Current queries use groupBy with subqueries

**Optimization**: 
```sql
CREATE INDEX idx_order_items_created_menu 
  ON order_items(created_at, id_menu);

CREATE INDEX idx_order_items_order_id 
  ON order_items(id_order);
```

### Livewire Optimization

1. **Lazy Load Components**:
```blade
@livewire('product-sales-analytics', lazy)
```

2. **Debounce Date Changes**:
```php
// In component
#[Livewire\Attributes\Computed]
public function todaysSales()
{
    return $this->loadTodaysSales();
}
```

---

## 🚢 Deployment Checklist

Before deploying to production:

```
✅ All files copied to correct locations
✅ Routes registered correctly
✅ Database migrations run
✅ Views compiled
✅ Assets built (npm run build)
✅ Cache cleared
✅ env variables configured
✅ File permissions correct (750 for directories, 640 for files)
✅ Tested locally with sample data
✅ Tested with real data in staging
✅ Backup database before production
✅ Monitor logs after deployment
```

---

## 📱 Browser Compatibility

Tested & supported:
- ✅ Chrome 120+
- ✅ Firefox 121+
- ✅ Safari 17+
- ✅ Edge 120+
- ✅ Mobile browsers (iOS Safari, Chrome Mobile)

---

## 🎓 Usage Documentation

### For Admin/Owner Users
See [SALES_DASHBOARD_DOCUMENTATION.md](SALES_DASHBOARD_DOCUMENTATION.md) for detailed usage guides.

### For Developers
- Components: `app/Livewire/`
- Views: `resources/views/livewire/` dan `resources/views/admin/`
- Routes: `routes/web.php` (search for `sales-today`, `product-analytics`, `tax-discount`)

---

## 📞 Support & FAQ

### Q: Mengapa data tidak real-time?
**A**: Livewire real-time hanya untuk user interactions (button clicks, date changes). Data database di-query saat event trigger. Untuk true real-time, gunakan WebSocket (future enhancement).

### Q: Bisa custom tax/discount per customer?
**A**: Current system: 1 active tax global. Future: dapat customize per order/customer di POS.

### Q: Export analytics ke Excel?
**A**: Coming soon. Saat ini bisa screenshot atau manual copy.

### Q: Apa beda dengan existing Analytics dashboard?
**A**: 
- **Sales Today**: Focus on today only, simpler UI
- **Product Analytics**: Product-centric with multiple periods
- **Existing Analytics**: Comprehensive overview dengan charts

---

## 📝 Version History

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 2025-01-20 | Initial release - 3 components |

---

**Document Version**: 1.0  
**For**: Café Berco Team  
**Support**: contact@cafeberca.local
