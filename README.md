# PPKLJ — Backend API

Backend REST API untuk sistem **PPKLJ (Pengelolaan Perangkat Jaringan & Lisensi)**, yaitu platform operasional internal kantor IT pemerintahan untuk mencatat dan mengelola pengadaan belanja DIPA (Belanja Modal MAK 53 & Belanja Pemeliharaan MAK 52), inventaris perangkat keras jaringan dan lisensi perangkat lunak, siklus masa berlaku garansi, serta pelacakan alur perbaikan/klaim garansi *Return Merchandise Authorization* (RMA).

---

## 🚀 Tech Stack

- **Framework**: Laravel 13.x (API Mode)
- **Language**: PHP 8.5 / 8.3+
- **Database**: MySQL 8+ / MariaDB
- **Autentikasi**: Laravel Sanctum (Stateful Cookie-based Session untuk SPA Next.js)
- **Testing**: Pest PHP 5 (Test-Driven Architecture)
- **Code Formatter**: Laravel Pint
- **Containerization**: OCI Containerfile (PHP 8.5-FPM & Nginx Alpine)

---

## 📦 Modul & Fitur Utama

1. **Autentikasi Stateful SPA (`/api/login`, `/api/logout`, `/api/user`)**
   - Mendukung autentikasi berbasis cookie session aman (`withCredentials: true`) tanpa penyimpanan token di `localStorage`.
   - Proteksi CSRF otomatis via `/sanctum/csrf-cookie`.
2. **Master Kantor & Penempatan (`/api/offices`)**
   - Manajemen kantor vertikal (Kanwil/UPT daerah) dan kantor pusat.
3. **Pos Anggaran Belanja DIPA (`/api/purchases`)**
   - Pencatatan pos anggaran Belanja Modal (Akun MAK 53) dan Belanja Barang/Pemeliharaan (Akun MAK 52).
4. **Paket Belanja Pengadaan & Pemeliharaan (`/api/asset-purchases`)**
   - 1 baris paket belanja dapat menaungi banyak unit aset fisik sekaligus (misal: 1 paket pengadaan 100 unit Aruba AP).
   - Relasi *Many-to-Many* via tabel pivot `asset_asset_purchase` sehingga riwayat belanja berulang (pengadaan modal lalu perpanjangan garansi tahunan) tidak saling menimpa.
   - Sinkronisasi otomatis tanggal akhir garansi (`end_date`) ke seluruh unit terkait.
5. **Master Aset Induk (`/api/assets`)**
   - Pencatatan aset kategori `jaringan` (hardware) dan `license` (perangkat lunak).
   - Menyimpan serial number fisik, harga perolehan, tanggal garansi, dan histori belanja.
6. **Detail Spesifikasi Jaringan & Tag Fitur (`/api/network-assets`, `/api/features`)**
   - Spesifikasi teknis: IP Address manajemen, hostname, merk (*brand*), seri/model, tipe perangkat, status operasional (`belum_dipasang`, `aktif`, `tidak_aktif`), dan kantor penempatan.
   - Fitur teknis dinamis berbasis tagging *Many-to-Many* (PoE+, VLAN, L3 Routing, OSPF, dll).
7. **Modul RMA & Tracking Pengiriman Aset (`/api/asset-rmas`, `/api/asset-rmas/{id}/tracks`)**
   - Pencatatan tiket klaim garansi/servis perangkat rusak lengkap dengan info PIC, vendor, nomor tiket, dan keluhan.
   - *Timeline progression*: melacak tahapan status barang (mulai rusak di lokasi ➔ dikirim ke pusat ➔ diterima PPKLJ ➔ diserahkan ke vendor ➔ diproses ➔ dikirim kembali ➔ selesai dipasang).
   - Resolusi RMA otomatis:
     - **Diperbaiki**: Serial number tetap sama, perangkat diaktifkan kembali.
     - **Ganti Unit Baru (*Swap*)**: Serial number baru otomatis memperbarui data aset induk di inventaris.

---

## 🛠️ Instalasi & Menjalankan Lokal (Development)

### 1. Prasyarat Sistem
- PHP >= 8.3 dengan ekstensi: `pdo_mysql`, `bcmath`, `mbstring`, `zip`, `xml`, `curl`
- Composer 2.x
- MySQL / MariaDB Server

### 2. Langkah Instalasi

```bash
# 1. Masuk ke direktori backend
cd backend

# 2. Install dependensi PHP
composer install

# 3. Buat file environment dari template
cp .env.example .env

# 4. Generate application key
php artisan key:generate
```

### 3. Konfigurasi Database & Environment (`.env`)

Sesuaikan koneksi database dan domain frontend SPA pada file `.env`:

```ini
APP_NAME=PPKLJ
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# URL Frontend Next.js (bisa multi-origin dipisah koma)
FRONTEND_URL=http://localhost:3000,http://127.0.0.1:3000

# Konfigurasi Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ppklj
DB_USERNAME=root
DB_PASSWORD=your_password

# Konfigurasi Session & Sanctum SPA
SESSION_DRIVER=database
SESSION_DOMAIN=
SANCTUM_STATEFUL_DOMAINS=localhost,localhost:3000,localhost:8000,127.0.0.1,127.0.0.1:3000,127.0.0.1:8000
```

> [!NOTE]
> Pada environment lokal (`localhost`), pastikan `SESSION_DOMAIN=` dikosongkan (bukan string `"null"`) agar cookie session browser tidak ditolak.

### 4. Migrasi Database & Akun Admin

```bash
# Jalankan migrasi database
php artisan migrate

# Buat akun administrator awal (Command Artisan khusus)
php artisan make:user admin "Administrator IT" --password=secret123
```

### 5. Menjalankan Server Development

```bash
php artisan serve
```
Aplikasi API akan aktif di `http://localhost:8000`.

---

## 🐳 Kontainerisasi (Podman / Docker)

Backend dilengkapi dengan `Containerfile` multi-stage dan konfigurasi `nginx/`:

### 1. Build Container Image

```bash
# Build image PHP-FPM runtime
podman build -f Containerfile -t ppklj-backend:latest .

# Build image Nginx Web Server
podman build -f nginx/Containerfile -t ppklj-nginx:latest .
```

### 2. Port Default Kontainer
- **Nginx Web Server**: Port `8080` (merutekan request PHP ke port `9000` PHP-FPM).

---

## 🧪 Testing & Code Quality

Proyek ini menerapkan pengujian otomatis dengan **Pest PHP** dan standarisasi gaya kode dengan **Laravel Pint**:

```bash
# Menjalankan seluruh test suite
php artisan test --compact

# Menjalankan test spesifik
php artisan test --filter=AssetTest

# Format kode otomatis dengan Pint
vendor/bin/pint --format agent
```

---

## 📖 Dokumentasi Lengkap API

Dokumentasi API mendalam beserta contoh request/response JSON, diagram ERD, alur integrasi autentikasi frontend Next.js, dan spesifikasi tipe TypeScript lengkap tersedia di:

📄 **[`.resources/api-docs.md`](.resources/api-docs.md)**
