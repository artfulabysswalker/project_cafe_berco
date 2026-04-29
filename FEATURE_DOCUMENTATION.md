# Fitur Reward Voucher & Comeback Notification System

## Deskripsi
Implementasi dua user story untuk meningkatkan engagement pelanggan yang tidak aktif:

### 1. Story – Inactive Customer Voucher Reward
**Sebagai admin**, saya ingin sistem mengirim voucher promo otomatis kepada member yang tidak berkunjung >30 hari untuk menarik mereka kembali.

### 2. Story – Comeback Notification System
**Sebagai pelanggan**, saya ingin menerima notifikasi pengingat jika sudah lama tidak mengunjungi website cafe, agar saya terdorong untuk kembali menggunakan website tersebut.

## Database Schema

### Tabel-tabel Baru

#### `vouchers`
```sql
CREATE TABLE vouchers (
    id BIGINT PRIMARY KEY,
    code VARCHAR(50) UNIQUE,
    title VARCHAR(255),
    description TEXT,
    discount_type ENUM('percentage', 'fixed'),
    discount_value DECIMAL(8,2),
    quantity INT,
    quantity_used INT DEFAULT 0,
    minimum_purchase DECIMAL(10,2),
    valid_from DATETIME,
    valid_until DATETIME,
    is_active BOOLEAN DEFAULT true,
    voucher_type ENUM('automatic', 'manual'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### `user_vouchers` (Pivot Table)
```sql
CREATE TABLE user_vouchers (
    id BIGINT PRIMARY KEY,
    user_id BIGINT FOREIGN KEY,
    voucher_id BIGINT FOREIGN KEY,
    assigned_at DATETIME DEFAULT NOW,
    used_at DATETIME,
    is_used BOOLEAN DEFAULT false,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(user_id, voucher_id)
);
```

#### `notifications`
```sql
CREATE TABLE notifications (
    id BIGINT PRIMARY KEY,
    user_id BIGINT FOREIGN KEY,
    title VARCHAR(255),
    message TEXT,
    type ENUM('comeback_reminder', 'voucher_offered', 'order_update', 'promotional'),
    related_url VARCHAR(255),
    is_read BOOLEAN DEFAULT false,
    read_at DATETIME,
    is_sent BOOLEAN DEFAULT false,
    channel ENUM('in_app', 'email', 'both'),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### Update `users` table
```sql
ALTER TABLE users ADD COLUMN (
    phone VARCHAR(20),
    last_visit_at DATETIME,
    is_active BOOLEAN DEFAULT true,
    notification_enabled BOOLEAN DEFAULT true,
    role ENUM('customer', 'admin') DEFAULT 'customer'
);
```

## Models

### Voucher Model
- Relasi BelongsToMany dengan User (melalui user_vouchers)
- Method: `isValid()`, `hasQuantityAvailable()`, `assignToUser()`

### UserVoucher Model
- Relasi BelongsTo dengan User dan Voucher
- Method: `markAsUsed()`

### Notification Model
- Relasi BelongsTo dengan User
- Method: `markAsRead()`, `markAsSent()`
- Scope: `unread()`, `unsent()`, `comebackReminders()`

### User Model Update
- Relasi BelongsToMany dengan Voucher
- Relasi HasMany dengan Notification
- Method: `isInactive()`, `updateLastVisit()`, `daysSinceLastVisit()`, `isAdmin()`

## Services

### VoucherService
Menangani logika bisnis untuk voucher:
- `assignVoucherToUser()` - Assign voucher ke user
- `createVoucherNotification()` - Buat notifikasi untuk voucher
- `sendVouchersToInactiveCustomers()` - Kirim voucher otomatis ke inactive customers

### NotificationService
Menangani logika bisnis untuk notifikasi:
- `sendComebackReminders()` - Kirim reminder ke inactive customers
- `createComebackReminder()` - Buat reminder notification
- `create()` - Buat notifikasi umum
- `getUnreadCount()` - Hitung notifikasi yang belum dibaca
- `markAllAsSent()` - Tandai semua notifikasi sebagai terkirim

## Controllers

### VoucherController
- `index()` - Daftar voucher
- `create()` - Form buat voucher
- `store()` - Simpan voucher baru
- `edit()` - Form edit voucher
- `update()` - Update voucher
- `destroy()` - Hapus voucher
- `sendToInactiveCustomers()` - Kirim voucher ke inactive customers
- `toggleActive()` - Toggle status voucher

### NotificationController
- `index()` - Daftar notifikasi user
- `markAsRead()` - Tandai notifikasi sebagai dibaca
- `markAllAsRead()` - Tandai semua notifikasi sebagai dibaca
- `destroy()` - Hapus notifikasi
- `getUnreadCount()` - API endpoint untuk hitung notifikasi belum dibaca

## Routes

### Customer Routes
```
GET /notifications                      - Lihat notifikasi
POST /notifications/{id}/mark-as-read   - Tandai dibaca
POST /notifications/mark-all-as-read    - Tandai semua dibaca
DELETE /notifications/{id}              - Hapus notifikasi
GET /api/notifications/unread-count     - Hitung unread
```

### Admin Routes
```
GET /admin/vouchers                     - Daftar voucher
GET /admin/vouchers/create              - Form buat voucher
POST /admin/vouchers                    - Simpan voucher
GET /admin/vouchers/{id}/edit           - Form edit voucher
PUT /admin/vouchers/{id}                - Update voucher
DELETE /admin/vouchers/{id}             - Hapus voucher
POST /admin/vouchers/{id}/toggle-active - Toggle status
POST /admin/vouchers/send-to-inactive   - Kirim ke inactive customers
```

## Middleware

### TrackUserLastVisit
Update `last_visit_at` setiap 1 jam untuk authenticated users.
Dipanggil otomatis untuk setiap request.

### IsAdmin
Validasi bahwa user adalah admin sebelum akses admin routes.

## Artisan Commands

### SendInactiveCustomerVouchersCommand
```bash
php artisan inactive-customers:send-vouchers
```
Mengirim:
- Voucher otomatis ke inactive customers (>30 hari tidak berkunjung)
- Notifikasi reminder ke inactive customers

**Setup Scheduler:**
Tambahkan ke `app/Console/Kernel.php`:
```php
$schedule->command('inactive-customers:send-vouchers')->daily();
```

## Fitur Utama

### 1. Tracking User Activity
- Setiap authenticated user akan memiliki `last_visit_at` yang diupdate otomatis
- Update dilakukan maksimal 1x per jam untuk performa

### 2. Automatic Voucher Distribution
- Admin bisa membuat voucher dengan tipe "Otomatis"
- Sistem otomatis mengirim ke customers tidak aktif >30 hari
- Dilakukan via scheduler job yang bisa dijalankan daily

### 3. Comeback Reminders
- Sistem otomatis mengirim reminder notification setiap hari ke inactive customers
- Reminder hanya dikirim 1x per 7 hari per customer

### 4. Notification Management
- Customers bisa melihat semua notifikasi mereka
- Bisa tandai notifikasi sebagai dibaca
- Bisa hapus notifikasi
- Bisa lihat jumlah notifikasi belum dibaca via API

## Testing

### Manual Testing untuk Admin

1. **Buat Voucher:**
   - Login sebagai admin
   - Go to /admin/vouchers/create
   - Isi form dengan data voucher

2. **Test Automatic Voucher Sending:**
   - Buat customer user
   - Set `last_visit_at` menjadi >30 hari yang lalu (via tinker)
   - Jalankan: `php artisan inactive-customers:send-vouchers`
   - Cek di user_vouchers dan notifications table

3. **Test Manual Voucher Sending:**
   - Di halaman voucher index, pilih voucher
   - Klik "Kirim ke Inactive Customers"

### Manual Testing untuk Customer

1. **View Notifications:**
   - Login sebagai customer
   - Go to /notifications
   - Lihat daftar notifikasi

2. **Interact dengan Notifications:**
   - Klik "Tandai sebagai dibaca"
   - Klik "Hapus"
   - Lihat unread count via `/api/notifications/unread-count`

## Implementation Notes

### Security
- Notification Policy memastikan user hanya bisa melihat notifikasi mereka sendiri
- Middleware admin memastikan hanya admin yang bisa akses voucher management

### Performance
- `last_visit_at` hanya diupdate 1x per jam (cek di TrackUserLastVisit middleware)
- Notification table memiliki indexes pada `user_id` dan `created_at`

### Future Improvements
- Email integration untuk notifikasi
- SMS integration untuk reminder
- Dashboard untuk admin melihat stats
- Analytics untuk track effectiveness
- A/B testing untuk messaging

## File Structure
```
app/
├── Console/
│   └── Commands/
│       └── SendInactiveCustomerVouchersCommand.php
├── Http/
│   ├── Controllers/
│   │   ├── VoucherController.php
│   │   └── NotificationController.php
│   └── Middleware/
│       ├── IsAdmin.php
│       └── TrackUserLastVisit.php
├── Models/
│   ├── Voucher.php
│   ├── UserVoucher.php
│   ├── Notification.php
│   └── User.php (updated)
├── Policies/
│   └── NotificationPolicy.php
└── Services/
    ├── VoucherService.php
    └── NotificationService.php

database/
└── migrations/
    ├── 2025_01_20_000000_create_vouchers_table.php
    ├── 2025_01_20_000001_create_user_vouchers_table.php
    ├── 2025_01_20_000002_create_notifications_table.php
    ├── 2025_01_20_000003_add_last_visit_to_users_table.php
    └── 2025_01_20_000004_add_role_to_users_table.php

resources/views/
├── notifications/
│   └── index.blade.php
└── admin/vouchers/
    ├── index.blade.php
    ├── create.blade.php
    └── edit.blade.php

routes/
└── web.php (updated)
```

## Setup Instructions

1. **Run migrations:**
   ```bash
   php artisan migrate
   ```

2. **Seed database dengan admin user:**
   ```bash
   php artisan db:seed
   ```

3. **Setup scheduler (for automated tasks):**
   Tambahkan cron job:
   ```bash
   * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
   ```

4. **Test manual command:**
   ```bash
   php artisan inactive-customers:send-vouchers
   ```

## Branch Information
Fitur ini dikembangkan di branch: `feature/inactive-customer-rewards-notification`
