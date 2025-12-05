# Lokalisasi Bahasa Indonesia

Website telah diubah ke Bahasa Indonesia.

## Perubahan yang Dilakukan

### 1. Konfigurasi Aplikasi

-   **File**: `.env`
-   **Perubahan**:
    -   `APP_LOCALE=id`
    -   `APP_FALLBACK_LOCALE=id`
    -   `APP_FAKER_LOCALE=id_ID`
    -   Timezone: `APP_TIMEZONE=Asia/Jakarta`

### 2. File Terjemahan

#### PHP Translation Files (`lang/id/`)

-   **auth.php** - Pesan autentikasi (login gagal, throttle, dll)
-   **pagination.php** - Kontrol paginasi
-   **passwords.php** - Pesan reset password (meski sistem pakai OTP)
-   **validation.php** - Pesan validasi lengkap untuk semua field RBAC

#### JSON Translation File (`lang/id.json`)

-   200+ terjemahan UI elemen
-   Mencakup: navigasi, form, pesan sistem, aksi, tanggal/waktu, dll

### 3. File Blade yang Diupdate

#### Gateway Page

-   **File**: `resources/views/livewire/user/group-gateway.blade.php`
-   **Terjemahan**: Semua text menggunakan `{{ __('...') }}`
-   **Bagian**: Redirecting, Access Granted, Access Denied

#### Admin Group Edit

-   **File**: `resources/views/admin/groups/edit.blade.php`
-   **Terjemahan**: Form fields, OAuth integration, member management
-   **Bagian**: Header, form labels, tooltips, pesan error

#### Navigation

-   **File**: `resources/views/livewire/layout/navigation.blade.php`
-   **Terjemahan**: Menu items (Dashboard, Admin, Users, Groups, Logout)

#### Admin Layout

-   **File**: `resources/views/components/admin/layout.blade.php`
-   **Terjemahan**: Admin navigation (Dashboard, Users, Groups, Permissions)

## Cara Menambah Terjemahan Baru

### Untuk UI Elements

Edit file `lang/id.json`:

```json
{
    "English Text": "Teks Indonesia"
}
```

### Untuk Validation/Auth Messages

Edit file di `lang/id/`:

-   `auth.php` - Pesan autentikasi
-   `validation.php` - Pesan validasi
-   `passwords.php` - Pesan password

## Penggunaan di Blade Templates

### Terjemahan Sederhana

```php
{{ __('Dashboard') }}
```

### Terjemahan dengan Parameter

```php
{{ __('Showing :from to :to of :total results', ['from' => 1, 'to' => 10, 'total' => 100]) }}
```

### Di Attribute

```php
<input placeholder="{{ __('Search users...') }}" />
```

## Testing

Setelah perubahan terjemahan:

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

## Status Terjemahan

### ✅ Sudah Diterjemahkan

-   Gateway page (redirect, access granted/denied)
-   Admin group edit form
-   Navigation menu (user & admin)
-   OAuth integration form
-   Common UI elements

### 📋 Mungkin Perlu Diterjemahkan (Opsional)

-   Dashboard pages
-   User management pages
-   Group list pages
-   Permission management pages
-   Email templates
-   Error pages (404, 500, dll)

## Format Date/Time Indonesia

Laravel otomatis menggunakan locale `id_ID` untuk format tanggal:

```php
// Di .env
APP_TIMEZONE=Asia/Jakarta
APP_FAKER_LOCALE=id_ID
```

Contoh output:

-   `now()->translatedFormat('l, d F Y')` → "Selasa, 3 Desember 2024"
-   `now()->diffForHumans()` → "1 jam yang lalu"

## Troubleshooting

### Terjemahan Tidak Muncul

1. Clear cache: `php artisan config:clear && php artisan cache:clear && php artisan view:clear`
2. Pastikan key terjemahan ada di `lang/id.json`
3. Cek typo di helper `__('...')`

### JSON Parse Error

-   Pastikan tidak ada duplikat key
-   Gunakan JSON validator untuk cek syntax
-   Hati-hati dengan escape character di string

## Catatan

-   Semua terjemahan menggunakan Laravel translation system
-   JSON file untuk UI elements yang cepat dan mudah
-   PHP files untuk pesan framework yang lebih kompleks
-   Sistem tetap fallback ke English jika terjemahan tidak ditemukan
