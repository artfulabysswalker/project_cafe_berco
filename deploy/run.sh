#!/bin/sh
# Alternatif tanpa podman-compose / docker-compose: build + jalankan murni podman CLI.
# Jalankan dari direktori proyek:  ./deploy/run.sh
set -e

cd "$(dirname "$0")/.."

echo "[1/4] Membangun image cafe-berco:latest ..."
podman build -t cafe-berco:latest -f deploy/Containerfile .

echo "[2/4] Membuat network + volume ..."
podman network create cafe-berco-net 2>/dev/null || true
podman volume create cafe-berco-storage 2>/dev/null || true
podman volume create cafe-berco-db 2>/dev/null || true

echo "[3/4] Menjalankan database (mariadb:11) ..."
if ! podman container exists cafe-berco-db; then
    podman run -d --name cafe-berco-db \
        --network cafe-berco-net \
        --network-alias db \
        -v cafe-berco-db:/var/lib/mysql \
        -e MARIADB_DATABASE="${DB_DATABASE:-cafe_berco}" \
        -e MARIADB_USER="${DB_USERNAME:-cafe_admin}" \
        -e MARIADB_PASSWORD="${DB_PASSWORD:-password_baru}" \
        -e MARIADB_ROOT_PASSWORD="${MARIADB_ROOT_PASSWORD:-ubah-root-ini}" \
        docker.io/library/mariadb:11
fi

echo "[4/4] Menjalankan aplikasi ..."
if podman container exists cafe-berco-app; then
    podman rm -f cafe-berco-app
fi
podman run -d --name cafe-berco-app \
    --network cafe-berco-net \
    --env-file .env \
    -e DB_HOST=db \
    -e DB_PORT=3306 \
    -v cafe-berco-storage:/var/www/html/storage \
    -p 127.0.0.1:8080:80 \
    cafe-berco:latest

echo "Selesai. Aplikasi di http://127.0.0.1:8080"
echo "Log: podman logs -f cafe-berco-app"