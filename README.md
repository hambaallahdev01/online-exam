# Modern Open Source Online Exam Platform

<p align="center">
  <a href="https://ajenonov2.wongcjdw.com/"><img src="https://img.shields.io/badge/Live_Demo-ajenonov2.wongcjdw.com-00C7B7?style=for-the-badge&logo=google-chrome&logoColor=white" alt="Live Demo" /></a>
  <a href="https://github.com/hambaallahdev01/online-exam/issues"><img src="https://img.shields.io/github/issues/hambaallahdev01/online-exam?style=for-the-badge&color=orange&logo=github" alt="GitHub Issues" /></a>
  <a href="https://github.com/hambaallahdev01/online-exam/stargazers"><img src="https://img.shields.io/github/stars/hambaallahdev01/online-exam?style=for-the-badge&color=yellow&logo=github" alt="GitHub Stars" /></a>
  <a href="https://github.com/hambaallahdev01/online-exam/network/members"><img src="https://img.shields.io/github/forks/hambaallahdev01/online-exam?style=for-the-badge&color=blue&logo=github" alt="GitHub Forks" /></a>
  <a href="LICENSE"><img src="https://img.shields.io/github/license/hambaallahdev01/online-exam?style=for-the-badge&color=green" alt="MIT License" /></a>
  <br>
  <a href="https://laravel.com"><img src="https://img.shields.io/badge/Laravel-13.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 13" /></a>
  <a href="https://php.net"><img src="https://img.shields.io/badge/PHP-8.5%2B-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.5+" /></a>
</p>

---

A high-performance, secure, and accessible open-source online examination platform built with **Laravel 13**, **Vanilla JS**, and **MySQL 8+**.

Designed for multi-school and multi-role operations (Administrators, Teachers, Students), with native support for high-concurrency exam scenarios as well as budget-friendly shared hosting environments.

---

## 🌐 Live Demo

Cobalah platform ini secara langsung tanpa perlu instalasi lokal. Anda dapat mendaftarkan sekolah/akun baru secara mandiri, atau langsung mencoba login menggunakan [Akun Demo & Token Ujian](#-demo-accounts--sample-testing-data) yang telah disediakan:

👉 **[https://ajenonov2.wongcjdw.com/](https://ajenonov2.wongcjdw.com/)**

---

## Supported Question Formats

The platform natively supports 7 comprehensive question formats:

1. **Single Choice** (*Pilihan Ganda*): Standard single correct answer selection with dynamic options count (min 2, unlimited max).
2. **Multiple Choice** (*Pilihan Banyak / Kompleks*): Multiple correct selections (complex choice) with dynamic options count.
3. **Essay** (*Uraian / Short Answer*): Free-form text responses with optional grading rubrics.
4. **True / False** (*Benar - Salah*): Binary true or false evaluations.
5. **Fact / Opinion** (*Fakta - Opini*): Fact or opinion classifications for statements.
6. **Matching** (*Mencocokkan / Menjodohkan*): Interactive pair matching between left and right item sets.
7. **Sequence Sorting** (*Mengurutkan*): Reordering items into the correct sequential order.

---

## Key Features & Platform Capabilities

- **Native Lightweight WYSIWYG Rich Editor & Image Resizer**:
  - Zero external CDN JS dependencies (HTML5 `contenteditable` native implementation).
  - Proportional image auto-resizing (GD library max 1024x1024 95% quality).
  - Interactive floating image resizer popover toolbar (`25%`, `50%`, `75%`, `100%`, `Delete`).
  - Embedded YouTube video player (responsive iframe).
  - PDF document attachments (max 5MB badge).
  - Glassmorphic animated upload loading modal.
- **Dynamic Choice Options Management**:
  - Full WYSIWYG mini editors for every single answer choice option (Option A, B, C, D, E, etc.).
  - Unlimited option addition (`➕ Tambah Opsi Jawaban Baru`) and deletion (`🗑️ Hapus Opsi`) with minimum 2 options enforcement.
  - Automatic A-Z option labeling.
- **Full Question & Exam Lifecycle Management (CRUD & Auto S3 Purge)**:
  - Create, view, edit, and delete questions and question groups.
  - Eloquent `deleting` model event hook to automatically purge all attached S3/Storage media files from HTML/JSON content when a question is deleted or updated.
  - Published Exams Management Table on Teacher Dashboard with Edit Modal (title, token, duration, tenant-local schedule, active/inactive toggle status) and Delete actions.
- **Multi-Role & Multi-Tenant Onboarding**: School registration wizard, IANA timezone selection, academic year setup, classrooms, subjects, teacher-subject mapping, and student enrollment.
- **Tenant-Aware Exam Scheduling**: Teachers enter and view schedules in the school's timezone while all timestamps, availability checks, and countdown deadlines remain UTC internally.
- **High-Resilience Student Exam Engine**:
  - Pure Vanilla JS frontend execution with **zero external CDN dependencies** for supply-chain security.
  - Background REST API autosave every 15 seconds and on option selection.
  - Real-time countdown timer with automatic graceful submission upon timeout.
  - Question palette navigation with visual status indicators (*Answered*, *Unanswered*, *Flagged/Ragu-ragu*, and *Active*).
  - Built-in Dark Mode toggle with persistent local storage preferences.
  - Built-in SVG Favicon featuring `fa-graduation-cap` (`public/favicon.svg`).
- **Flexible Deployment Architecture**:
  - High Performance Mode: Laravel Octane + Redis.
  - Web Server Container Mode: OpenLiteSpeed (OLS).
  - Standard / Shared Hosting Mode: cPanel / DirectAdmin (PHP 8.2+ with MySQL, no Octane/Redis required).

---

## Quickstart (Local Development)

### System Requirements
- **PHP**: PHP 8.2+ (tested on PHP 8.5.7)
- **Composer**: 2.x+
- **Database**: MySQL 8+ or SQLite

### Installation Steps

1. **Clone Repository & Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

2. **Environment Configuration**:
   ```bash
   copy .env.example .env
   php artisan key:generate
   ```
   Set `TENANT_DEFAULT_TIMEZONE` to the IANA timezone used as the default for new schools (for example, `Asia/Jakarta`).

3. **Database Migration & Seeding**:
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Run Development Server**:
   ```bash
   php artisan serve
   ```
   Open `http://127.0.0.1:8000` in your web browser.

---

## 🔑 Demo Accounts & Sample Testing Data

Baik pada server **Live Demo** ([ajenonov2.wongcjdw.com](https://ajenonov2.wongcjdw.com/)) maupun di lingkungan **Local Development** (setelah menjalankan `php artisan migrate:fresh --seed`), Anda dapat langsung mencoba login menggunakan akun demo berikut. 

Semua akun menggunakan default password: **`password`**.

### 👥 Akun Demo Berdasarkan Role (Sekolah: *Demo International Academy*):
- 🏫 **Super / School Admin**: `admin@demo.org` &bull; Password: `password`
  - *Hak Akses*: Manajemen profil sekolah, tahun akademik, kelas, mata pelajaran, serta pendaftaran guru dan siswa.
- 👨‍🏫 **Guru / Teacher**: `teacher@demo.org` &bull; Password: `password`
  - *Hak Akses*: Pembuatan & pengelolaan bank soal (7 format tipe soal), penambahan/penghapusan bank soal, generator token ujian, durasi, dan publish ujian.
- 👨‍🎓 **Siswa / Student**: `student@demo.org` &bull; Password: `password`
  - *Hak Akses*: Mengikuti ujian realtime dengan fitur autosave, countdown timer, indikator ragu-ragu, dan navigasi soal.

---

### 📝 Daftar Token Ujian Aktif (Siap Diuji oleh Siswa):
Siswa (`student@demo.org`) dapat langsung memasukkan salah satu Token Ujian di bawah ini saat memasuki dashboard siswa:

| Nama Ujian | Token Ujian | Mata Pelajaran | Durasi | Keterangan |
| :--- | :---: | :--- | :---: | :--- |
| **Ujian Komprehensif (Semua Mapel)** | `EXAM26` | Multi-Subject | 120 Menit | Kumpulan 7 Tipe Soal Lengkap |
| **UTS Matematika 2026** | `MTK26` | Matematika | 60 Menit | Pilihan Ganda & Isian |
| **UTS Bahasa Indonesia 2026** | `BIN26` | Bahasa Indonesia | 45 Menit | Bacaan & Essay |
| **UTS Ilmu Pengetahuan Alam 2026** | `IPA26` | IPA | 45 Menit | Pilihan Banyak & Matching |
| **UTS Ilmu Pengetahuan Sosial 2026** | `IPS26` | IPS | 45 Menit | Fact / Opinion & Sequence |
| **UTS Bahasa Inggris 2026** | `ENG26` | Bahasa Inggris | 45 Menit | Reading & Matching |
| **UTS Informatika 2026** | `CS26` | Informatika | 60 Menit | Problem Solving & Logic |

---

## Deployment Configurations

### Option 1: High-Performance Production (Laravel Octane + Redis)

For simultaneous examination of thousands of students across multiple classrooms:

1. **Install Octane** (Predis is already included; `phpredis` remains the faster native option):
   ```bash
   composer require laravel/octane
   php artisan octane:install --server=frankenphp # or swoole / roadrunner
   ```

2. **Update `.env`**:
   ```env
   CACHE_STORE=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   EXAM_PAYLOAD_CACHE_STORE=redis
   EXAM_DRAFT_STORE=redis
   REDIS_CLIENT=predis
   ```

3. **Start Octane Server**:
   ```bash
   php artisan octane:start --port=8000 --workers=auto
   ```

---

### Option 2: OpenLiteSpeed (OLS) Deployment

OpenLiteSpeed provides high event-driven performance with minimal memory consumption on VPS servers:

1. **Document Root**: Point your Virtual Host document root to `/path/to/online-exam/public`.
2. **PHP Upload Limits in OLS / CyberPanel**:
   Set `upload_max_filesize = 64M` and `post_max_size = 64M` in CyberPanel PHP Config or `.user.ini` in `public/`.
3. **Rewrite Rules** (`.htaccess` inside `public/`):
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
4. Enable `LSAPI` mode in OpenLiteSpeed WebAdmin for fast PHP execution.
5. **Enable optional Redis acceleration**. Use `phpredis` when its PHP extension is
   installed, or `predis` (included in this project) when it is not:
   ```env
   CACHE_STORE=redis
   SESSION_DRIVER=redis
   EXAM_PAYLOAD_CACHE_STORE=redis
   EXAM_DRAFT_STORE=redis
   REDIS_CLIENT=predis
   REDIS_HOST=127.0.0.1
   REDIS_PORT=6379
   REDIS_DB=0
   REDIS_CACHE_DB=1
   ```
   `EXAM_PAYLOAD_CACHE_STORE` caches the sanitized question package. `EXAM_DRAFT_STORE`
   keeps frequent answer drafts in Redis and checkpoints them to MySQL every 30 seconds;
   the final submission is always written to MySQL. After changing `.env`, run
   `php artisan config:clear` (or rebuild the production config cache).

---

### Option 3: Shared Hosting Deployment (cPanel / DirectAdmin)

Easily deploy on standard cPanel/DirectAdmin shared web hosting without root access, Redis, or Octane:

1. **Upload Code**: Upload the project directory to your hosting account (e.g. `/home/user/online-exam`).
2. **Setup Document Root / Symlink**:
   - Option A: Point your domain/subdomain document root directly to `/home/user/online-exam/public`.
   - Option B: Copy files inside `public/` to `public_html/` and update `index.php` paths:
     ```php
     require __DIR__.'/../online-exam/vendor/autoload.php';
     $app = require_once __DIR__.'/../online-exam/bootstrap/app.php';
     ```
3. **Configure `.env` for Shared Hosting**:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://your-school-domain.org
   TENANT_DEFAULT_TIMEZONE=Asia/Jakarta

   DB_CONNECTION=mysql
   DB_TIMEZONE=+00:00
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cpanel_exam_db
   DB_USERNAME=cpanel_exam_user
   DB_PASSWORD=your_secure_password

   CACHE_STORE=file
   SESSION_DRIVER=file
   QUEUE_CONNECTION=sync
   EXAM_PAYLOAD_CACHE_STORE=file
   EXAM_DRAFT_STORE=database
   ```
   This mode does not connect to Redis. Question packages use Laravel's file cache,
   while every accepted answer autosave is written directly to MySQL.
4. **Run Migrations via Cron Job or SSH**:
   Use cPanel's exact PHP 8 binary path (e.g. `/opt/cpanel/ea-php85/root/usr/bin/php` or `/opt/alt/php85/usr/bin/php`):
   ```bash
   /opt/cpanel/ea-php85/root/usr/bin/php artisan migrate --force
   /opt/cpanel/ea-php85/root/usr/bin/php artisan storage:link
   ```
5. **cPanel CLI Troubleshooting & Composer Setup Guide**:
   On cPanel servers (CloudLinux / EasyApache 4), system CLI `php` often defaults to a legacy PHP version (e.g. PHP 5.6). Follow these step-by-step instructions to run Composer and Artisan commands using PHP 8.5:

   - **Step 1: Locate Exact PHP 8 Binary**:
     Check available PHP 8 binaries on your cPanel server:
     - EasyApache 4 path: `/opt/cpanel/ea-php85/root/usr/bin/php` (or `ea-php83`)
     - CloudLinux Alt-PHP path: `/opt/alt/php85/usr/bin/php` (or `php83`)

   - **Step 2: Download Composer v2 with Suhosin Bypass**:
     If Suhosin security extension is active on cPanel:
     ```bash
     /opt/cpanel/ea-php85/root/usr/bin/php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
     /opt/cpanel/ea-php85/root/usr/bin/php -d suhosin.executor.include.whitelist=phar composer-setup.php
     /opt/cpanel/ea-php85/root/usr/bin/php -r "unlink('composer-setup.php');"
     ```

   - **Step 3: Run Composer Install & Regenerate Autoload Classmap**:
     ```bash
     /opt/cpanel/ea-php85/root/usr/bin/php -d suhosin.executor.include.whitelist=phar composer.phar install --no-dev --optimize-autoloader
     /opt/cpanel/ea-php85/root/usr/bin/php -d suhosin.executor.include.whitelist=phar composer.phar dump-autoload -o
     /opt/cpanel/ea-php85/root/usr/bin/php artisan optimize:clear
     /opt/cpanel/ea-php85/root/usr/bin/php artisan package:discover
     ```
     *Note*: Re-dumping the autoloader classmap with `--optimize-autoloader` using PHP 8.5 resolves any `Class "SortDirection" not found` autoloader mismatches caused by legacy PHP CLI dump operations.

   - **Step 4: (Optional) Create Terminal Alias for Convenience**:
     ```bash
     echo "alias php='/opt/cpanel/ea-php85/root/usr/bin/php'" >> ~/.bashrc
     echo "alias composer='/opt/cpanel/ea-php85/root/usr/bin/php -d suhosin.executor.include.whitelist=phar ~/composer.phar'" >> ~/.bashrc
     source ~/.bashrc
     ```

---

## Object Storage Setup (Linode S3 / AWS S3)

By default, the platform uses local storage (`FILESYSTEM_DISK=public` / `local`) which stores files in `storage/app/public` out-of-the-box.

For cloud deployments or shared storage across multiple web servers, configure Linode Object Storage (E0 Cluster):

1. **Bucket Settings in Linode Cloud Manager**:
   - Region/Cluster: `ap-south-1` (Singapore E0)
   - Bucket Name: `nawaitu`
   - Access Control: Set Bucket Access to **Public Read**.

2. **Configure `.env`**:
   ```env
   FILESYSTEM_DISK=s3

   AWS_ACCESS_KEY_ID=your_linode_access_key
   AWS_SECRET_ACCESS_KEY=your_linode_secret_key
   AWS_DEFAULT_REGION=ap-south-1
   AWS_BUCKET=nawaitu
   AWS_ENDPOINT=https://ap-south-1.linodeobjects.com
   AWS_URL=https://nawaitu.ap-south-1.linodeobjects.com
   AWS_USE_PATH_STYLE_ENDPOINT=false
   AWS_VERIFY_SSL=true
   ```

---

## Automated Image Resizing & S3 Media Cleanup

To minimize storage space and save bandwidth:

- **Proportional Image Resizing**: All uploaded images (JPEG, PNG, WebP) are processed by [`MediaUploadService`](/app/Services/MediaUploadService.php) using native PHP GD library to automatically resize to **maximum 1024x1024 pixels** while preserving original aspect ratio and applying **95% high quality optimization**.
- **Tenant-Scoped S3 Storage Purging**: Media is stored under `questions/{school_id}/{teacher_id}`. Deleting a question or removing an upload can purge only files owned by that teacher.
- **Strict Upload Allowlist**: Only content-verified JPEG, PNG, WebP, and PDF files are accepted, with a **maximum size of 5MB** (5,120 KB). Images are decoded and re-encoded before storage.
- **YouTube Video Policy**: Direct video file uploads (MP4, AVI, MOV) are prohibited to save storage and bandwidth. Videos are embedded via responsive YouTube iframes.

---

## Security, Supply Chain Integrity & Email Deliverability

- **Cloudflare Real IP Restoration**: [`RestoreCloudflareRealIp`](/app/Http/Middleware/RestoreCloudflareRealIp.php) accepts `CF-Connecting-IP` only when `REMOTE_ADDR` matches a CIDR configured in `CLOUDFLARE_TRUSTED_PROXIES`. Populate it from [Cloudflare's published IP ranges](https://www.cloudflare.com/ips/) and keep it current.
- **Role and Tenant Authorization**: Admin, teacher, and student routes are role-gated; question banks, exams, subjects, results, and media operations are scoped to their school and owner.
- **Exam Integrity**: Opening an exam requires a successful token entry, an active UTC-normalized schedule, and the same school. Teachers and students see the schedule in their tenant's IANA timezone; remaining time is calculated server-side and answer IDs are restricted to the selected exam.
- **Stored-XSS Defense**: Rich question HTML is sanitized with an allowlist before storage and again before rendering; executable attributes, unsafe URLs, and untrusted embeds are removed.
- **Application-Level Anti-DDoS & Throttle Rate Limiting**:
  - Login Endpoint: Max 5 failed login attempts per minute per IP (`throttle:login`).
  - Student Exam REST API: Max 60 requests per minute (`throttle:api-exam`).
  - School Registration: Max 5 attempts per hour per IP (`throttle:register-school`).
- **Cloudflare Turnstile CAPTCHA Integration**: Native server-side validation ([`TurnstileRule`](/app/Rules/TurnstileRule.php)) to block automated bot submissions on Login, School Registration, and Password Reset forms.
- **HTML Email Delivery (Brevo SMTP Integration)**: Integrated HTML Mailables (`resources/views/emails/`) for email verification and password reset to ensure high inbox deliverability and prevent rSPAM classification by mail relays.
- **HTTP Security Headers**: Global middleware [`SecurityHeaders`](/app/Http/Middleware/SecurityHeaders.php) automatically injects `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, and `X-XSS-Protection`.
- **Zero External CDN Dependencies**: All CSS, JavaScript, icons (FontAwesome 6 Free), and layout utilities are served locally from `public/vendor/`. This prevents potential third-party script injection or CDN outage disruptions during exam sessions.
- **Password Reset & Email Verification**: Reset tokens enforce expiration and revoke existing sessions; verification links require a valid temporary URL signature.

### SMTP Mail Setup (`.env`)
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=your_brevo_account_email
MAIL_PASSWORD=your_brevo_smtp_key
MAIL_SCHEME=smtp
MAIL_FROM_ADDRESS=your_brevo_verified_sender_email
MAIL_FROM_NAME="${APP_NAME}"

# Cloudflare Turnstile CAPTCHA (Optional)
TURNSTILE_SITE_KEY=your_cloudflare_turnstile_site_key
TURNSTILE_SECRET_KEY=your_cloudflare_turnstile_secret_key

# Public Cloudflare IPv4/IPv6 CIDRs (review https://www.cloudflare.com/ips/ periodically).
CLOUDFLARE_TRUSTED_PROXIES="173.245.48.0/20,103.21.244.0/22,103.22.200.0/22,103.31.4.0/22,141.101.64.0/18,108.162.192.0/18,190.93.240.0/20,188.114.96.0/20,197.234.240.0/22,198.41.128.0/17,162.158.0.0/15,104.16.0.0/13,104.24.0.0/14,172.64.0.0/13,131.0.72.0/22,2400:cb00::/32,2606:4700::/32,2803:f800::/32,2405:b500::/32,2405:8100::/32,2a06:98c0::/29,2c0f:f248::/32"
```

---

## Automated Test Suite

Run the full automated test suite (25 unit and feature tests, 80 assertions):
```bash
php artisan test
```

---

## 🤝 Support, Bug Reports & Feature Requests

Kami menyambut baik masukan, laporan kendala, maupun ide fitur baru untuk pengembangan platform ini:

- 🐛 **Lapor Bug / Kendala**: Jika Anda menemukan bug atau error, silakan buat laporan detail di [GitHub Issues](https://github.com/hambaallahdev01/online-exam/issues).
- 💡 **Request Fitur Baru**: Memiliki ide fitur baru atau peningkatan UX? Ajukan di [GitHub Issues](https://github.com/hambaallahdev01/online-exam/issues).
- 🔀 **Contribute**: Pull request selalu terbuka untuk perbaikan dan pengembangan bersama.

---

## License & Attribution

This open-source platform is released under the [MIT License](LICENSE).

Made with ❤️ by [Hamba Allah](https://github.com/hambaallahdev01) &bull; Inspired by [Pak Wong](https://wongcjdw.com) (Big thanks!). Feel free to adapt, modify, and contribute to benefit schools and educational institutions worldwide!
