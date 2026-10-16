# Deployment & Operations Guide — AllThingsCambridge LMS

## 1. Environment & Architecture Prerequisites
- **Web Server**: Apache 2.4+ / Nginx with URL rewriting enabled.
- **PHP**: PHP 8.2 or 8.3 with extensions `pdo_mysql`, `mbstring`, `openssl`, `bcmath`, `curl`, `gd`, `zip`.
- **Database Engine**: MySQL 8.0+ or MariaDB 10.4+ (default local environment configured on port `3308`).
- **Memory Limit**: `memory_limit = 256M` or higher recommended for past-paper PDF watermarking.

---

## 2. Environment Configuration (`.env`)
Ensure the following environment keys are properly populated:

```ini
APP_NAME=AllThingsCambridge
APP_ENV=production
APP_DEBUG=false
APP_URL=https://allthingscambridge.co.uk

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3308
DB_DATABASE=enterprise_lms
DB_USERNAME=root
DB_PASSWORD=

# Session & Security
SESSION_DRIVER=database
SESSION_LIFETIME=120
ENFORCE_SESSION_TIMEOUT=true

# Payment Gateway (Stripe)
STRIPE_KEY=pk_test_your_key_here
STRIPE_SECRET=sk_test_your_key_here
STRIPE_WEBHOOK_SECRET=whsec_your_webhook_secret_here
CASHIER_CURRENCY=gbp

# Storage & File Delivery
FILESYSTEM_DISK=public
```

---

## 3. Deployment Steps

### Step 1: Install & Optimize Dependencies
```bash
composer install --no-dev --optimize-autoloader
npm run build
```

### Step 2: Run Database Migrations & Seeders
```bash
php artisan migrate --force
php artisan db:seed --class=CambridgeAcademicDataSeeder --force
```

### Step 3: Cache Configuration & Routes for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Step 4: Storage Symlink
```bash
php artisan storage:link
```

---

## 4. Rollback & Disaster Recovery Procedures
In the event that a rollback of the Cambridge additions is required:

1. **Database Rollback**:
   ```bash
   php artisan migrate:rollback --step=1
   ```
2. **Clear Application Caches**:
   ```bash
   php artisan optimize:clear
   ```
3. **Database Backup Restoration**:
   If restoring a full MySQL snapshot from prior to the upgrade:
   ```bash
   mysql -u root -P 3308 enterprise_lms < backup_pre_upgrade.sql
   ```

---

## 5. Manual Actions Required by Project Owner
1. **Stripe Production Keys**:
   Update `STRIPE_KEY`, `STRIPE_SECRET`, and `STRIPE_WEBHOOK_SECRET` in `.env` once live merchant account credentials are provided. Configure the Stripe Webhook endpoint to point to `https://allthingscambridge.co.uk/stripe/webhook` with the events:
   - `checkout.session.completed`
   - `invoice.paid`
   - `invoice.payment_failed`
   - `customer.subscription.deleted`
2. **DNS & SSL Certificate**:
   Ensure SSL certificate covers `allthingscambridge.co.uk` and automated renewals are enabled.
3. **Official Examination Dates Maintenance**:
   Use the Cambridge administrator backend to keep examination timetable countdowns aligned with the latest CIE schedule.
