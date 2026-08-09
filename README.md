# Modern Open Source Online Exam Platform

<div dir="rtl" align="center">

### بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم
### اَللَّهُمَّ صَلِّ عَلَى سَيِّدِنَا مُحَمَّدٍ
### اللَّهُمَّ انْصُرْ وَاحْفَظْ وَأَيِّدْ جَاكَا د صَلَوَات فِي الْعَالَمِينَ بِحَقِّ سَيِّدِنَا مُحَمَّدٍ صَلَّى اللهُ عَلَيْهِ وَسَلَّمَ

</div>

---

A high-performance, secure, and accessible open-source online examination platform built with **Laravel 13**, **Vanilla JS**, and **MySQL 8+**.

Designed for multi-school and multi-role operations (Administrators, Teachers, Students), with native support for high-concurrency exam scenarios as well as budget-friendly shared hosting environments.

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
  - Published Exams Management Table on Teacher Dashboard with Edit Modal (title, token, duration, active/inactive toggle status) and Delete actions.
- **Multi-Role & Multi-Tenant Onboarding**: School registration wizard, academic year setup, classrooms, subjects, teacher-subject mapping, and student enrollment.
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

## Demo Accounts for Testing (Password: `password`)

- **Super / School Admin**: `admin@demo.org`
- **Guru / Teacher**: `teacher@demo.org`
- **Siswa / Student**: `student@demo.org` (Demo Exam Token: `EXAM26`)

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
   AWS_VERIFY_SSL=false
   ```

---

## Automated Image Resizing & S3 Media Cleanup

To minimize storage space and save bandwidth:

- **Proportional Image Resizing**: All uploaded images (JPEG, PNG, WebP) are processed by [`MediaUploadService`](/app/Services/MediaUploadService.php) using native PHP GD library to automatically resize to **maximum 1024x1024 pixels** while preserving original aspect ratio and applying **95% high quality optimization**.
- **Automated S3 Storage Purging**: Deleting a question or removing an image during editing automatically purges all associated media files from Linode S3 / Storage to prevent orphaned garbage files.
- **Strict PDF Limit**: PDF document uploads are strictly limited to a **maximum size of 5MB** (5,120 KB).
- **YouTube Video Policy**: Direct video file uploads (MP4, AVI, MOV) are prohibited to save storage and bandwidth. Videos are embedded via responsive YouTube iframes.

---

## Security, Supply Chain Integrity & Email Deliverability

- **Cloudflare Real IP Restoration**: Native middleware [`RestoreCloudflareRealIp`](/app/Http/Middleware/RestoreCloudflareRealIp.php) extracts genuine student client IPs from `CF-Connecting-IP` headers to prevent proxy spoofing.
- **Application-Level Anti-DDoS & Throttle Rate Limiting**:
  - Login Endpoint: Max 5 failed login attempts per minute per IP (`throttle:login`).
  - Student Exam REST API: Max 60 requests per minute (`throttle:api-exam`).
  - School Registration: Max 5 attempts per hour per IP (`throttle:register-school`).
- **Cloudflare Turnstile CAPTCHA Integration**: Native server-side validation ([`TurnstileRule`](/app/Rules/TurnstileRule.php)) to block automated bot submissions on Login, School Registration, and Password Reset forms.
- **HTML Email Delivery (Brevo SMTP Integration)**: Integrated HTML Mailables (`resources/views/emails/`) for email verification and password reset to ensure high inbox deliverability and prevent rSPAM classification by mail relays.
- **HTTP Security Headers**: Global middleware [`SecurityHeaders`](/app/Http/Middleware/SecurityHeaders.php) automatically injects `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, and `X-XSS-Protection`.
- **Zero External CDN Dependencies**: All CSS, JavaScript, icons (FontAwesome 6 Free), and layout utilities are served locally from `public/vendor/`. This prevents potential third-party script injection or CDN outage disruptions during exam sessions.
- **Password Reset & Email Verification**: Integrated password recovery workflow (`/forgot-password` & `/reset-password`) and email verification via secure SMTP delivery.

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
```

---

## Automated Test Suite

Run the full automated test suite (25 unit and feature tests, 80 assertions):
```bash
php artisan test
```

---

## License & Attribution

This open-source platform is released under the [MIT License](LICENSE).

Made with ❤️ by [Achmad An'im](https://github.com/animfahmy) &bull; Inspired by [Pak Wong](https://wongcjdw.com) (Big thanks!). Feel free to adapt, modify, and contribute to benefit schools and educational institutions worldwide!
