---
description: Step-by-step guide to deploy Laravel 11 to Vercel (from zero to production)
---

# 🚀 Deploy Laravel 11 ke Vercel — Panduan Lengkap

> Panduan ini berdasarkan pengalaman nyata fix deployment error.
> Vercel adalah platform serverless — artinya **filesystem read-only** kecuali `/tmp`.
> Ini adalah sumber utama semua error yang biasa terjadi.

---

## 📋 Prasyarat

- Laravel 11.x project yang sudah berjalan di localhost
- Akun GitHub + repository untuk project
- Akun Vercel (gratis) — https://vercel.com
- Database yang bisa diakses dari internet (Supabase, PlanetScale, Neon, dll.)

---

## Step 1: Buat File `api/index.php`

**Kenapa?** Vercel tidak bisa langsung menjalankan `public/index.php`. Vercel memerlukan serverless function di folder `api/`.

Buat file `api/index.php` di root project:

```php
<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// ──────────────────────────────────────────────────────────────
// Vercel Serverless Bootstrap for Laravel 11
// ──────────────────────────────────────────────────────────────
// Vercel filesystem = READ-ONLY kecuali /tmp
// Semua path yang butuh WRITE harus diarahkan ke /tmp
// ──────────────────────────────────────────────────────────────

$storagePath = '/tmp/storage';

// Buat semua direktori storage yang diperlukan
foreach ([
    $storagePath,
    $storagePath . '/framework',
    $storagePath . '/framework/views',
    $storagePath . '/framework/cache',
    $storagePath . '/framework/cache/data',
    $storagePath . '/framework/sessions',
    $storagePath . '/framework/testing',
    $storagePath . '/logs',
] as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
}

// ──────────────────────────────────────────────────────────────
// KUNCI PENTING: Set cache paths ke /tmp SEBELUM Laravel boot
// Laravel internal menggunakan env vars ini untuk menentukan
// dimana menyimpan cache (Application::normalizeCachePath())
// ──────────────────────────────────────────────────────────────
$_ENV['APP_CONFIG_CACHE']   = '/tmp/config.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_ENV['APP_ROUTES_CACHE']   = '/tmp/routes.php';
$_ENV['APP_EVENTS_CACHE']   = '/tmp/events.php';

// Set di semua tempat yang mungkin dibaca Laravel
foreach (['APP_CONFIG_CACHE', 'APP_SERVICES_CACHE', 'APP_PACKAGES_CACHE', 'APP_ROUTES_CACHE', 'APP_EVENTS_CACHE'] as $key) {
    $_SERVER[$key] = $_ENV[$key];
    putenv("{$key}={$_ENV[$key]}");
}

// Register Composer autoloader
require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';

// Arahkan storage path ke /tmp
$app->useStoragePath($storagePath);

// Override config SETELAH config loaded tapi SEBELUM providers boot
// Ini memastikan ViewServiceProvider mendapat path yang benar
$app->beforeBootstrapping(
    \Illuminate\Foundation\Bootstrap\BootProviders::class,
    function ($app) use ($storagePath) {
        config([
            'view.compiled' => $storagePath . '/framework/views',
            'view.paths' => [resource_path('views')],
            'cache.stores.file.path' => $storagePath . '/framework/cache/data',
            'session.files' => $storagePath . '/framework/sessions',
            'logging.channels.single.path' => $storagePath . '/logs/laravel.log',
            'logging.channels.daily.path' => $storagePath . '/logs/laravel.log',
        ]);
    }
);

// Handle request (Laravel 11 native method)
$app->handleRequest(Request::capture());
```

---

## Step 2: Buat File `config/view.php`

**Kenapa?** Laravel 11 tidak selalu generate file ini (menggunakan default internal), tapi Vercel butuh agar `VIEW_COMPILED_PATH` env var bisa di-override.

Buat file `config/view.php`:

```php
<?php

return [
    'paths' => [
        resource_path('views'),
    ],

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views')) ?: storage_path('framework/views')
    ),
];
```

---

## Step 3: Buat File `vercel.json`

**Kenapa?** File ini mengkonfigurasi bagaimana Vercel menjalankan project kamu.

Buat file `vercel.json` di root project:

```json
{
    "version": 2,
    "framework": null,
    "outputDirectory": "public",
    "functions": {
        "api/index.php": {
            "runtime": "vercel-php@0.8.0"
        }
    },
    "routes": [
        {
            "src": "/build/(.*)",
            "dest": "/build/$1"
        },
        {
            "src": "/(css|js|images|storage|favicon\\.ico|robots\\.txt|manifest\\.json|sw\\.js)(.*)",
            "dest": "/$1$2"
        },
        {
            "src": "/(.*)",
            "dest": "/api/index.php"
        }
    ],
    "env": {
        "APP_ENV": "production",
        "APP_DEBUG": "true",
        "APP_URL": "https://NAMA-PROJECT-KAMU.vercel.app",
        "APP_CONFIG_CACHE": "/tmp/config.php",
        "APP_SERVICES_CACHE": "/tmp/services.php",
        "APP_PACKAGES_CACHE": "/tmp/packages.php",
        "APP_ROUTES_CACHE": "/tmp/routes.php",
        "APP_EVENTS_CACHE": "/tmp/events.php",
        "VIEW_COMPILED_PATH": "/tmp/storage/framework/views",
        "LOG_CHANNEL": "stderr",
        "SESSION_DRIVER": "cookie",
        "CACHE_STORE": "array",
        "DB_CONNECTION": "pgsql"
    }
}
```

### ⚠️ Penjelasan Penting untuk `vercel.json`:

| Key               | Penjelasan                                                                                            |
| ----------------- | ----------------------------------------------------------------------------------------------------- |
| `runtime`         | `vercel-php@0.8.0` = PHP 8.4, `vercel-php@0.7.2` = PHP 8.3                                            |
| `outputDirectory` | `public` — agar asset statis (CSS, JS, images) bisa diakses langsung                                  |
| `routes`          | Route pertama & kedua = serve static files; route terakhir = kirim semua request ke Laravel           |
| `SESSION_DRIVER`  | **HARUS `cookie`** — Vercel serverless tidak bisa pakai `file` atau `database` session secara efisien |
| `CACHE_STORE`     | **HARUS `array`** — tidak ada persistent filesystem untuk cache                                       |
| `LOG_CHANNEL`     | **HARUS `stderr`** — agar log muncul di Vercel dashboard                                              |
| `APP_*_CACHE`     | **KRITIS** — mengarahkan semua cache files ke `/tmp` yang writable                                    |

---

## Step 4: Update `AppServiceProvider.php`

Tambahkan HTTPS force untuk production:

```php
public function boot(): void
{
    if ($this->app->environment('production')) {
        \Illuminate\Support\Facades\URL::forceScheme('https');
    }
}
```

---

## Step 5: Pastikan `.gitignore` Benar

File `.gitignore` harus berisi:

```
/vendor
/node_modules
/public/build
/public/hot
/public/storage
/storage/*.key
.env
.env.backup
.env.production
/bootstrap/cache/*.php
```

**Penting:** `/vendor` HARUS di-gitignore — Vercel akan otomatis menjalankan `composer install` saat build.

---

## Step 6: Setup Database (Supabase/Neon/PlanetScale)

1. Buat database di provider pilihan (contoh: Supabase)
2. Dapatkan connection string / credentials
3. Jalankan migrasi dari localhost dulu:
    ```
    php artisan migrate
    ```

---

## Step 7: Setup Vercel

### 7a. Connect Repository

1. Buka https://vercel.com/new
2. Import Git Repository kamu
3. Pilih repository Laravel

### 7b. Set Environment Variables di Vercel Dashboard

**Ini SANGAT PENTING! Variabel di bawah ini HARUS diset di Vercel Dashboard → Settings → Environment Variables:**

| Variable        | Contoh Value                                          |
| --------------- | ----------------------------------------------------- |
| `APP_KEY`       | `base64:GFgA/tmTEyiMECJm4lBcfb6hYgF9cigXmPGChgTKA8o=` |
| `APP_ENV`       | `production`                                          |
| `APP_DEBUG`     | `true` (ubah ke `false` setelah semua berjalan)       |
| `APP_URL`       | `https://nama-project.vercel.app`                     |
| `DB_CONNECTION` | `pgsql`                                               |
| `DB_HOST`       | host dari Supabase/Neon                               |
| `DB_PORT`       | `6543` (Supabase) atau `5432`                         |
| `DB_DATABASE`   | `postgres`                                            |
| `DB_USERNAME`   | username dari provider                                |
| `DB_PASSWORD`   | password dari provider                                |

**⚠️ JANGAN set `APP_KEY` di `vercel.json`** — itu tidak aman karena file tersebut di-commit ke git! Set hanya melalui Vercel Dashboard.

### 7c. Deploy

1. Klik **Deploy**
2. Tunggu build selesai
3. Buka URL yang diberikan Vercel

---

## Step 8: Verifikasi

1. Buka `https://nama-project.vercel.app/`
2. Jika masih error, cek **Vercel Logs** di dashboard:
    - Vercel Dashboard → Project → Deployments → klik deployment → Logs
3. Setelah semua berjalan, ubah `APP_DEBUG` ke `false`

---

## 🔥 Troubleshooting — Error Umum & Solusinya

### Error: "Target class [view] does not exist"

**Penyebab:** `ViewServiceProvider` gagal register karena cache paths mencoba write ke filesystem read-only.
**Solusi:** Pastikan `APP_SERVICES_CACHE`, `APP_PACKAGES_CACHE`, dll. diset ke `/tmp` di `api/index.php` DAN `vercel.json`.

### Error: "failed to open stream: Read-only file system"

**Penyebab:** Laravel mencoba menulis ke `storage/` atau `bootstrap/cache/` yang read-only.
**Solusi:** Pastikan `$app->useStoragePath('/tmp/storage')` dipanggil di `api/index.php`.

### Error: "SQLSTATE connection refused"

**Penyebab:** Database credentials tidak benar atau tidak diset di Vercel.
**Solusi:** Cek Environment Variables di Vercel Dashboard — pastikan `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` sudah benar.

### Error: "419 Page Expired" (CSRF)

**Penyebab:** Session tidak persistent di serverless.
**Solusi:** Gunakan `SESSION_DRIVER=cookie` di `vercel.json` env.

### Error: "No application encryption key"

**Penyebab:** `APP_KEY` tidak diset di Vercel.
**Solusi:** Copy `APP_KEY` dari `.env` lokal ke Vercel Dashboard Environment Variables.

### CSS/JS tidak tampil

**Penyebab:** Asset statis tidak ter-route dengan benar.
**Solusi:**

1. Pastikan `npm run build` dijalankan sebelum push (agar folder `public/build/` ada)
2. Pastikan `public/build/` TIDAK di-gitignore (hapus dari `.gitignore` jika perlu)
3. Cek routing di `vercel.json` sudah benar

### Blank page tanpa error

**Penyebab:** `APP_DEBUG=false` dan ada error tersembunyi.
**Solusi:** Set `APP_DEBUG=true` sementara di Vercel env vars, lalu redeploy.

---

## 📁 Struktur File Penting

```
project-root/
├── api/
│   └── index.php          ← Entry point untuk Vercel (WAJIB)
├── app/
│   └── Providers/
│       └── AppServiceProvider.php  ← HTTPS force
├── bootstrap/
│   └── app.php            ← Laravel bootstrap (jangan diubah)
├── config/
│   └── view.php           ← Harus ada untuk Vercel (WAJIB)
├── public/
│   ├── index.php           ← Entry point untuk localhost
│   └── build/              ← Asset Vite (harus ter-commit)
├── vercel.json             ← Konfigurasi Vercel (WAJIB)
├── composer.json
└── .gitignore
```

---

## ⚡ Quick Checklist Sebelum Deploy

- [ ] File `api/index.php` sudah dibuat dengan benar
- [ ] File `config/view.php` sudah ada
- [ ] File `vercel.json` sudah dikonfigurasi
- [ ] `APP_KEY` sudah diset di Vercel Dashboard (bukan di vercel.json!)
- [ ] Database credentials sudah diset di Vercel Dashboard
- [ ] `npm run build` sudah dijalankan dan `public/build/` ter-commit
- [ ] Migrasi database sudah dijalankan
- [ ] `AppServiceProvider` sudah force HTTPS
- [ ] `SESSION_DRIVER=cookie` dan `CACHE_STORE=array` sudah diset
