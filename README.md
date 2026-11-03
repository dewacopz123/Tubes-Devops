# 🏥 eLabora - Sistem Informasi Laboratorium Medis

Proyek monorepo terpadu untuk platform **eLabora**, menggabungkan layanan backend REST API dan antarmuka web (frontend).

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
├── .gitignore                          # Aturan git ignore terpadu untuk monorepo
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

### 2. Menjalankan Backend API (`backend/`)

1. Buka terminal dan masuk ke folder `backend`:
   ```bash
   cd backend
   ```
2. Pasang dependensi:
   ```bash
   npm install
   ```
3. Konfigurasi file `.env`:
   ```bash
   cp .env.example .env
   ```
   *Sesuaikan konfigurasi database MySQL, kredensial JWT, dan port API (default: 3000 atau 5000).*
4. Jalankan backend dalam mode development:
   ```bash
   npm run dev
   ```

---

### 3. Menjalankan Web Application (`frontend/`)

1. Buka terminal baru dan masuk ke folder `frontend`:
   ```bash
   cd frontend
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
   *Pastikan variabel `EXPRESS_API_URL` pada `.env` mengarah ke URL service `backend` (misal: `http://localhost:5000`).*
4. Jalankan compiler asset (Vite) dan server Laravel:
   ```bash
   # Terminal 1: Vite asset compiler
   npm run dev

   # Terminal 2: Laravel development server
   php artisan serve
   ```

---

## 🛠️ Praktik DevOps
- **Containerization**: Anda dapat menambahkan `Dockerfile` di dalam folder `backend/` dan `frontend/` serta `docker-compose.yml` di root untuk menjalankan kedua service secara bersamaan.
- **CI/CD**: Pipeline GitHub Actions dapat mendeteksi perubahan pada masing-masing folder (`backend/**` dan `frontend/**`) secara independen untuk testing dan deployment.
