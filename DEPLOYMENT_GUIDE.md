# 🚀 Deployment Guide - Laravel RBAC with Redis

## Railway.app Deployment (Recommended)

### Prerequisites
- GitHub account
- Your code pushed to GitHub repository

### Step-by-Step Deployment

#### 1. Sign Up & Create Project
1. Go to https://railway.app
2. Sign up with GitHub
3. Click "New Project"
4. Select "Deploy from GitHub repo"
5. Choose your `RBAC-livewire` repository

#### 2. Add MySQL Database
1. In your Railway project, click "New"
2. Select "Database" → "Add MySQL"
3. Railway will automatically set `DATABASE_URL` environment variable

#### 3. Add Redis
1. Click "New" again
2. Select "Database" → "Add Redis"
3. Railway will automatically set `REDIS_URL` environment variable

#### 4. Configure Environment Variables
Go to your Laravel service → "Variables" tab and add:

```env
# App Configuration
APP_NAME=RBAC
APP_ENV=production
APP_KEY=<your-app-key-here>
APP_DEBUG=false
APP_URL=https://your-app.up.railway.app

# Database (Auto-configured by Railway MySQL)
# DB_CONNECTION=mysql
# DB_HOST=<auto-set>
# DB_PORT=<auto-set>
# DB_DATABASE=<auto-set>
# DB_USERNAME=<auto-set>
# DB_PASSWORD=<auto-set>

# Redis Cache (Auto-configured by Railway Redis)
CACHE_STORE=redis
CACHE_PREFIX=rbac_

# Redis (Auto-configured by Railway)
# REDIS_HOST=<auto-set>
# REDIS_PORT=<auto-set>
# REDIS_PASSWORD=<auto-set>

# Session
SESSION_DRIVER=redis
SESSION_LIFETIME=86400

# Queue
QUEUE_CONNECTION=redis

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

#### 5. Generate APP_KEY
```bash
# Run locally to generate key
php artisan key:generate --show
# Copy the output and paste into Railway APP_KEY variable
```

#### 6. Add Build & Start Commands

In Railway settings for your Laravel service:

**Build Command:**
```bash
composer install --optimize-autoloader --no-dev && php artisan config:cache && php artisan route:cache && php artisan view:cache
```

**Start Command:**
```bash
php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
```

Or create `Procfile` in your project root:
```
web: php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT
```

#### 7. Deploy!
Railway will automatically deploy when you push to your main branch.

---

## Alternative: Render.com Deployment

### Setup Steps

1. **Create Account:** https://render.com
2. **Create Web Service:**
   - Connect GitHub repository
   - Select "Web Service"
   - Name: `rbac-app`
   - Build Command: `composer install --optimize-autoloader --no-dev`
   - Start Command: `php artisan serve --host=0.0.0.0 --port=$PORT`

3. **Add MySQL:**
   - Create → Database → MySQL
   - Copy connection details

4. **Add Redis:**
   - Create → Redis
   - Copy REDIS_URL

5. **Environment Variables:**
   - Go to your web service → Environment
   - Add all variables from above

---

## Alternative: DigitalOcean App Platform

### Setup Steps

1. **Create App:**
   - Go to https://cloud.digitalocean.com/apps
   - "Create App" → Connect GitHub

2. **Add Managed Database:**
   - Choose "MySQL" ($15/month or Droplet)
   - Note connection details

3. **Add Managed Redis:**
   - Choose "Redis" ($15/month)
   - Or install Redis on Droplet (cheaper)

4. **Configure Build:**
   ```yaml
   # .do/app.yaml
   name: rbac-app
   services:
   - name: web
     build_command: composer install --optimize-autoloader --no-dev
     run_command: php artisan serve --host=0.0.0.0 --port=8080
     http_port: 8080
   databases:
   - name: mysql
     engine: MYSQL
   - name: redis
     engine: REDIS
   ```

---

## Budget-Friendly Option: Upstash + PlanetScale

### For Serverless/Free Tier:

1. **PlanetScale (MySQL):**
   - Free tier: 5GB storage, 1 billion row reads/month
   - https://planetscale.com

2. **Upstash (Redis):**
   - Free tier: 10k commands/day
   - https://upstash.com

3. **Deploy Laravel on:**
   - Vercel (for API)
   - Or Railway (free $5/month credit)

### Setup:
```env
# PlanetScale MySQL
DATABASE_URL=mysql://user:pass@host/db?sslaccept=strict

# Upstash Redis
REDIS_URL=rediss://default:password@host:port
```

---

## 🔒 Production Checklist

Before going live:

### Security
- [ ] Set `APP_DEBUG=false`
- [ ] Set `APP_ENV=production`
- [ ] Generate strong `APP_KEY`
- [ ] Set Redis password: `REDIS_PASSWORD=strong-password`
- [ ] Use HTTPS (Railway provides this automatically)
- [ ] Enable CSRF protection
- [ ] Set secure session cookie settings

### Performance
- [ ] Enable Redis caching: `CACHE_STORE=redis`
- [ ] Use Redis for sessions: `SESSION_DRIVER=redis`
- [ ] Use Redis for queues: `QUEUE_CONNECTION=redis`
- [ ] Cache config: `php artisan config:cache`
- [ ] Cache routes: `php artisan route:cache`
- [ ] Cache views: `php artisan view:cache`

### Database
- [ ] Run migrations: `php artisan migrate --force`
- [ ] Seed initial data: `php artisan db:seed --force`
- [ ] Backup strategy in place

### Environment Variables
```env
# Production Settings
APP_ENV=production
APP_DEBUG=false
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
LOG_CHANNEL=stack
LOG_LEVEL=error
```

---

## 📊 Cost Comparison

| Platform | MySQL | Redis | App Hosting | Total/Month |
|----------|-------|-------|-------------|-------------|
| **Railway** | ✅ Included | ✅ Included | $5 credit free | **$0-5** |
| **Render** | ✅ Free | ✅ Free | $7 | **$7** |
| **Heroku** | $5 ClearDB | $15 Redis | $7 | **$27** |
| **DigitalOcean** | $15 | $15 | $5 | **$35** |
| **Upstash+PlanetScale** | ✅ Free | ✅ Free | $0-5 | **$0-5** |

---

## 🆘 Troubleshooting

### Redis Connection Failed
```bash
# Check REDIS_URL format
echo $REDIS_URL
# Should be: redis://default:password@host:port

# Test connection
php artisan tinker
>>> \Illuminate\Support\Facades\Redis::connection()->ping()
```

### Database Migration Failed
```bash
# Check database connection
php artisan migrate --force --verbose

# If SSL error, add to .env:
DB_SSLMODE=require
```

### APP_KEY Not Set
```bash
# Generate locally
php artisan key:generate --show
# Copy to Railway environment variables
```

---

## 🎉 Success!

Your app is live with:
- ✅ Redis caching for 10-20x faster permission checks
- ✅ MySQL database for data persistence
- ✅ Auto-scaling and managed infrastructure
- ✅ HTTPS enabled
- ✅ Auto-deploy on git push

**Access your app:**
- Railway: `https://your-app.up.railway.app`
- Render: `https://your-app.onrender.com`

---

## 📚 Additional Resources

- [Railway Docs](https://docs.railway.app)
- [Render Docs](https://render.com/docs)
- [Laravel Deployment Docs](https://laravel.com/docs/deployment)
- [Redis Production Tips](https://redis.io/docs/manual/admin/)
