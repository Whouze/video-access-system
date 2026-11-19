# Video Access System

Aplikasi Laravel untuk mengelola video dan permintaan akses. Admin mengelola video, customer, serta menyetujui atau menolak permintaan akses. Customer dapat melihat video dan mengajukan akses.

## Teknologi

- PHP 8.3 atau lebih baru
- Laravel 13
- MySQL atau SQLite
- Node.js dan npm untuk aset frontend

## Persiapan lokal

1. Install dependency PHP dan siapkan file environment:

   ```bash
   composer install
   ```

   Salin `.env.example` menjadi `.env` jika file `.env` belum tersedia. Di PowerShell:

   ```powershell
   Copy-Item .env.example .env
   ```

2. Buat application key:

   ```bash
   php artisan key:generate
   ```

3. Siapkan database. `.env.example` menggunakan SQLite. Buat file database bila belum ada:

   ```powershell
   New-Item database/database.sqlite -ItemType File
   ```

   Jalankan migrasi:

   ```bash
   php artisan migrate
   ```

   Jika memakai MySQL, ubah `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` di `.env`, lalu pastikan service MySQL aktif dan database sudah dibuat.

4. Install dependency frontend dan buat aset:

   ```bash
   npm install
   npm run build
   ```

5. Jalankan aplikasi:

   ```bash
   php artisan serve
   ```

   Buka alamat lokal yang ditampilkan Artisan. Saat mengembangkan frontend, jalankan `npm run dev` di terminal lain.

> Session dan cache disimpan di database pada konfigurasi bawaan. Jalankan migrasi setelah menyiapkan koneksi database.

## Perintah berguna

```bash
php artisan migrate
php artisan test
npm run dev
npm run build
```

## Struktur fitur

- **Admin:** mengelola customer dan video, serta memproses permintaan akses.
- **Customer:** menjelajahi video dan mengajukan permintaan akses.
- **Autentikasi:** pendaftaran, login, reset password, dan pengelolaan password.

