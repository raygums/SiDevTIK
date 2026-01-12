# 🚀 Domain TIK - Dockerized Development Environment

Dokumentasi teknis untuk setup dan workflow pengembangan proyek Domain TIK menggunakan Docker.

## 🛠️ Tech Stack
* **Framework:** Laravel 12 (Bleeding Edge)
* **Language:** PHP 8.2 & Node.js 20
* **Database:** PostgreSQL 15
* **Frontend:** Tailwind CSS + Vite
* **Container:** Docker & Docker Compose V2

---

## ⚡ Quick Start (Instalasi 1x Jalan)

Proyek ini menggunakan **Automated Entrypoint**. Anda tidak perlu menginstall dependensi manual.

1.  **Clone & Masuk Direktori**
    ```bash
    git clone <repo_url>
    cd domaintik
    ```

2.  **Jalankan Docker**
    ```bash
    docker compose up -d --build
    ```
    > **Catatan:** Saat pertama kali dijalankan, container akan memproses `composer install`, `npm install`, `key:generate`, dan `migrate` secara otomatis. Tunggu 1-2 menit hingga log menunjukkan "Starting Apache".

3.  **Akses Aplikasi**
    * **Web App:** [http://localhost:8080](http://localhost:8080)
    * **Database Manager (Adminer):** [http://localhost:8081](http://localhost:8081)
        * *System:* PostgreSQL
        * *Server:* `db`
        * *User/Pass:* Sesuai file `.env` (Default: `postgres`/`postgres`)

---

## 👨‍💻 Development Workflow (Cara Kerja)

### 1. Mode Frontend Development (Hot Reload)
Gunakan mode ini saat Anda sedang **aktif mengedit** tampilan (Blade/Tailwind/JS) agar perubahan terlihat instan.
```bash
docker compose exec app npm run dev
```