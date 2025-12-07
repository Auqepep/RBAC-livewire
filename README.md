# 🔐 RBAC OAuth Server

**Role-Based Access Control (RBAC) System dengan OAuth 2.0 Authorization Server**

Sistem manajemen akses berbasis grup dengan OAuth 2.0 untuk integrasi third-party applications.

---

## 📋 Daftar Isi

1. [Fitur Utama](#-fitur-utama)
2. [Tech Stack](#-tech-stack)
3. [Setup Awal](#-setup-awal)
4. [Cara Kerja Web](#-cara-kerja-web)
5. [Setup Redis](#-setup-redis)
6. [OAuth Configuration](#-oauth-configuration)
7. [User Roles](#-user-roles)
8. [Development](#-development)
9. [Deployment](#-deployment)
10. [Dokumentasi Tambahan](#-dokumentasi-tambahan)

---

## ✨ Fitur Utama

-   ✅ **Group-Based RBAC** - Hierarki grup, role, dan permission
-   ✅ **OAuth 2.0 Server** - Authorization Code Flow dengan PKCE
-   ✅ **Redis Caching** - Performance optimization untuk permission checks
-   ✅ **Multi-language** - Indonesia & English (default: Indonesia)
-   ✅ **Livewire 3** - Reactive UI tanpa JavaScript framework
-   ✅ **Mary UI + DaisyUI** - Beautiful Tailwind components
-   ✅ **Email OTP** - Secure email verification
-   ✅ **OAuth Client Management** - Web UI untuk manage third-party apps

---

## 🛠️ Tech Stack

-   **Backend:** Laravel 12.0
-   **Frontend:** Livewire 3, Tailwind CSS, DaisyUI, Mary UI, Lucide Icons
-   **Database:** MySQL 8.0+
-   **Cache:** Redis 7.0+
-   **OAuth:** Laravel Passport 13.4
-   **Mail:** SMTP (Gmail recommended)

---

## 🚀 Setup Awal

### 1. Requirements

Pastikan sudah terinstal:

-   PHP 8.2+
-   Composer 2.x
-   Node.js 18+ & NPM
-   MySQL 8.0+
-   Redis 7.0+

### 2. Clone Repository

```bash
git clone https://github.com/Auqepep/RBAC-livewire.git
cd RBAC-livewire
```

### 3. Install Dependencies

```bash
# Install PHP dependencies
composer install

# Install JavaScript dependencies (includes Lucide, Mary UI, DaisyUI)
npm install
```

### 4. Environment Configuration

```bash
# Copy .env example
cp .env.example .env

# Generate application key
php artisan key:generate
```

Edit `.env`:

```env
# Application
APP_NAME=RBAC
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_LOCALE=id
APP_FALLBACK_LOCALE=en

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rbac_db
DB_USERNAME=root
DB_PASSWORD=

# Redis Cache
CACHE_STORE=redis
CACHE_PREFIX=rbac_
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Session & Queue
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

# Mail (Gmail example)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 5. Database Setup

```bash
# Create database
mysql -u root -p -e "CREATE DATABASE rbac_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Run migrations
php artisan migrate

# Seed initial data (admin, roles, permissions, groups)
php artisan db:seed
```

**Default Admin:**

-   Email: `admin@rbac.com`
-   Password: `password`

### 6. Laravel Passport (OAuth)

```bash
# Install Passport
php artisan passport:install

# Create default OAuth client (optional - untuk testing)
php artisan db:seed --class=OAuthClientSeeder
```

### 7. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 8. Run Application

```bash
# Laravel server
php artisan serve

# Vite dev server (untuk hot reload)
npm run dev
```

Akses: http://localhost:8000

---

## 🏗️ Cara Kerja Web

### 1. RBAC Structure

```
┌─────────────────────────────────────────┐
│            GROUPS (Grup)                │
│  Contoh: IT Support, Marketing, HR      │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│         ROLES (Peran/Jabatan)           │
│  Contoh: Manager, Staff    │
└──────────────┬──────────────────────────┘
               │
               ▼
┌─────────────────────────────────────────┐
│       PERMISSIONS (Izin Akses)          │
│  Contoh: view-users, edit-groups        │
└─────────────────────────────────────────┘
```

**Cara Kerja:**

1. **User** bergabung ke **Group** (misal: IT Support)
2. User diberi **Role** dalam group tersebut (misal: IT Manager)
3. **Role** memiliki **Permissions** (misal: view-users, manage-groups)
4. User mendapat akses sesuai permissions dari role-nya

**Contoh:**

-   John di grup "IT Support" sebagai "IT Manager" → dapat permission: manage-it-infrastructure
-   Sarah di grup "Marketing" sebagai "Staff" → dapat permission: view-campaigns
-   John bisa punya role berbeda di grup lain (flexible!)

📖 **Detail lengkap:** [RBAC_STRUCTURE.md](RBAC_STRUCTURE.md)

### 2. OAuth 2.0 Flow

Website ini bertindak sebagai **OAuth Authorization Server** (bukan client).

```
Third-Party App → Request Authorization → RBAC Server
                                              ↓
                                    User Login & Approve
                                              ↓
                    Authorization Code → Third-Party App
                                              ↓
                    Exchange Code for Access Token
                                              ↓
                    Use Token to Access User Data
```

**Langkah-langkah:**

1. Admin buat OAuth Client di `/admin/oauth-clients`
2. Third-party dapat Client ID & Secret
3. Third-party redirect user ke `/oauth/authorize`
4. User approve akses
5. Third-party dapat authorization code
6. Exchange code untuk access token
7. Gunakan token untuk API calls

🔐 **FAQ OAuth:** [OAUTH_FAQ_INDO.md](OAUTH_FAQ_INDO.md)

### 3. Redis Caching

**Tanpa Redis:**

```
User check permission → 4+ database queries
```

**Dengan Redis:**

```
User check permission → 1 Redis lookup (super fast!)
                     ↓ (jika belum ada)
                Database query → Store to Redis
```

**Yang di-cache:**

-   User permissions (1 jam TTL)
-   User groups & roles (1 jam TTL)
-   Group members (1 jam TTL)
-   OAuth scopes (permanent)

**Auto-invalidation:**
Cache otomatis dihapus saat:

-   User role berubah
-   Permission diupdate
-   Member ditambah/dihapus dari grup

---

## 🔧 Setup Redis

### Opsi 1: Docker (Recommended - Termudah!)

```bash
# Start Redis container
docker run -d --name rbac-redis -p 6379:6379 redis:latest

# Verify
docker ps | grep rbac-redis
```

### Opsi 2: Install Redis Natively

**Windows:**

```bash
# Download dari: https://github.com/microsoftarchive/redis/releases
# Atau gunakan Memurai: https://www.memurai.com/
```

**Linux (Ubuntu/Debian):**

```bash
sudo apt-get update
sudo apt-get install redis-server
sudo systemctl start redis
sudo systemctl enable redis
```

**macOS:**

```bash
brew install redis
brew services start redis
```

### Verify Redis Running

```bash
redis-cli ping
# Output: PONG ✅
```

### Laravel Configuration

Sudah ter-configure di `.env`:

```env
CACHE_STORE=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### Clear Cache (jika perlu)

```bash
php artisan cache:clear
php artisan config:clear
```

---

## 🔐 OAuth Configuration

### 1. Create OAuth Client (Admin Only)

1. Login sebagai admin
2. Buka `/admin/oauth-clients`
3. Klik **"Buat Client Baru"**
4. Isi form:
    - **Nama Aplikasi:** Nama third-party app
    - **Redirect URI:** URL callback mereka
    - Bisa tambah multiple URIs (dev, staging, production)
5. Klik **"Buat Client"**
6. **PENTING:** Copy Client ID & Secret (hanya muncul sekali!)
7. Kirim credentials ke third-party developer

### 2. Third-Party Implementation

Third-party app harus:

**Step 1:** Simpan credentials

```env
OAUTH_CLIENT_ID=3c1c109b-8ab4-4874-8819-330dfa13e02c
OAUTH_CLIENT_SECRET=abc123xyz...
OAUTH_REDIRECT_URI=https://myapp.com/auth/callback
```

**Step 2:** Redirect user untuk authorization

```javascript
const authUrl =
    `${OAUTH_SERVER}/oauth/authorize?` +
    `client_id=${CLIENT_ID}&` +
    `redirect_uri=${encodeURIComponent(REDIRECT_URI)}&` +
    `response_type=code&` +
    `scope=read-user read-groups&` +
    `state=${randomState}`;

window.location.href = authUrl;
```

**Step 3:** Handle callback & exchange code

```javascript
// Callback route: /auth/callback?code=xxx&state=xxx
const response = await fetch(`${OAUTH_SERVER}/oauth/token`, {
    method: "POST",
    body: JSON.stringify({
        grant_type: "authorization_code",
        client_id: CLIENT_ID,
        client_secret: CLIENT_SECRET,
        redirect_uri: REDIRECT_URI,
        code: code,
    }),
});

const { access_token } = await response.json();
```

**Step 4:** Use access token

```javascript
const userInfo = await fetch(`${OAUTH_SERVER}/api/user`, {
    headers: { Authorization: `Bearer ${access_token}` },
});
```

### 3. Available OAuth Scopes

-   `read-user` - Baca informasi user (name, email)
-   `read-groups` - Baca grup membership user
-   `read-permissions` - Baca permissions user
-   `*` - All scopes (wildcard)

### 4. Multiple Redirect URIs

**1 Client ID bisa punya BANYAK redirect URI!**

Contoh:

```
Client ID: abc-123-xyz

Redirect URIs:
✓ https://myapp.com/callback          (Production)
✓ https://staging.myapp.com/callback  (Staging)
✓ http://localhost:3000/callback      (Development)
```

Keuntungan:

-   Semua environment pakai 1 client
-   Flexible deployment
-   Easy management

📖 **FAQ lengkap:** [OAUTH_FAQ_INDO.md](OAUTH_FAQ_INDO.md)

---

## 👥 User Roles

### System Roles

| Role            | Level | Deskripsi              |
| --------------- | ----- | ---------------------- |
| **Super Admin** | 100   | Full system access     |
| **Admin**       | 90    | System-wide management |
| **Manager**     | 70    | Group management       |
| **Staff**       | 50    | Standard access        |

### Default Groups

1. **System Administrators** - System-wide control
2. **IT Support** - Technical operations
3. **Marketing** - Marketing campaigns
4. **Human Resources** - Employee management

### Permission Examples

-   `view-users` - Lihat daftar user
-   `manage-users` - Create, edit, delete users
-   `view-groups` - Lihat daftar grup
-   `manage-groups` - Create, edit grup
-   `assign-roles` - Assign roles ke user
-   `manage-permissions` - Edit permission mappings

---

## 💻 Development

### Running Development Server

```bash
# Terminal 1: Laravel
php artisan serve

# Terminal 2: Vite (hot reload)
npm run dev

# Terminal 3: Redis (jika belum running)
redis-server
# Atau Docker:
docker start rbac-redis

# Terminal 4: Queue worker (optional - untuk email)
php artisan queue:work
```

### Useful Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Refresh database (WARNING: deletes all data!)
php artisan migrate:fresh --seed

# Create new OAuth client
php artisan db:seed --class=OAuthClientSeeder

# List OAuth clients
php artisan passport:client

# Test email
php artisan email:test your-email@example.com
```

### Directory Structure

```
app/
├── Console/Commands/     # Artisan commands
├── Http/
│   ├── Controllers/     # Controllers
│   │   └── Admin/       # Admin controllers
│   └── Middleware/      # Custom middleware
├── Livewire/           # Livewire components
│   ├── Admin/          # Admin components
│   ├── Auth/           # Authentication
│   └── User/           # User components
├── Models/             # Eloquent models
└── Services/           # Business logic services

resources/
├── css/
│   └── app.css        # Tailwind CSS
├── js/
│   └── app.js         # JavaScript entry
└── views/
    ├── admin/         # Admin views
    ├── auth/          # Auth views
    ├── components/    # Blade components
    └── livewire/      # Livewire views

routes/
├── web.php           # Web routes
├── auth.php          # Authentication routes
└── console.php       # Artisan commands
```

---

## 🚀 Deployment

### Railway.app (Recommended - Gratis!)

#### 1. Prerequisites

-   GitHub account
-   Code di-push ke GitHub

#### 2. Deploy Steps

**a. Create Project**

1. Buka https://railway.app
2. Sign up dengan GitHub
3. Klik "New Project" → "Deploy from GitHub repo"
4. Pilih repository `RBAC-livewire`

**b. Add MySQL Database**

1. Di Railway project → "New" → "Database" → "Add MySQL"
2. Railway auto-set `DATABASE_URL`

**c. Add Redis**

1. "New" → "Database" → "Add Redis"
2. Railway auto-set `REDIS_URL`

**d. Configure Environment**
Di service → "Variables" tab, tambahkan:

```env
APP_NAME=RBAC
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app.up.railway.app
APP_KEY=base64:xxx... # Generate dengan: php artisan key:generate --show

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
```

**e. Build & Start Commands**

Settings → Build Command:

```bash
composer install --optimize-autoloader --no-dev && npm install && npm run build && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

Settings → Start Command:

```bash
php artisan migrate --force && php artisan passport:install --force && php artisan serve --host=0.0.0.0 --port=$PORT
```

**f. Deploy!**
Railway akan auto-deploy setiap push ke GitHub.

**g. Seed Initial Data**

```bash
# Di Railway Console
php artisan db:seed --class=DatabaseSeeder
php artisan db:seed --class=OAuthClientSeeder
```

📖 **Detail lengkap:** [DEPLOYMENT_GUIDE.md](DEPLOYMENT_GUIDE.md)

### Alternative: VPS/Shared Hosting

1. Upload files via FTP/Git
2. Install Composer dependencies
3. Setup `.env` dengan database & Redis credentials
4. Run migrations & seeders
5. Point web server ke `public/` folder
6. Setup cron job untuk scheduler (optional):
    ```
    * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
    ```

---

## 📚 Dokumentasi Tambahan

### Reference Docs

-   📘 [**RBAC_STRUCTURE.md**](RBAC_STRUCTURE.md) - Detail struktur RBAC, database schema
-   🔐 [**OAUTH_FAQ_INDO.md**](OAUTH_FAQ_INDO.md) - FAQ OAuth dalam Bahasa Indonesia
-   🚀 [**DEPLOYMENT_GUIDE.md**](DEPLOYMENT_GUIDE.md) - Panduan deployment production

### API Documentation

**User Info API:**

```
GET /api/user
Authorization: Bearer {access_token}

Response:
{
  "id": 1,
  "name": "John Doe",
  "email": "john@example.com",
  "groups": [...],
  "permissions": [...]
}
```

### Troubleshooting

**Problem: Redis connection failed**

```bash
# Check Redis running
redis-cli ping

# Restart Redis
docker restart rbac-redis
# Atau: sudo systemctl restart redis
```

**Problem: OAuth client secret tidak muncul**

-   Secret hanya muncul sekali saat create
-   Solusi: Regenerate secret atau buat client baru

**Problem: Permission denied**

-   Clear cache: `php artisan cache:clear`
-   Check user role assignment di database

**Problem: Email tidak terkirim**

-   Pastikan SMTP credentials benar
-   Cek `storage/logs/laravel.log`
-   Test: `php artisan email:test your-email@example.com`

---

## 🤝 Contributing

Contributions welcome! Please:

1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Pull Request

---

## 📄 License

This project is open-sourced under the [MIT license](https://opensource.org/licenses/MIT).

---

## 🙏 Credits

-   **Framework:** [Laravel](https://laravel.com)
-   **UI:** [Livewire](https://livewire.laravel.com), [Tailwind CSS](https://tailwindcss.com)
-   **Components:** [Mary UI](https://mary-ui.com), [DaisyUI](https://daisyui.com)
-   **Icons:** [Lucide](https://lucide.dev)
-   **OAuth:** [Laravel Passport](https://laravel.com/docs/passport)
-   **Cache:** [Redis](https://redis.io)

---

**Made with ❤️ by Auqepep**
