# Modern Open Source Online Exam Platform

<div dir="rtl" align="center">

### بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم
### اَللَّهُمَّ صَلِّ عَلَى سَيِّدِنَا مُحَمَّدٍ
### اللَّهُمَّ انْصُرْ وَاحْفَظْ وَأَيِّدْ جَاكَا د صَلَوَات فِي الْعَالَمِينَ بِحَقِّ سَيِّدِنَا مُحَمَّدٍ صَلَّى اللهُ عَلَيْهِ وَسَلَّمَ

</div>

---

A high-performance, secure, and accessible open-source online examination platform built with **Laravel 13**, **Vanilla JS**, and **MySQL 8+**.

Designed for multi-school and multi-role operations (Administrators, Teachers, Students), with native support for high-concurrency exam scenarios as well as budget-friendly shared hosting environments.

### Fitur Pertanyaan:
1. Pilihan Ganda
2. Pilihan Banyak
3. Uraian
4. Benar - Salah
5. Fakta - Opini
6. Mencocokkan
7. Mengurutkan

---

## Key Features

- **Multi-Role & Multi-Tenant Onboarding**: School registration wizard, academic year setup, classrooms, subjects, teacher-subject mapping, and student enrollment.
- **Rich Question Bank**: Supports multiple question types:
  - Single Choice (Pilihan Ganda)
  - Multiple Choice / Complex Select (Pilihan Ganda Kompleks)
  - True / False (Benar / Salah)
  - Essay & Short Answer (Esai & Pembahasan)
- **High-Resilience Exam Engine**:
  - Pure Vanilla JS frontend execution (0 external CDN dependencies for supply-chain security).
  - Background autosave REST API every 15 seconds & on selection change.
  - Live countdown timer with automatic graceful submit upon timeout.
  - Offline resilience & instant restoration upon page refresh.
- **Flexible Deployment Architecture**:
  - High Performance Mode: Laravel Octane + Redis.
  - Web Server Container Mode: OpenLiteSpeed (OLS).
  - Standard / Shared Hosting Mode: cPanel / DirectAdmin (PHP 8.2+ with MySQL, no Octane/Redis required).

---

## Quickstart (Local Development)

### Requirements
- **PHP**: PHP 8.2+ (tested on PHP 8.5.7)
- **Composer**: 2.x+
- **Database**: MySQL 8+ or SQLite (local testing)

### Installation Steps

1. **Clone Repository & Install Dependencies**:
   ```bash
   composer install
   ```

2. **Environment Configuration**:
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```

3. **Database Migration & Seeding**:
   ```bash
   php artisan migrate --seed
   ```

4. **Run Development Server**:
   ```bash
   php artisan serve
   ```
   Open `http://127.0.0.1:8000` in your web browser.

### Akun Demo untuk Login (Password: `password`):
- **Super / School Admin**: `admin@demo.org`
- **Guru / Teacher**: `teacher@demo.org`
- **Siswa / Student**: `student@demo.org` (Token Ujian Demo: `EXAM26`)

---

## Deployment Configurations

### Option 1: High-Performance Production (Laravel Octane + Redis)

For simultaneous examination of thousands of students across multiple classrooms:

1. **Install Octane & Redis Extensions**:
   ```bash
   composer require laravel/octane predis/predis
   php artisan octane:install --server=frankenphp # or swoole / roadrunner
   ```

2. **Update `.env`**:
   ```env
   CACHE_STORE=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   ```

3. **Start Octane Server**:
   ```bash
   php artisan octane:start --port=8000 --workers=auto
   ```

---

### Option 2: OpenLiteSpeed (OLS) Deployment

OpenLiteSpeed provides high event-driven performance with minimal memory consumption on VPS servers:

1. **Document Root**: Point your Virtual Host document root to `/path/to/ujian-online/public`.
2. **Rewrite Rules** (`.htaccess` inside `public/`):
   ```apache
   <IfModule mod_rewrite.c>
       <IfModule mod_negotiation.c>
           Options -MultiViews -Indexes
       </IfModule>

       RewriteEngine On

       # Handle Authorization Header
       RewriteCond %{HTTP:Authorization} .
       RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

       # Redirect Trailing Slashes If Not A Folder...
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteCond %{REQUEST_URI} (.+)/$
       RewriteRule ^ %1 [L,R=301]

       # Send Requests To Front Controller...
       RewriteCond %{REQUEST_FILENAME} !-d
       RewriteCond %{REQUEST_FILENAME} !-f
       RewriteRule ^ index.php [L]
   </IfModule>
   ```
3. Enable `LSAPI` mode in OpenLiteSpeed WebAdmin for fast PHP execution.

---

### Option 3: Shared Hosting Deployment (cPanel / DirectAdmin)

Easily deploy on standard cPanel/DirectAdmin shared web hosting without root access, Redis, or Octane:

1. **Upload Code**: Upload the project directory to your hosting account (e.g. `/home/user/ujian-online`).
2. **Setup Document Root / Symlink**:
   - Option A: Point your domain/subdomain document root directly to `/home/user/ujian-online/public`.
   - Option B: Copy files inside `public/` to `public_html/` and update `index.php` paths:
     ```php
     require __DIR__.'/../ujian-online/vendor/autoload.php';
     $app = require_once __DIR__.'/../ujian-online/bootstrap/app.php';
     ```
3. **Configure `.env` for Shared Hosting**:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-school-domain.org

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cpanel_exam_db
   DB_USERNAME=cpanel_exam_user
   DB_PASSWORD=your_secure_password

   CACHE_STORE=file
   SESSION_DRIVER=file
   QUEUE_CONNECTION=sync
   ```
4. **Run Migrations via Cron Job or SSH**:
   ```bash
   php artisan migrate --force
   ```

---

## Security & Supply Chain Integrity

- **Zero External CDN Dependencies**: All CSS, JavaScript, icons, and layout utilities are compiled locally. This prevents potential third-party script injection or CDN outage disruptions during exam sessions.
- **CSRF & Session Protection**: All REST endpoints enforce Laravel CSRF token verification and multi-role session guards.

---

## License

This open-source platform is released under the [MIT License](LICENSE). Feel free to adapt, modify, and contribute to benefit schools and educational institutions worldwide!eleased under the [MIT License](LICENSE). Feel free to adapt, modify, and contribute to benefit schools and educational institutions worldwide!
