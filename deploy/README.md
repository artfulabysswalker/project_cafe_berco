# Cafe Berco — Deploy dengan Podman + Online via Nginx

Panduan lengkap untuk menjalankan aplikasi di dalam **Podman** lalu membukanya
dari internet memakai **Nginx** (berlaku juga untuk VPS/Debian/Fedora).

---

## 1. Prasyarat

```bash
# Podman sudah terpasang (periksa):
podman --version          # mis. podman version 5.8.7

# Opsional, agar bisa memakai deploy/compose.yml (bila tidak ada gunakan deploy/run.sh):
#   pip install podman-compose        atau
#   sudo dnf install podman-compose   atau
#   sudo apt install podman-compose
```

Buka file `.env` dan pastikan nilai-nilai penting (jangan di-commit):

```ini
APP_ENV=production
APP_DEBUG=false
APP_URL=https://berco.example.com
QR_CODE_BASE_URL=https://berco.example.com
DB_CONNECTION=mysql
DB_HOST=db            # dipaksa oleh deploy (compose/run.sh)
DB_DATABASE=cafe_berco
DB_USERNAME=cafe_admin
DB_PASSWORD=<sama dengan MARIADB_PASSWORD di compose.yml>
SESSION_DRIVER=database   # opsional: tahan login walau container restart
```

> `APP_KEY` harus sudah terisi (untuk environment baru: `php artisan key:generate`).
> `QR_CODE_BASE_URL` ikut terenkripsi di dalam QR meja. Bila domain berubah,
> QR lama harus **di-regenerate dari menu admin Meja > Generate QR**.

---

## 2. Jalankan aplikasi di Podman

### Opsi A — pakai compose (bila podman-compose terpasang)

```bash
podman-compose -f deploy/compose.yml up -d --build
podman-compose -f deploy/compose.yml ps
```

### Opsi B — murni podman CLI (tidak butuh paket tambahan)

```bash
chmod +x deploy/run.sh
./deploy/run.sh

# Cek log
podman logs -f cafe-berco-app
```

Uji lokal dulu:

```bash
curl -I http://127.0.0.1:8080        # harap 200/302
```

### Migrasi database (sekali, setelah container db siap)

```bash
podman exec -it cafe-berco-app php artisan migrate --force
podman exec -it cafe-berco-app php artisan db:seed --force   # data awal bila perlu
```

---

## 3. Siapkan domain & akses internet

Ada dua jalur. **Jalur B** cocok bila tidak punya IP statis / tidak mau repot
dengan port forward di router.

### Jalur A — IP publik + port-forward + Nginx (hanya server/hosting VPS/DNS)

1. Arahkan **A record** domain ke IP publik server, mis. `A berco.example.com -> 203.0.113.10`.
2. Di router: forward port **80** dan **443** ke IP lokal server ini.
3. Buka firewall server:

```bash
# firewalld (Fedora)
sudo firewall-cmd --permanent --add-service=http --add-service=https
sudo firewall-cmd --reload

# atau ufw (Ubuntu/Debian)
sudo ufw allow 80/tcp && sudo ufw allow 443/tcp
```

4. Pasang Nginx + Certbot:

```bash
# Fedora
sudo dnf install nginx certbot python3-certbot-nginx
# Debian/Ubuntu
sudo apt update && sudo apt install nginx certbot python3-certbot-nginx
```

5. Ganti `berco.example.com` yang ada di `deploy/nginx-online.conf` dengan
   domain Anda (bisa edit file lalu salin, atau pakai `sed`):

```bash
sed "s/berco.example.com/DOMAIN_ANDA/g" deploy/nginx-online.conf | sudo tee /etc/nginx/sites-available/berco >/dev/null

sudo ln -s /etc/nginx/sites-available/berco /etc/nginx/sites-enabled/ 2>/dev/null || true
sudo nginx -t && sudo systemctl enable --now nginx
```

6. Dapatkan sertifikat HTTPS otomatis:

```bash
sudo certbot --nginx -d berco.example.com
```

### Jalur B — Cloudflare Tunnel (tanpa IP publik / tanpa port-forward)

Cocok untuk proyek di rumah/jaringan biasa.

```bash
# pasang cloudflared
#   - Fedora:   sudo dnf install cloudflared
#   - Debian:   sudo apt install cloudflared
#   - atau unduh dari https://github.com/cloudflare/cloudflared/releases

cloudflared tunnel login
cloudflared tunnel create berco
cloudflared tunnel route dns berco berco.example.com
```

Buat `/etc/cloudflared/berco.yml`:

```yaml
tunnel: berco
credentials-file: /root/.cloudflared/<uuid>.json
ingress:
  - hostname: berco.example.com
    service: http://127.0.0.1:8080
  - service: http_status:404
```

Jalankan (atau instal sebagai service `cloudflared service install`):

```bash
cloudflared tunnel run berco
```

Cloudflare menangani HTTPS secara otomatis, jadi cukup arahkan DNS (orange-cloud)
di dashboard Cloudflare. Aplikasi tetap berjalan di container Podman di `:8080`.

---

## 4. Catatan penting untuk akses dari internet

- **laravel `local.network` di route scan QR.** Jika `TRUST_PROXIES` TIDAK
  dikonfigurasi, Laravel melihat `REMOTE_ADDR` (yaitu IP Nginx/`127.0.0.1` dari
  loopback), sehingga middleware jaringan lokal otomatis lolos dan pelanggan di
  luar Wi-Fi kafe tetap bisa scan. Jangan mengaktifkan trusted proxies bila
  memang ingin QR bekerja online. Bila sebaliknya QR hanya boleh untuk Wi-Fi
  kafe, biarkan dan nonaktifkan akses online ke route `/order?token=...`.
- **Session/cart tetap bertahan** karena `storage` dipetakan sebagai volume;
  bila beralih `SESSION_DRIVER=database` pastikan tabel sessions sudah dibuat.
- **Backup volume** DB: `podman volume export cafe-berco-db` atau `mysqldump`:
  `podman exec cafe-berco-db sh -c 'exec mariadb-dump -u$MARIADB_USER -p$MARIADB_PASSWORD cafe_berco' > backup.sql`
- **QR lama / foto menu**: file tersimpan di `storage/app/public`. Untuk
  memindahkan dari mesin dev, salin isi `storage/app/public` ke volume:
  `podman cp storage/app/public cafe-berco-app:/var/www/html/storage/app/public`.
- **Perbaikan:** log ada di `podman logs cafe-berco-app` dan `storage/logs`.

---

## Daftar file

| File | Fungsi |
|------|--------|
| `deploy/Containerfile` | Image aplikasi: Nginx + PHP-FPM + dependensi |
| `deploy/nginx-default.conf` | Konfigurasi Nginx di dalam container |
| `deploy/php-opcache.ini` | Pengaturan PHP produksi |
| `deploy/entrypoint.sh` | Perintah awal container (storage:link + jalankan server) |
| `deploy/compose.yml` | Orchestra (app + db) untuk podman-compose |
| `deploy/run.sh` | Alternatif jalankan murni `podman` CLI |
| `deploy/nginx-online.conf` | Reverse proxy Nginx di host untuk akses publik |
| `deploy/README.md` | Panduan ini |