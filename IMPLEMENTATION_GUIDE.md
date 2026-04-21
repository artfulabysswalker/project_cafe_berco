# 📋 Cafe Berco - Panduan Implementasi Lengkap

## 🎯 Ringkasan Fitur yang Telah Diimplementasikan

Sistem ini mengintegrasikan tiga fitur utama:
1. **Browse Menu** - Menjelajahi menu produk dengan filter dan pencarian
2. **Cart Management** - Mengelola keranjang belanja dengan tambah/kurang/hapus item
3. **Payment & Receipt** - Proses pembayaran dan riwayat pesanan

Semua fitur **terhubung langsung ke database `cafe_berco`** tanpa hardcoding.

---

## 🗄️ Konfigurasi Database

### File: `.env`
Database sudah dikonfigurasi untuk MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cafe_berco
DB_USERNAME=root
DB_PASSWORD=
```

**Catatan**: Ganti `DB_USERNAME` dan `DB_PASSWORD` sesuai konfigurasi MySQL Anda.

### Database yang Dibutuhkan
Pastikan database `cafe_berco` sudah dibuat:
```sql
CREATE DATABASE cafe_berco CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

## 📦 File yang Dibuat/Dimodifikasi

### 1. **Models** (`app/Models/`)
| File | Fungsi |
|------|--------|
| `Product.php` | Representasi menu produk |
| `Order.php` | Representasi pesanan dengan methods helper |
| `OrderItem.php` | Item dalam pesanan |
| `CartItem.php` | Item dalam keranjang |
| `User.php` | (diupdate) Tambah relationships |

### 2. **Controllers** (`app/Http/Controllers/`)
| File | Method | Fungsi |
|------|--------|--------|
| `MenuController.php` | `index()` | Tampilkan menu dengan filter & search |
| | `show()` | Detail produk (API) |
| `CartController.php` | `index()` | Tampilkan keranjang |
| | `add()` | Tambah item ke keranjang |
| | `update()` | Update kuantitas |
| | `remove()` | Hapus item |
| | `clear()` | Kosongkan keranjang |
| | `count()` | Hitung item di keranjang |
| `OrderController.php` | `checkout()` | Tampilkan halaman checkout |
| | `store()` | Proses pembayaran & buat order |
| | `receipt()` | Tampilkan struk pesanan |
| | `history()` | Riwayat pesanan |
| | `show()` | Detail pesanan (API) |

### 3. **Blade Views** (`resources/views/`)
| File | Deskripsi |
|------|-----------|
| `menu.blade.php` | Halaman browsing menu dengan grid produk |
| `cart.blade.php` | Halaman keranjang belanja |
| `checkout.blade.php` | Halaman pembayaran (metode, layanan, catatan) |
| `receipt.blade.php` | Struk pesanan (bisa dicetak) |
| `order-history.blade.php` | Riwayat semua pesanan |

### 4. **Migrations** (`database/migrations/`)
```
2025_01_15_100000_create_products_table.php
2025_01_15_100001_create_orders_table.php
2025_01_15_100002_create_order_items_table.php
2025_01_15_100003_create_cart_items_table.php
```

### 5. **Seeders** (`database/seeders/`)
| File | Jumlah Data |
|------|----------|
| `ProductSeeder.php` | 28 produk di 6 kategori |
| `DatabaseSeeder.php` | (diupdate) Panggil ProductSeeder |

### 6. **Routes** (`routes/web.php`)
```php
GET    /menu                      → MenuController@index
GET    /menu/{product}            → MenuController@show
GET    /cart                      → CartController@index
POST   /cart/add                  → CartController@add
POST   /cart/{cartItem}/update    → CartController@update
POST   /cart/{cartItem}/remove    → CartController@remove
POST   /cart/clear                → CartController@clear
GET    /cart/count                → CartController@count
GET    /checkout                  → OrderController@checkout
POST   /order                     → OrderController@store
GET    /order/{order}/receipt     → OrderController@receipt
GET    /orders                    → OrderController@history
GET    /order/{order}             → OrderController@show
```

### 7. **Policies** (`app/Policies/`)
- `CartItemPolicy.php` - Otorisasi update/delete cart items (hanya pemilik)

### 8. **Service Provider** (`app/Providers/`)
- `AuthServiceProvider.php` - (dibuat) Register policies

---

## 🚀 Langkah-Langkah Setup

### **Step 1: Install Dependencies**
```bash
cd "d:\project PBL\project_cafe_berco"
composer install
npm install
```

### **Step 2: Generate App Key**
```bash
php artisan key:generate
```

### **Step 3: Update .env**
Pastikan sudah benar:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=cafe_berco
DB_USERNAME=root
DB_PASSWORD=
```

### **Step 4: Jalankan Migrations & Seeders**
```bash
php artisan migrate
php artisan db:seed
```

**Apa yang terjadi:**
- Membuat tabel: `products`, `orders`, `order_items`, `cart_items`
- Membuat user test: `test@example.com` / `password`
- Seed 28 produk ke tabel `products`

### **Step 5: Build Frontend Assets**
```bash
npm run build
```

Atau untuk development dengan hot reload:
```bash
npm run dev
```

### **Step 6: Jalankan Laravel Server**
```bash
php artisan serve
```

Buka browser: **http://localhost:8000**

---

## 🧪 Testing

### Login Akun Test
- **Email**: `test@example.com`
- **Password**: `password`

### Test Menu Browsing
1. Buka `/menu` (tidak perlu login)
2. Coba filter kategori, harga, atau search

### Test Cart & Checkout
1. Login dengan akun test
2. Klik "Tambah ke Keranjang" di menu manapun
3. Buka `/cart` untuk lihat keranjang
4. Klik "Lanjut ke Pembayaran"
5. Pilih metode pembayaran dan layanan
6. Klik "Konfirmasi Pembayaran"
7. Lihat struk di halaman receipt

### Test Order History
1. Login
2. Buka `/orders` untuk melihat riwayat pesanan

---

## 📊 Database Schema

### **Table: products**
```sql
id, name, slug, description, category, price, image_url, available, created_at, updated_at
```

**Kategori:**
- `kopi` (7 produk)
- `non-kopi` (4 produk)
- `ice-blend` (4 produk)
- `snack` (4 produk)
- `dessert` (4 produk)
- `makanan` (5 produk)

### **Table: orders**
```sql
id, user_id, order_number, status, service_type, payment_method, 
subtotal, tax, total, notes, completed_at, created_at, updated_at
```

**Status**: `pending`, `completed`, `cancelled`
**Service Type**: `dine_in`, `take_away`
**Payment Method**: `cash`, `debit`, `credit`
**Order Number Format**: `ORD-YYYYMMDD-#####` (misal: `ORD-20250415-00001`)

### **Table: order_items**
```sql
id, order_id, product_id, quantity, price, subtotal, created_at, updated_at
```

**Note**: `price` adalah snapshot harga saat membeli (bukan reference ke products)

### **Table: cart_items**
```sql
id, user_id, product_id, quantity, created_at, updated_at
```

**Note**: Unique constraint pada `(user_id, product_id)` - hanya 1 item per produk per user

---

## 🔒 Security Features

### Authentication & Authorization
- ✅ Menu browsing public (tidak perlu login)
- ✅ Cart & checkout protected (require login)
- ✅ User hanya bisa lihat/edit pesanan mereka sendiri
- ✅ CartItemPolicy mengecek pemilik item

### Data Protection
- ✅ CSRF token protection pada semua POST requests
- ✅ Input validation di semua controller methods
- ✅ Password hashed dengan bcrypt
- ✅ Foreign key constraints di database

### Session Management
- ✅ User sessions disimpan di database
- ✅ Session lifetime: 120 menit (configurable di `.env`)

---

## 💰 Kalkulasi & Perhitungan

### Order Total
```
Subtotal = Σ(product.price × item.quantity)
Tax = Subtotal × 10%
Total = Subtotal + Tax
```

**Dapat diubah di:**
- `OrderController::store()` - Garis: `$tax = $subtotal * 0.1;`

---

## 🎯 Alur Pengguna

### 1. **Browse Menu**
```
User → /menu → Filter/Search → Lihat Produk
```

### 2. **Add to Cart** (perlu login)
```
Login → /menu → Klik "Tambah ke Keranjang" → Item masuk database
```

### 3. **View Cart**
```
/cart → Lihat items → Update qty / Hapus item
```

### 4. **Checkout**
```
/checkout → Pilih metode pembayaran & layanan → Klik "Konfirmasi"
```

### 5. **Receipt**
```
/order/{id}/receipt → Lihat struk → Bisa cetak
```

### 6. **History**
```
/orders → Lihat semua pesanan → Klik untuk lihat detail
```

---

## 🛠️ Customization

### Ubah Tax Rate
File: `app/Http/Controllers/OrderController.php` → `store()` method
```php
$tax = $subtotal * 0.1;  // Ubah 0.1 jadi nilai lain
```

### Ubah Kategori Produk
File: `app/Http/Controllers/MenuController.php` → `index()` method
```php
$categories = ['kopi', 'non-kopi', 'ice-blend', 'snack', 'dessert', 'makanan'];
```

### Ubah Session Timeout
File: `.env`
```env
SESSION_LIFETIME=120  // Ganti jadi nilai lain (menit)
```

---

## 📝 API Responses

### GET /menu
```json
{
  "data": [...],
  "current_page": 1,
  "total": 28,
  "last_page": 3
}
```

### POST /cart/add
```json
{
  "success": true,
  "message": "Produk ditambahkan ke keranjang",
  "cart_count": 5
}
```

### GET /cart/count
```json
{
  "count": 5
}
```

### POST /order
```json
{
  "success": true,
  "message": "Pesanan berhasil dibuat",
  "order_id": 1,
  "redirect": "/order/1/receipt"
}
```

---

## ⚠️ Catatan Penting

1. **Database Must Exist**: Database `cafe_berco` harus sudah ada di MySQL
2. **Credentials**: Update `.env` dengan credentials MySQL Anda
3. **App Key**: Jangan lupa `php artisan key:generate`
4. **Assets**: Jalankan `npm run build` sebelum production
5. **Permissions**: Pastikan folder `storage/` dan `bootstrap/cache/` writable

---

## 📞 Troubleshooting

### Error: "SQLSTATE[HY000]: General error: 1030"
**Solusi**: Pastikan database `cafe_berco` exist dan credentials benar di `.env`

### Error: "No query results for model"
**Solusi**: Jalankan `php artisan migrate` dan `php artisan db:seed`

### Cart item tidak tersimpan
**Solusi**: Pastikan user sudah login dan session driver di `.env` adalah `database`

### 404 pada route tertentu
**Solusi**: Pastikan `routes/web.php` sudah update dan jalankan `php artisan cache:clear`

---

## 📚 Referensi File

### Core Implementation Files:
- Models: `app/Models/{Product,Order,OrderItem,CartItem}.php`
- Controllers: `app/Http/Controllers/{Menu,Cart,Order}Controller.php`
- Views: `resources/views/{menu,cart,checkout,receipt,order-history}.blade.php`
- Routes: `routes/web.php`
- Migrations: `database/migrations/2025_01_15_*.php`
- Seeder: `database/seeders/ProductSeeder.php`

### Configuration:
- `.env` - Database & app config
- `config/database.php` - Database connections
- `routes/web.php` - URL routes

---

## ✅ Checklist Sebelum Production

- [ ] Update `.env` dengan credentials yang benar
- [ ] Jalankan `php artisan migrate`
- [ ] Jalankan `php artisan db:seed`
- [ ] Jalankan `npm run build`
- [ ] Test semua fitur (menu, cart, checkout)
- [ ] Ubah `APP_DEBUG=true` → `APP_DEBUG=false` di `.env`
- [ ] Set up email untuk order notifications (opsional)
- [ ] Integrasikan payment gateway (opsional)
- [ ] Set up SSL/HTTPS

---

**Implementasi Selesai! 🎉**

Sistem sudah siap digunakan dan terhubung ke database `cafe_berco` tanpa hardcoding. 
Semua fitur menu browsing, cart management, dan payment/receipt sudah terimplemen.
