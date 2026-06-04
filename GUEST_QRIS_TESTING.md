# 🧪 Guest QRIS Payment Testing Guide

## Ringkasan Test yang Sudah Berhasil

✅ **Order #24** berhasil dibuat dengan:
- Customer: Main Admin
- Total: Rp 33,000
- Status Pembayaran: **PAID** ✅
- Payment Method: **QRIS** ✅
- Reconciliation: **MATCHED** ✅

---

## 🚀 Testing Guest Checkout Flow (Browser Testing)

Untuk test flow yang realistis sebagai guest customer:

### Step 1: Start Local Server

```bash
php artisan serve
```

Server akan jalan di: **http://localhost:8000**

### Step 2: Login sebagai Guest atau Customer Biasa

#### Option A: Buat Guest Account Baru
1. Buka: `http://localhost:8000/register`
2. Isi form registrasi (guest bisa skip bagian yang tidak wajib)
3. Click **Register**
4. Auto-login setelah registrasi

#### Option B: Login dengan Akun Existing
1. Buka: `http://localhost:8000/login`
2. Gunakan credentials:
   ```
   Email: customer@email.com
   Password: password
   ```
3. Click **Login**

### Step 3: Browse Menu & Order

1. Setelah login, Anda akan di home page
2. Lihat menu yang tersedia
3. Klik menu item untuk order, misal **Espresso** (Rp 15,000)
4. Adjust quantity (misal: 2 items)
5. Click **Add to Cart**

### Step 4: Checkout dan Pilih QRIS Payment

1. Click **Checkout** (biasanya di top right atau dalam sidebar)
2. Review order summary:
   ```
   Espresso x2        Rp 30,000
   Tax (10%)          Rp  3,000
   ─────────────────────────────
   Total              Rp 33,000
   ```

3. **PILIH PAYMENT METHOD**: Scroll down ke bagian "Metode Pembayaran"
4. Lihat opsi pembayaran:
   - ☐ Cash
   - ☐ Debit Card
   - ☐ Credit Card
   - ☐ E-Wallet
   - ☐ Bank Transfer
   - ☑ **QRIS** ← Click ini!

5. Lihat deskripsi QRIS:
   ```
   💚 QRIS - "Scan kode QRIS dengan aplikasi mobile banking"
   ```

6. Click **Confirm Order** atau **Bayar**

### Step 5: QRIS Payment Page

Setelah click bayar, Anda akan di-redirect ke halaman QRIS payment:

**URL**: `http://localhost:8000/xendit/qris/payment/{order_id}`

Halaman akan menampilkan:

```
╔═════════════════════════════════════════╗
║         ORDER SUMMARY                   ║
╠═════════════════════════════════════════╣
║ Order ID:        #24                    ║
║ Customer:        John Doe               ║
║ Total Amount:    Rp 33,000             ║
║ Status:          Pending Payment       ║
╚═════════════════════════════════════════╝

🔷 INSTRUKSI PEMBAYARAN QRIS:

1. Buka aplikasi mobile banking atau e-wallet Anda
   (BNI Mobile, BRI Mobile, Jenius, OVO, GCash, dll)

2. Pilih menu "Scan QRIS" atau "Bayar dengan QRIS"

3. Arahkan kamera ke kode QRIS di bawah

4. Masukkan jumlah: Rp 33,000

5. Konfirmasi pembayaran

6. Tunggu halaman ini auto-update (polling setiap 3 detik)

┌─────────────────────────────────────┐
│  [QR CODE akan ditampilkan di sini] │
│  (jika invoice sudah dibuat)         │
└─────────────────────────────────────┘

[🔄 Buat Kode QRIS Baru]  [🔄 Retry]

Status: ⏳ Menunggu Pembayaran...
```

### Step 6: Simulasi Pembayaran (untuk testing)

Di terminal lain (jangan close yang server):

```bash
# 1. Dapatkan order_id dari URL (misal: #25)
# 2. Jalankan webhook simulation
php artisan qris:test-webhook 25
```

Output:
```
✅ QRIS Transaction marked as PAID
✅ Reconciliation: MATCHED
```

### Step 7: Payment Success

Setelah webhook berhasil:

1. Browser akan **auto-refresh** (polling setiap 3 detik)
2. Status akan berubah menjadi: **✅ PEMBAYARAN BERHASIL**
3. Auto-redirect ke halaman receipt/order confirmation
4. Lihat order details:
   ```
   Receipt #25
   Customer: John Doe
   Total: Rp 33,000
   Status Pembayaran: PAID ✅
   Payment Method: QRIS
   Paid At: 2026-06-04 04:18:11
   ```

---

## 📋 Testing Checklist

### Checkout Flow
- [ ] Bisa login sebagai customer
- [ ] Bisa browse dan add menu to cart
- [ ] Bisa masuk ke checkout page
- [ ] Bisa lihat order summary dengan benar
- [ ] **QRIS option tersedia** di payment methods
- [ ] Bisa select QRIS sebagai payment method
- [ ] Tidak ada error saat click "Confirm Order"

### QRIS Payment Page
- [ ] URL format benar: `/xendit/qris/payment/{order_id}`
- [ ] Order summary ditampilkan dengan benar
- [ ] Total harga: Rp 33,000 (benar)
- [ ] Status awalnya: **Pending Payment**
- [ ] Tombol "Buat Kode QRIS Baru" tersedia

### Payment Simulation
- [ ] Jalankan `php artisan qris:test-webhook {order_id}` tanpa error
- [ ] Command output menampilkan: **✅ QRIS Transaction marked as PAID**
- [ ] Reconciliation: **MATCHED**

### Payment Success Page
- [ ] Browser auto-refresh dan detect payment
- [ ] Status berubah: **✅ PEMBAYARAN BERHASIL**
- [ ] Auto-redirect ke receipt page (atau success page)
- [ ] Order status berubah ke: **PAID**
- [ ] Lihat order details dengan benar

### Database Verification
- [ ] Order status_pembayaran = "paid"
- [ ] QrisTransaction status = "paid"
- [ ] QrisReconciliation status = "matched"
- [ ] Tidak ada error di logs

---

## 🔧 Testing Scenarios

### Scenario 1: Basic Guest QRIS Payment (RECOMMENDED)
```bash
# 1. Start server
php artisan serve

# 2. Buka browser: http://localhost:8000
# 3. Login atau register sebagai guest

# 4. Order menu dengan QRIS payment method

# 5. Di terminal lain, simulate payment:
php artisan qris:test-webhook {order_id}

# 6. Lihat payment success page di browser
```

### Scenario 2: Multiple Orders
```bash
# 1. Lakukan Step 1-7 untuk order pertama
# 2. Kembali ke menu (checkout selesai)
# 3. Order menu lagi dengan order berbeda
# 4. Gunakan QRIS lagi
# 5. Simulate pembayaran kedua
```

### Scenario 3: Payment Timeout/Expiration
```bash
# 1. Create order dengan QRIS
# 2. Jangan bayar selama 30 menit (timeout)
# 3. Di terminal: php artisan qris:test-webhook akan gagal
#    karena transaction sudah expired
# 4. Customer harus create ulang kode QRIS
```

### Scenario 4: Failed Payment (optional)
```bash
# Edit scripts/create_test_qris_transaction.php
# Ubah: 'status' => 'pending'
# Jadi: 'status' => 'failed'
# Maka payment akan langsung gagal
```

---

## 🐛 Troubleshooting

### Issue: QRIS option tidak muncul di checkout
**Solusi:**
- Check migration sudah run: `php artisan migrate`
- Check orders table: `payment_method` ENUM sudah include 'qris'
- Buka database: `SELECT * FROM information_schema.COLUMNS WHERE TABLE_NAME = 'orders' AND COLUMN_NAME = 'payment_method'`

### Issue: Halaman payment blank atau error
**Solusi:**
- Check console browser (F12 → Console tab)
- Check Laravel logs: `tail -f storage/logs/laravel.log`
- Make sure Order record exists di database

### Issue: Polling tidak update payment status
**Solusi:**
- Webhook simulation harus run SEBELUM polling habis (30 menit timeout)
- Jalankan: `php artisan qris:test-webhook {order_id}`
- Refresh browser setelah webhook berhasil

### Issue: "QRIS transaction not found"
**Solusi:**
- Pastikan sudah click "Buat Kode QRIS Baru" di payment page
- Atau jalankan manual creation script terlebih dahulu

---

## 📊 Expected Database State

Setelah guest berhasil order dengan QRIS:

### orders table
```sql
SELECT * FROM orders WHERE id_order = 25;

id_order | tanggal             | nama_pelanggan | total_harga | status_pembayaran | payment_method
---------|---------------------|----------------|-------------|------------------|---------------
   25    | 2026-06-04 04:20:00 | John Doe       |    33000    | paid             | qris
```

### qris_transactions table
```sql
SELECT * FROM qris_transactions WHERE id_order = 25;

id_qris_transaction | id_order | invoice_id      | status | amount | payment_channel | expires_at          | paid_at
--------------------|----------|-----------------|--------|--------|-----------------|---------------------|---------------------
        2           |    25    | inv_abc123      | paid   | 33000  | qris            | 2026-06-04 04:50:00 | 2026-06-04 04:20:00
```

### qris_reconciliations table
```sql
SELECT * FROM qris_reconciliations WHERE id_qris_transaction = 2;

id_reconciliation | id_qris_transaction | reconciliation_status | system_amount | bank_amount | amount_difference
------------------|---------------------|----------------------|---------------|-------------|------------------
        2         |         2           | matched               | 33000         | 33000       | 0
```

---

## 🎯 Summary

**Test Sebelumnya** (CLI/Script):
- ✅ Buat order dengan QRIS payment method
- ✅ Simulate QRIS payment webhook
- ✅ Verify reconciliation matched
- ✅ Check database state

**Test Sekarang** (Browser/Guest):
- 🔄 Test realistic guest checkout flow
- 🔄 Verify QRIS payment page UI
- 🔄 Verify real-time polling works
- 🔄 Verify payment success flow

---

## 🚀 Next Steps

1. ✅ Run local server: `php artisan serve`
2. ✅ Test guest checkout dengan QRIS
3. ✅ Simulate payment dengan CLI command
4. ✅ Verify payment success di browser
5. ✅ Check database dan logs
6. 📧 (Optional) Test email notification
7. 📱 (Optional) Test dengan real Xendit sandbox

---

*Last Updated: June 4, 2026*
*Test Status: ✅ READY FOR BROWSER TESTING*
