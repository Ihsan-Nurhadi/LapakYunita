# LapakYunita - Panduan Instalasi & Setup Aplikasi

Repository ini berisi aplikasi Laravel **LapakYunita**. Berikut adalah panduan langkah demi langkah untuk menginstal dan menjalankan aplikasi ini di komputer lain/baru.

---

## Prasyarat (Prerequisites)
Sebelum memulai, pastikan komputer baru sudah terinstal software berikut:
1. **PHP (Minimal versi 8.1)**
2. **Composer** (untuk manajemen package PHP)
3. **Node.js & NPM** (untuk compiler frontend Vite)
4. **Git** (untuk clone repository)
5. **Database Engine** (Secara default menggunakan **SQLite**, namun Anda juga bisa menggunakan **MySQL** seperti XAMPP).

---

## Langkah Instalasi

Ikuti langkah-langkah di bawah ini secara berurutan:

### 1. Clone Repository
Clone repository ini dari GitHub ke komputer lokal Anda:
```bash
git clone <URL_REPOSITORY_ANDA>
cd LapakYunita
```

### 2. Install Dependency PHP
Jalankan perintah berikut untuk menginstal package PHP yang dibutuhkan aplikasi:
```bash
composer install
```

### 3. Salin File Environment (`.env`)
Salin file konfigurasi `.env.example` menjadi `.env`:
* **Windows (Command Prompt):**
  ```cmd
  copy .env.example .env
  ```
* **Windows (PowerShell) / Linux / macOS:**
  ```bash
  cp .env.example .env
  ```

### 4. Generate Application Key
Jalankan perintah ini untuk membuat key aplikasi baru:
```bash
php artisan key:generate
```

### 5. Setup Database

Aplikasi ini dikonfigurasi menggunakan **SQLite** secara default agar mudah dijalankan tanpa perlu membuat database di phpMyAdmin.

#### Opsi A: Menggunakan SQLite (Rekomendasi & Paling Mudah)
1. Buat file database SQLite kosong jika belum ada:
   * **Windows (PowerShell):**
     ```powershell
     New-Item -Path database\database.sqlite -ItemType File
     ```
   * **Linux / macOS / Git Bash:**
     ```bash
     touch database/database.sqlite
     ```
2. Pastikan baris database di file `.env` Anda seperti ini:
   ```env
   DB_CONNECTION=sqlite
   ```

#### Opsi B: Menggunakan MySQL (seperti XAMPP / Laragon)
1. Buka phpMyAdmin atau client database Anda, lalu buat database baru dengan nama, misalnya, `lapak_yunita`.
2. Buka file `.env` yang baru saja dibuat, lalu sesuaikan bagian konfigurasinya:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=lapak_yunita
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### 6. Jalankan Migrasi & Seeder Database
Jalankan perintah berikut untuk membuat tabel dan mengisi data awal (seperti data produk, user, dll.):
```bash
php artisan migrate --seed
```

### 7. Install Dependency Frontend (Node.js)
Jalankan perintah ini untuk menginstal package frontend:
```bash
npm install
```

---

## Menjalankan Aplikasi di Komputer Lokal

Untuk menjalankan aplikasi, Anda perlu membuka **dua jendela terminal**:

1. **Terminal 1 (Server PHP Laravel):**
   ```bash
   php artisan serve
   ```
   Aplikasi akan berjalan di `http://127.0.0.1:8000`.

2. **Terminal 2 (Compiler Asset Vite):**
   ```bash
   npm run dev
   ```
   Terminal ini wajib tetap berjalan agar tampilan web (CSS & JS) termuat dengan benar.

Setelah kedua server berjalan, buka browser Anda dan akses alamat:
👉 **[http://127.0.0.1:8000](http://127.0.0.1:8000)**

---

## Troubleshooting (Masalah Umum)

* **Tampilan Berantakan / CSS Tidak Masuk:**
  Pastikan Anda sudah menjalankan perintah `npm install` dan `npm run dev`.
* **Error `Database file does not exist` (jika pakai SQLite):**
  Pastikan file `database/database.sqlite` sudah dibuat di folder `database` sebelum menjalankan `php artisan migrate`.
* **Error `Target class [PosSeeder] does not exist`:**
  Jalankan `composer dump-autoload`, lalu coba jalankan kembali `php artisan migrate --seed`.

