# 🏥 eLabora - Sistem Informasi Laboratorium Medis

Proyek monorepo terpadu untuk platform **eLabora**, menggabungkan layanan backend API dan antarmuka web (frontend).

---

## 📁 Struktur Monorepo

```
Tubes Devops/
├── elabora-api/                        # Backend REST API (Node.js & Express.js)
│   ├── src/                            # Kode sumber API (controllers, models, routes, dll.)
│   ├── tests/                          # Automated testing
│   ├── .env.example                    # Template konfigurasi environment backend
│   └── package.json                    # Dependensi backend
│
├── Project-Web---eLabora/              # Frontend / Web Application
│   └── eLabora-dev-laravel/
│       └── eLabora_app/                # Aplikasi Laravel (PHP & Vite / Tailwind CSS)
│           ├── app/                    # Controller, Models, Providers Laravel
│           ├── resources/views/        # Blade templates / tampilan UI
│           ├── routes/                 # Routing web & API proxy
│           ├── .env.example            # Template konfigurasi environment frontend
│           ├── composer.json           # Dependensi PHP/Composer
│           └── package.json            # Dependensi asset Vite/Tailwind
│
├── .gitignore                          # Konfigurasi ignore terpadu untuk Git
└── README.md                           # Dokumentasi utama proyek
```

---

## 🚀 Panduan Memulai Cepat

### 1. Prasyarat Sistem
- **Node.js**: v18.x atau lebih baru
- **npm**: v9.x atau lebih baru
- **PHP**: v8.2 atau lebih baru
- **Composer**: v2.x
- **MySQL**: v8.0 atau MariaDB 10.4+

---

### 2. Menjalankan Backend API (`elabora-api`)

1. Masuk ke folder API:
   ```bash
   cd elabora-api
   ```
2. Pasang dependensi:
   ```bash
   npm install
   ```
3. Konfigurasi file `.env`:
   ```bash
   cp .env.example .env
   ```
   *Sesuaikan konfigurasi database MySQL, kredensial JWT, dan port (default: 3000 atau 5000).*
4. Jalankan server dalam mode development:
   ```bash
   npm run dev
   ```

---

### 3. Menjalankan Web Application (`Project-Web---eLabora`)

1. Masuk ke direktori aplikasi Laravel:
   ```bash
   cd Project-Web---eLabora/eLabora-dev-laravel/eLabora_app
   ```
2. Pasang dependensi PHP dan Node:
   ```bash
   composer install
   npm install
   ```
3. Konfigurasi file `.env`:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Pastikan URL API di `.env` mengarah ke instance `elabora-api`.*
4. Jalankan kompilasi asset (Vite) dan server Laravel:
   ```bash
   # Terminal 1: Asset compiler
   npm run dev

   # Terminal 2: Laravel development server
   php artisan serve
   ```

---

## 🛠️ DevOps & Deployment
- File alur kerja GitHub Actions dapat dikonfigurasi di root `.github/workflows/` untuk pipeline CI/CD otomatis (pengujian unit, linting, build docker, dan deployment otomatis).
