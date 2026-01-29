# SI Project TIK - Local Development

Panduan untuk menjalankan SI Project TIK di environment local menggunakan Docker.

## Prerequisites

- Docker Desktop (Windows/Mac) atau Docker Engine (Linux)
- Git Bash (Windows) atau Terminal (Mac/Linux)
- PostgreSQL berjalan di local (Laragon/standalone)

## Struktur File

```
deployment/local/
├── docker-compose.yml    # Konfigurasi Docker services
├── Dockerfile            # Build image Laravel + Node.js
├── docker-entrypoint.sh  # Script inisialisasi container
├── deploy.sh             # Helper script interaktif
├── .env.example          # Template environment variables
├── .env                  # Environment variables (dibuat dari .env.example)
└── README.md             # Dokumentasi ini
```

## Quick Start

### 1. Setup Environment

```bash
cd C:/laragon/www/si-project-tik/deployment/local

# Copy environment file
cp .env.example .env

# Edit .env sesuai kebutuhan (database credentials, dll)
```

### 2. Buat Database

Pastikan database PostgreSQL sudah dibuat di local:

```sql
CREATE DATABASE si_project_tik;
```

### 3. Jalankan Aplikasi

**Opsi A: Menggunakan Helper Script (Recommended)**

```bash
./deploy.sh
```

Pilih menu `1) Start Development`

**Opsi B: Menggunakan Docker Compose langsung**

```bash
docker-compose up -d --build
```

### 4. Akses Aplikasi

| Service | URL |
|---------|-----|
| Aplikasi | http://localhost:8090 |
| Vite HMR | http://localhost:5173 |

## Environment Variables

| Variable | Default | Keterangan |
|----------|---------|------------|
| `APP_PORT` | 8090 | Port aplikasi Laravel |
| `DB_PORT` | 5432 | Port PostgreSQL |
| `DB_DATABASE` | si_project_tik | Nama database |
| `DB_USERNAME` | postgres | Username database |
| `DB_PASSWORD` | (kosong) | Password database |

## Helper Script (deploy.sh)

Script interaktif untuk memudahkan operasi development:

```bash
./deploy.sh
```

### Menu Tersedia

| No | Operasi | Keterangan |
|----|---------|------------|
| 1 | Start Development | Jalankan semua containers |
| 2 | Stop Development | Hentikan semua containers |
| 3 | Clean Rebuild | Hapus dan rebuild dari awal |
| 4 | Quick Rebuild | Rebuild tanpa hapus volume |
| 10 | Show Status | Lihat status containers |
| 11 | Show Logs | Lihat logs aplikasi |
| 12 | Test Endpoint | Test apakah app berjalan |
| 20 | Run Migrations | Jalankan database migrations |
| 21 | Fresh Migration | Reset database + seed |
| 22 | Clear Cache | Bersihkan semua cache |
| 23 | Artisan Command | Jalankan perintah artisan |
| 24 | App Shell | Akses shell container |
| 30 | NPM Install | Install dependencies JS |
| 31 | NPM Build | Build assets untuk production |
| 40 | Cleanup | Bersihkan Docker resources |
| 41 | Remove All | Hapus semua containers & images |

## Commands Manual

### Container Management

```bash
# Start
docker-compose up -d

# Stop
docker-compose down

# Rebuild
docker-compose up -d --build

# Logs
docker logs si-project-tik-app-dev -f

# Status
docker ps --filter "name=si-project-tik"
```

### Laravel Commands

```bash
# Artisan
docker exec si-project-tik-app-dev php artisan migrate
docker exec si-project-tik-app-dev php artisan db:seed
docker exec si-project-tik-app-dev php artisan optimize:clear

# Composer
docker exec si-project-tik-app-dev composer install
docker exec si-project-tik-app-dev composer update

# NPM
docker exec si-project-tik-app-dev npm install
docker exec si-project-tik-app-dev npm run build
```

### Shell Access

```bash
docker exec -it si-project-tik-app-dev sh
```

## Troubleshooting

### Port Already in Use

Jika port 8090 atau 5173 sudah digunakan:

1. Edit `.env` dan ubah `APP_PORT` ke port lain
2. Restart container: `docker-compose down && docker-compose up -d`

### Database Connection Failed

1. Pastikan PostgreSQL berjalan di local
2. Pastikan database sudah dibuat
3. Cek credentials di `.env`
4. Pastikan PostgreSQL menerima koneksi dari Docker (`host.docker.internal`)

### Vite/HMR Not Working

1. Pastikan port 5173 tidak diblokir firewall
2. Hard refresh browser: `Ctrl+Shift+R`
3. Cek logs: `docker logs si-project-tik-app-dev`

### Permission Denied

```bash
# Di dalam container
docker exec si-project-tik-app-dev chmod -R 777 storage bootstrap/cache
```

## Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Docker Container                      │
│  ┌─────────────────────────────────────────────────┐    │
│  │              si-project-tik-app-dev              │    │
│  │                                                  │    │
│  │  ┌──────────────┐      ┌──────────────┐        │    │
│  │  │   Laravel    │      │    Vite      │        │    │
│  │  │  Port 8000   │      │  Port 5173   │        │    │
│  │  └──────────────┘      └──────────────┘        │    │
│  │         │                     │                 │    │
│  └─────────┼─────────────────────┼─────────────────┘    │
│            │                     │                       │
└────────────┼─────────────────────┼───────────────────────┘
             │                     │
        Port 8090             Port 5173
             │                     │
             ▼                     ▼
┌─────────────────────────────────────────────────────────┐
│                      Host Machine                        │
│                                                          │
│  ┌──────────────────────────────────────────────────┐   │
│  │                   PostgreSQL                      │   │
│  │                   Port 5432                       │   │
│  └──────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────┘
```
