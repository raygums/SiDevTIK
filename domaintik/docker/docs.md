Cara Menjalankan (Deployment)
Persiapan: Pastikan file .env di server sudah disesuaikan untuk production (Matikan APP_DEBUG, set database password yang kuat).

Build & Run: Gunakan flag -f untuk menunjuk file compose khusus production.

```
# Build image (bisa agak lama karena proses compile npm & composer)
docker compose -f docker-compose.prod.yml build

# Jalankan di background
docker compose -f docker-compose.prod.yml up -d
```