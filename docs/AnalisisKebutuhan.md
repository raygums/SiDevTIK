# Kebutuhan Fungsional dan Non-Fungsional Sistem Domaintik

Dokumen ini menjelaskan secara keseluruhan kebutuhan fungsional (Functional Requirements) dan kebutuhan non-fungsional (Non-Functional Requirements) untuk sistem Domaintik (Domain TIK Unila). Dokumen ini memetakan akses fitur secara detail dan spesifik berdasarkan tingkatan peran pengguna, termasuk fitur ringkasan, riwayat, dan penyaringan data (filter).

---

## 1. Kebutuhan Fungsional Berdasarkan Peran (Role-Based Access)

Sistem ini menerapkan *Role-Based Access Control* (RBAC) di mana akses fitur dibatasi sesuai dengan kewenangan pengguna. Berikut adalah pembagian fitur berdasarkan peran:

### 1.1. Peran: Pengguna / Pemohon (User)
Pengguna adalah dosen, staf, atau perwakilan organisasi mahasiswa yang membutuhkan layanan TIK.
*   **Registrasi & Autentikasi:** Sistem harus memungkinkan pengguna untuk mendaftar akun baru, masuk (login), dan keluar (logout) dari sistem.
*   **Manajemen Profil:** Sistem harus memfasilitasi pengguna untuk mengubah kata sandi dan melihat informasi profil.
*   **Dashboard & Ringkasan:** Sistem harus menyediakan dashboard personal yang menampilkan ringkasan jumlah pengajuan (total, diproses, selesai, ditolak) milik pengguna.
*   **Pengajuan Layanan:**
    *   Sistem harus menyediakan formulir bagi pengguna untuk mengajukan layanan **Sub Domain** (termasuk spesifikasi nama domain dan penanggung jawab).
    *   Sistem harus menyediakan formulir bagi pengguna untuk mengajukan layanan **Hosting** (termasuk pemilihan kuota penyimpanan).
    *   Sistem harus menyediakan formulir bagi pengguna untuk mengajukan layanan **VPS** (termasuk spesifikasi CPU, RAM, Storage, OS).
*   **Pemrosesan Form:** 
    *   Sistem dapat menghasilkan format keluaran form pengajuan berupa **Paperless** (tampilan digital cetak peramban) atau **Hardcopy** (berkas PDF yang dapat diunduh).
    *   Sistem harus menyediakan fitur bagi pengguna untuk mengunggah pindaian dokumen/form pengajuan yang telah ditandatangani oleh Pimpinan/Dekan.
*   **Pelacakan & Riwayat:** Sistem harus menampilkan daftar riwayat seluruh pengajuan yang telah dibuat oleh pengguna beserta status terkininya.
*   **Pencarian & Filter:** Sistem harus menyediakan fitur pencarian berdasarkan nomor tiket dan fungsi penyaringan (filter) berdasarkan status pengajuan pada halaman riwayat pengguna.

### 1.2. Peran: Verifikator (Verificator)
Verifikator adalah staf TIK yang bertugas memvalidasi persyaratan administratif pengajuan.
*   **Autentikasi & Profil:** Sistem harus memungkinkan verifikator untuk masuk (login), keluar (logout), dan mengubah kata sandi akun mereka.
*   **Dashboard & Ringkasan:** Sistem harus menampilkan dashboard ringkasan jumlah permohonan yang berstatus "Diajukan" (menunggu verifikasi) dan statistik tugas verifikasi lainnya.
*   **Validasi Pengajuan:**
    *   Sistem harus menampilkan daftar permohonan layanan yang berstatus "Diajukan" kepada verifikator.
    *   Sistem harus menyediakan antarmuka bagi verifikator untuk memeriksa detail pengajuan, kelengkapan form, dan keabsahan dokumen tanda tangan yang diunggah.
    *   Sistem harus dapat memproses verifikasi permohonan yang dilakukan oleh verifikator dengan mengubah status pengajuan menjadi "Diverifikasi".
    *   Sistem harus dapat memproses penolakan permohonan yang tidak memenuhi syarat oleh verifikator dengan mengubah status menjadi "Ditolak" beserta kewajiban mengisi catatan penolakan.
*   **Riwayat Pekerjaan:** Sistem harus menampilkan riwayat permohonan yang telah berhasil diverifikasi atau ditolak oleh verifikator tersebut di masa lalu.
*   **Pencarian & Filter:** Sistem harus memungkinkan verifikator untuk melakukan pencarian tiket permohonan dan menyaring data (filter) berdasarkan jenis layanan, unit kerja, atau tanggal pengajuan.

### 1.3. Peran: Eksekutor (Executor / Teknisi)
Eksekutor adalah staf teknis (SysAdmin/Network Admin) yang bertugas mengeksekusi layanan secara teknis di server.
*   **Autentikasi & Profil:** Sistem harus memungkinkan eksekutor untuk masuk (login), keluar (logout), dan mengubah kata sandi akun mereka.
*   **Dashboard & Ringkasan:** Sistem harus menampilkan dashboard ringkasan jumlah permohonan yang berstatus "Diverifikasi" (menunggu dieksekusi) dan permohonan yang saat ini sedang "Diproses".
*   **Pemrosesan Layanan:**
    *   Sistem harus menampilkan daftar permohonan layanan yang telah berstatus "Diverifikasi" kepada eksekutor.
    *   Sistem harus memungkinkan eksekutor mengambil alih pekerjaan dan mengubah status pengajuan menjadi "Diproses" saat mulai melakukan pengaturan teknis di server (konfigurasi DNS, alokasi Hosting/VPS).
    *   Sistem harus memungkinkan eksekutor menyelesaikan pekerjaan dan mengubah status pengajuan menjadi "Selesai" setelah layanan dipastikan aktif dan dapat digunakan.
*   **Riwayat Pekerjaan (My History):** Sistem harus menampilkan daftar riwayat pengajuan (History) layanan yang telah dieksekusi atau diselesaikan oleh eksekutor bersangkutan secara spesifik.
*   **Pencarian & Filter:** Sistem harus memungkinkan eksekutor melakukan pencarian tiket dan menyaring (filter) data permohonan berdasarkan jenis layanan atau rentang waktu pengajuan.

### 1.4. Peran: Admin (Administrator)
Admin adalah pengelola sistem utama yang memastikan operasional aplikasi berjalan lancar.
*   **Autentikasi & Profil:** Sistem harus memungkinkan admin untuk masuk (login), keluar (logout), dan mengubah kata sandi akun.
*   **Dashboard & Ringkasan Eksekutif:** Sistem harus menyediakan dasbor utama (Grand Dashboard) yang merangkum statistik seluruh pengajuan layanan (Sub Domain, Hosting, VPS), status terkini, dan metrik sistem lainnya secara terpusat.
*   **Manajemen Pengguna (User Management):** 
    *   Sistem harus menyediakan fitur bagi admin untuk melihat daftar seluruh pengguna, memverifikasi akun pengguna baru, dan melakukan manajemen akun.
    *   Sistem harus memungkinkan admin untuk menetapkan, mengubah, atau mencabut peran (assign roles) dari seorang pengguna.
*   **Manajemen Data Master (Master Data):** 
    *   Sistem harus menyediakan antarmuka bagi admin untuk menambah, mengubah, atau menghapus data Unit Kerja (`referensi.unit_kerja`).
    *   Sistem harus menyediakan antarmuka bagi admin untuk mengelola Kategori Unit (`referensi.kategori_unit`).
*   **Audit Trail & Log Aktivitas:** 
    *   Sistem harus merekam dan menampilkan log aktivitas sistem atau Audit Trail (`audit.riwayat_pengajuan`) yang mencatat siapa, apa, dan kapan sebuah aksi dilakukan.
    *   Sistem harus memungkinkan admin untuk memfilter log aktivitas (Audit Log Filters) berdasarkan aktor, jenis aksi, rentang tanggal, atau modul sistem terkait.
*   **Pemantauan Menyeluruh:** Sistem harus memungkinkan admin melihat seluruh detail pengajuan dan riwayat dari semua pengguna tanpa batasan wewenang wilayah, lengkap dengan kemampuan pencarian dan filter *advanced*.

### 1.5. Peran: Pimpinan (Leadership / Dekan / Kepala UPT)
Pimpinan bertugas untuk memberikan persetujuan akhir pada tingkat kebijakan.
*   **Persetujuan Fisik / Administratif:** Sistem harus dapat mencetak form PDF (Hardcopy/Paperless) yang tervalidasi secara sistem (memiliki nomor tiket unik) sebagai dokumen resmi yang akan ditandatangani dan diverifikasi oleh pimpinan secara fisik/digital di luar aplikasi.
*   **Laporan & Statistik (Opsional):** Sistem dapat menyediakan akses ke dasbor laporan rekapitulasi yang menampilkan grafik dan total jumlah layanan Sub Domain, Hosting, dan VPS yang diaktifkan dalam periode waktu tertentu.

---

## 2. Kebutuhan Fungsional Sistem Terpusat (General Functional Requirements)

Selain fitur berbasis peran, sistem secara keseluruhan harus memiliki kemampuan fungsional berikut:

*   **FR-2.1 Pembuatan Nomor Tiket Otomatis:** Sistem harus menghasilkan nomor tiket unik (format: `TIK-YYYYMMDD-XXXX`) secara otomatis ketika pengajuan baru dibuat.
*   **FR-2.2 Audit Trail Transaksi:** Sistem wajib mencatat entitas pembuat (`id_creator`) dan pengubah (`id_updater`) di setiap tabel, serta mencatat log perubahan status secara berurutan.
*   **FR-2.3 Integrasi Single Sign-On (SSO):** Sistem harus dapat memetakan atribut autentikasi pengguna SSO Unila secara langsung ke dalam peran lokal (`akun.pemetaan_peran_sso`).
*   **FR-2.4 Dual Output Generation:** Sistem harus mendukung *engine* pembentukan PDF (DomPDF) dan peramban antarmuka untuk mencetak dua mode form administratif secara andal.

---

## 3. Kebutuhan Non-Fungsional (Non-Functional Requirements)

Kebutuhan non-fungsional mendefinisikan kriteria yang digunakan untuk menilai operasi sistem, bukan perilakunya (bagaimana sistem melakukannya, bukan apa yang dilakukannya).

### 3.1. Keamanan (Security)
*   **NFR-3.1.1 Enkripsi Kata Sandi:** Semua kata sandi (`kata_sandi`) wajib disimpan dalam format yang di-hash dengan standar algoritma keamanan tinggi bawaan Laravel (seperti Bcrypt/Argon2).
*   **NFR-3.1.2 Pemisahan Skema Data (Data Isolation):** Database PostgreSQL harus menggunakan beberapa skema berbeda (`akun`, `referensi`, `transaksi`, `audit`) untuk meningkatkan pengamanan akses logis dan manajemen objek database.
*   **NFR-3.1.3 Penghapusan Aman (Soft Deletion):** Sistem harus mengimplementasikan metode **Soft Deletes** (`delete_at`) pada data kritikal sehingga data yang dihapus tidak dihilangkan permanen dari basis data fisik demi keperluan forensik.

### 3.2. Kinerja dan Skalabilitas (Performance & Scalability)
*   **NFR-3.2.1 Integritas Terdistribusi:** Sistem harus menggunakan **UUID** (`gen_random_uuid()`) sebagai Primary Key pada seluruh tabel untuk memastikan integritas identitas dan skalabilitas jika arsitektur sistem berkembang menjadi *distributed systems*.
*   **NFR-3.2.2 Caching Cerdas:** Sistem harus memanfaatkan fitur *cache* tingkat aplikasi (melalui file/Redis/Memcached via Laravel) untuk mempercepat pengambilan data referensi statis (seperti jenis layanan, status pengajuan, dll).

### 3.3. Lingkungan dan Perawatan (Environment & Maintainability)
*   **NFR-3.3.1 Kontainerisasi (Containerization):** Seluruh lingkungan eksekusi sistem wajib dijalankan di atas wadah **Docker** (`docker-compose`) untuk menjamin konsistensi yang identik antara lingkungan lokal (development) dan produksi (production).
*   **NFR-3.3.2 Standardisasi Teknologi:** Sistem harus dikembangkan menggunakan framework web **Laravel 12.x** dengan standar PSR (PHP Standard Recommendation) di atas lingkungan **PHP 8.2+**.
*   **NFR-3.3.3 Custom Timestamps Database:** Entitas database disyaratkan untuk tidak menggunakan default Laravel timestamps, melainkan memakai konvensi korporat lokal yaitu penamaan kolom `create_at` dan `last_update`.

### 3.4. Keandalan dan Ketersediaan (Reliability & Availability)
*   **NFR-3.4.1 Validasi Ketat Tingkat DB:** Struktur database menggunakan constraint ketat (contoh: `TIMESTAMP DEFAULT CURRENT_TIMESTAMP`, `NULLABLE` field yang jelas) untuk mencegah anomali atau korupsi data dari sisi layer basis data.
*   **NFR-3.4.2 Penyimpanan Data Terstruktur:** Komponen formulir yang bersifat dinamis (seperti spesifikasi teknis VPS, detail naratif) akan disimpan secara terstruktur dengan format JSON pada field `keterangan_keperluan`.

### 3.5. Usability (Kemudahan Penggunaan)
*   **NFR-3.5.1 Antarmuka Intuitif & Responsif:** Tampilan depan (User Interface) harus dibangun secara *mobile-first*, adaptif dan *clean* dengan menggunakan utility-first CSS framework **TailwindCSS** beserta set icon standar dari **Heroicons**.
*   **NFR-3.5.2 Nomenklatur Lokal (Bahasa Indonesia):** Sistem harus menggunakan penamaan kolom, tabel, dan variabel bisnis (contoh: `nm_lmbg`, `kata_sandi`, `no_tiket`) menggunakan bahasa lokal (Bahasa Indonesia) sesuai kebiasaan UPT TIK Universitas Lampung, untuk mempercepat pemahaman lintas pengembang lokal.
