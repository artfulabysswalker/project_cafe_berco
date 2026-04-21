# ⚡ Quick Start Guide - Cafe Berco

## 5 Minutes Setup

### 1️⃣ Install & Setup
```bash
cd "d:\project PBL\project_cafe_berco"
composer install && npm install
php artisan key:generate
```

### 2️⃣ Configure Database
Create database:
```sql
CREATE DATABASE cafe_berco;
```

Update `.env`:
```env
DB_CONNECTION=mysql
DB_DATABASE=cafe_berco
DB_USERNAME=root
DB_PASSWORD=
```

### 3️⃣ Run Migrations & Seed
```bash
php artisan migrate
php artisan db:seed
```

### 4️⃣ Build Assets
```bash
npm run build
```

### 5️⃣ Start Server
```bash
php artisan serve
```

🎉 Done! Visit: **http://localhost:8000**

---

## 📝 Test Account
- Email: `test@example.com`
- Password: `password`

---

## 🗺️ Main Routes

| URL | Deskripsi |
|-----|-----------|
| `/` | Homepage |
| `/menu` | Browse menu (public) |
| `/cart` | Shopping cart (login required) |
| `/checkout` | Payment (login required) |
| `/order/{id}/receipt` | Order receipt |
| `/orders` | Order history |

---

## 💡 Quick Features

✅ Browse Menu dengan filter & search  
✅ Tambah/Kurang/Hapus dari keranjang  
✅ Checkout dengan pilihan pembayaran  
✅ Print struk pesanan  
✅ Lihat riwayat pesanan  

---

## 📦 Database Tables

```
products         (28 produk di 6 kategori)
orders          (pesanan dari users)
order_items     (item dalam pesanan)
cart_items      (item di keranjang)
users           (user accounts)
```

---

## 🔒 Catatan Keamanan

- Cart & checkout hanya untuk user yang login
- User hanya bisa lihat pesanan mereka sendiri
- Semua database credentials di `.env` (bukan hardcoded)

---

**Lihat `IMPLEMENTATION_GUIDE.md` untuk dokumentasi lengkap**
