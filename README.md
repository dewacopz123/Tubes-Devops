# 🏥 eLabora - Sistem Informasi Laboratorium Medis

Proyek monorepo terpadu untuk platform **eLabora**, menggabungkan layanan backend REST API, antarmuka web (frontend), dan database MySQL.

---

## 📁 Struktur Monorepo

```
Tubes Devops/
├── backend/                            # Backend REST API (Node.js & Express.js)
│   ├── src/                            # Kode sumber API (controllers, models, routes, middleware)
│   ├── tests/                          # Automated testing (Vitest)
│   ├── .env.example                    # Template konfigurasi environment backend
│   └── package.json                    # Dependensi backend
│
├── frontend/                           # Web Application (Laravel 11, Vite & Tailwind CSS)
│   ├── app/                            # Controllers, Models, Services Laravel
│   ├── resources/views/                # Blade templates / tampilan antarmuka (UI)
│   ├── routes/                         # Routing web Laravel
│   ├── .env.example                    # Template konfigurasi environment frontend
│   ├── composer.json                   # Dependensi PHP/Composer
│   └── package.json                    # Dependensi frontend (Vite & Tailwind)
│
├── database/                           # Skema & Seed Data Database
│   └── database-elabora.sql            # Dump MySQL (DDL + Sample Data lengkap)
│
├── scripts/                            # Skrip Utilitas Proyek
│   └── import-db.ps1                   # Skrip otomatis import database ke MySQL lokal
│
├── .gitignore                          # Aturan git ignore terpadu untuk monorepo
└── README.md                           # Dokumentasi utama proyek
```

---

## 🚀 Panduan Memulai Cepat

### 1. Prasyarat Sistem
- **MySQL**: v8.0 / MariaDB 10.4+ (mis. via XAMPP atau Laragon)
- **Node.js**: v18.x atau lebih baru
- **PHP**: v8.2 atau lebih baru & **Composer** v2.x

---

### 2. Setup Database (`database/`)

1. Nyalakan service **MySQL** di XAMPP / Laragon.
2. Impor file database menggunakan salah satu cara:
   - **Cara Otomatis (PowerShell)**:
     ```powershell
     .\scripts\import-db.ps1
     ```
   - **Cara phpMyAdmin / MySQL CLI**:
     Buka phpMyAdmin (`http://localhost/phpmyadmin`) lalu import file [`database/database-elabora.sql`](file:///C:/Tubes%20Devops/database/database-elabora.sql), atau jalankan perintah:
     ```bash
     mysql -u root -p < database/database-elabora.sql
     ```
     *(Database `elabora_db` beserta seluruh tabel dan datanya akan dibuat secara otomatis).*

---

### 3. Menjalankan Backend API (`backend/`)

1. Masuk ke direktori `backend`:
   ```bash
   cd backend
   ```
2. Pasang dependensi:
   ```bash
   npm install
   ```
3. Konfigurasi file `.env`:
   File `.env` sudah disediakan secara lokal dengan konfigurasi default:
   ```env
   PORT=3000
   DB_HOST=localhost
   DB_USER=root
   DB_PASSWORD=
   DB_NAME=elabora_db
   DB_SSL=false
   ```
4. Jalankan backend:
   ```bash
   npm run dev
   ```

---

### 4. Menjalankan Web Application (`frontend/`)

1. Buka terminal baru dan masuk ke folder `frontend`:
   ```bash
   cd frontend
   ```
2. Pasang dependensi:
   ```bash
   composer install
   npm install
   ```
3. File `.env` lokal sudah siap dengan URL backend:
   ```env
   EXPRESS_API_URL=http://localhost:3000
   ```
4. Jalankan Vite compiler dan server Laravel:
   ```bash
   # Terminal A: Compiler asset
   npm run dev

   # Terminal B: Server Laravel
   php artisan serve
   ```
