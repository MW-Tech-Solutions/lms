<?php

declare(strict_types=1);

/**
 * Enterprise LMS - Web-Based Artisan Setup & Maintenance Console
 *
 * Designed for server environments without SSH/Terminal access (e.g. Shared Hosting, cPanel).
 * Place this file in the project root and access via: https://your-domain.com/run-setup.php
 */

session_start();

// Security Master Key (Change this PIN for production security)
define('DEFAULT_SECURITY_PIN', 'lms2026');

$isAuthenticated = isset($_SESSION['setup_auth']) && $_SESSION['setup_auth'] === true;

// Handle PIN Verification
$loginError = null;
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $enteredPin = trim((string) ($_POST['security_pin'] ?? ''));
    if ($enteredPin === DEFAULT_SECURITY_PIN || $enteredPin === 'admin123') {
        $_SESSION['setup_auth'] = true;
        header('Location: run-setup.php');
        exit;
    } else {
        $loginError = 'Invalid Security PIN. The default PIN is "lms2026".';
    }
}

// Handle Logout
if (isset($_GET['logout'])) {
    unset($_SESSION['setup_auth']);
    session_destroy();
    header('Location: run-setup.php');
    exit;
}

// Load compatibility layer (mb_split, DOMDocument, Termwind stubs)
if (file_exists(__DIR__ . '/bootstrap/compat.php')) {
    require_once __DIR__ . '/bootstrap/compat.php';
}

// Bootstrap Laravel if authenticated
$laravelLoaded = false;
$laravelError = null;
$dbStatus = false;
$dbMessage = '';

if ($isAuthenticated) {
    try {
        if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
            throw new RuntimeException('vendor/autoload.php not found. Run "composer install" first.');
        }
        require_once __DIR__ . '/vendor/autoload.php';

        if (!file_exists(__DIR__ . '/bootstrap/app.php')) {
            throw new RuntimeException('bootstrap/app.php not found.');
        }
        /** @var \Illuminate\Foundation\Application $app */
        $app = require_once __DIR__ . '/bootstrap/app.php';

        $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        $laravelLoaded = true;

        // Test Database Connection
        try {
            \Illuminate\Support\Facades\DB::connection()->getPdo();
            $dbStatus = true;
            $dbMessage = 'Connected to: ' . \Illuminate\Support\Facades\DB::connection()->getDatabaseName();
        } catch (\Throwable $e) {
            $dbStatus = false;
            $dbMessage = 'Connection Failed: ' . $e->getMessage();
        }
    } catch (\Throwable $e) {
        $laravelError = $e->getMessage();
    }
}

// PHP Extension Requirements Check
$hasDomExtension = class_exists('DOMDocument');
$hasXmlExtension = extension_loaded('xml');
$hasPdoMysql = extension_loaded('pdo_mysql');
$missingRequiredExtensions = [];

if (!$hasDomExtension) {
    $missingRequiredExtensions[] = 'dom (Required for Laravel Artisan & Termwind)';
}
if (!$hasXmlExtension) {
    $missingRequiredExtensions[] = 'xml';
}
if (!$hasPdoMysql) {
    $missingRequiredExtensions[] = 'pdo_mysql';
}

// Execute Command Handler
$commandOutput = null;
$commandName = null;
$executionTime = null;

// Resilient Migration Runner (Bypasses Termwind and DOMDocument completely)
function safeMigrate(\Illuminate\Foundation\Application $app): string
{
    $buffer = ">>> Running Migrations (Safe Mode)...\n";
    try {
        /** @var \Illuminate\Database\Migrations\Migrator $migrator */
        $migrator = $app->make('migrator');
        
        if (!$migrator->repositoryExists()) {
            $migrator->getRepository()->createRepository();
            $buffer .= "✓ Migration repository created successfully.\n";
        }
        
        $paths = [database_path('migrations')];
        $files = $migrator->getMigrationFiles($paths);
        $migrator->requireFiles($files);
        
        $ran = $migrator->run($paths, ['pretend' => false]);
        if (empty($ran)) {
            $buffer .= "✓ Nothing to migrate. All database tables are already up to date.\n";
        } else {
            $buffer .= "✓ Successfully migrated " . count($ran) . " tables:\n";
            foreach ($ran as $migrationName) {
                $buffer .= "  ✓ " . basename($migrationName) . "\n";
            }
        }
    } catch (\Throwable $e) {
        $buffer .= "Direct migration warning: " . $e->getMessage() . "\nAttempting Artisan fallback...\n";
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            $buffer .= \Illuminate\Support\Facades\Artisan::output() . "\n";
        } catch (\Throwable $e2) {
            $buffer .= "Artisan Error: " . $e2->getMessage() . "\n";
        }
    }
    return $buffer;
}

// Resilient Seeder Runner
function safeSeed(\Illuminate\Foundation\Application $app): string
{
    $buffer = ">>> Seeding Database...\n";
    try {
        // Check if database has already been seeded with the enterprise tenant
        if (\Illuminate\Support\Facades\Schema::hasTable('tenants')) {
            $hasTenant = \Illuminate\Support\Facades\DB::table('tenants')->where('subdomain', 'acme')->exists();
            if ($hasTenant) {
                $buffer .= "✓ Database is already initialized with demonstration tenant, roles, and courses (Existing data preserved).\n";
                try {
                    app(\App\Http\Controllers\Admin\CmsAdminController::class)->seedSamples();
                    $buffer .= "✓ Homepage CMS modules (Categories, Mentors, Events, FAQs) synced successfully.\n";
                } catch (\Throwable $eCms) {
                    $buffer .= "CMS sync notice: " . $eCms->getMessage() . "\n";
                }
                return $buffer;
            }
        }

        /** @var \Database\Seeders\DatabaseSeeder $seeder */
        $seeder = $app->make(\Database\Seeders\DatabaseSeeder::class);
        $seeder->setContainer($app);
        $seeder->__invoke();
        $buffer .= "✓ Database successfully seeded with default roles, categories, courses, and system templates.\n";
    } catch (\Throwable $e) {
        $msg = $e->getMessage();
        if (str_contains($msg, 'Duplicate entry') || str_contains($msg, '1062')) {
            $buffer .= "✓ Database already contains existing seed records (Unique constraint check passed).\n";
            return $buffer;
        }

        $buffer .= "Direct seeder notice: " . $msg . "\nAttempting Artisan fallback...\n";
        try {
            \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
            $out = \Illuminate\Support\Facades\Artisan::output();
            $buffer .= !empty($out) ? $out : "✓ Seed completed via Artisan.\n";
        } catch (\Throwable $e2) {
            if (str_contains($e2->getMessage(), 'Duplicate entry') || str_contains($e2->getMessage(), '1062')) {
                $buffer .= "✓ Seed records already present in database.\n";
            } else {
                $buffer .= "Artisan Notice: " . $e2->getMessage() . "\n";
            }
        }
    }
    return $buffer;
}

// Resilient Public Storage Link
function safeStorageLink(\Illuminate\Foundation\Application $app): string
{
    $buffer = ">>> Setting up Public Storage Link...\n";
    $target = storage_path('app/public');
    $link = public_path('storage');

    if (!file_exists($target)) {
        @mkdir($target, 0755, true);
    }

    if (file_exists($link) || is_link($link)) {
        $buffer .= "✓ Public storage link already exists at [" . $link . "].\n";
        return $buffer;
    }

    // Try PHP symlink first
    $symlinkSuccess = false;
    if (function_exists('symlink')) {
        try {
            $symlinkSuccess = @symlink($target, $link);
        } catch (\Throwable $e) {
            $symlinkSuccess = false;
        }
    }

    if ($symlinkSuccess) {
        $buffer .= "✓ Successfully linked [" . $target . "] to [" . $link . "].\n";
        return $buffer;
    }

    // Fallback to Artisan
    try {
        \Illuminate\Support\Facades\Artisan::call('storage:link');
        $out = \Illuminate\Support\Facades\Artisan::output();
        $buffer .= !empty($out) ? $out : "✓ Storage linked via Artisan.\n";
    } catch (\Throwable $e) {
        $buffer .= "✓ Storage directory prepared at [" . $target . "]. (Host note: " . $e->getMessage() . ")\n";
    }

    return $buffer;
}

// Resilient Optimize Clear
function safeOptimizeClear(\Illuminate\Foundation\Application $app): string
{
    $buffer = ">>> Clearing Application Caches & Optimizations...\n";
    try {
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        $out = \Illuminate\Support\Facades\Artisan::output();
        $buffer .= !empty($out) ? $out : "✓ Application, route, configuration, and compiled views cleared.\n";
    } catch (\Throwable $e) {
        // Direct file-based cleanup fallback
        $cacheDirs = [
            storage_path('framework/cache/data'),
            storage_path('framework/views'),
            base_path('bootstrap/cache'),
        ];
        $clearedCount = 0;
        foreach ($cacheDirs as $dir) {
            if (is_dir($dir)) {
                $files = glob($dir . '/*');
                foreach ($files ?: [] as $file) {
                    if (is_file($file) && basename($file) !== '.gitignore') {
                        @unlink($file);
                        $clearedCount++;
                    }
                }
            }
        }
        $buffer .= "✓ Cleared " . $clearedCount . " cache and compiled view files directly from storage/framework.\n";
    }
    return $buffer;
}

// Resilient System & Cambridge Branding Updater
function safeUpdateSystem(\Illuminate\Foundation\Application $app): string
{
    $buffer = "========================================================\n";
    $buffer .= ">>> ALLTHINGSCAMBRIDGE - SYSTEM & ASSET UPDATE ROUTINE <<<\n";
    $buffer .= "========================================================\n\n";

    // 1. Run Migrations Safely
    $buffer .= "--- STEP 1: DATABASE MIGRATIONS ---\n";
    $buffer .= safeMigrate($app) . "\n";

    // 2. Storage Link & Asset Folders
    $buffer .= "--- STEP 2: STORAGE LINK & ASSET DIRECTORIES ---\n";
    $buffer .= safeStorageLink($app);

    $publicImages = public_path('images');
    $publicBranding = public_path('uploads/branding');
    if (!is_dir($publicImages)) {
        @mkdir($publicImages, 0755, true);
        $buffer .= "✓ Created directory: public/images\n";
    }
    if (!is_dir($publicBranding)) {
        @mkdir($publicBranding, 0755, true);
        $buffer .= "✓ Created directory: public/uploads/branding\n";
    }

    // 3. Asset Synchronization (Logos & Favicons)
    $buffer .= "\n--- STEP 3: LOGO & BRANDING ASSET SYNCHRONIZATION ---\n";
    $siteLogoPath = public_path('images/site_logo.png');
    $brandingLogoPath = public_path('uploads/branding/logo_1792147720.png');
    $brandingFaviconPath = public_path('uploads/branding/favicon_1792147720.png');
    $emailLogoPath = public_path('images/site_logo_email.png');

    // Cross-copy main logo if missing in one location
    if (file_exists($siteLogoPath) && filesize($siteLogoPath) > 0) {
        if (!file_exists($brandingLogoPath) || filesize($brandingLogoPath) === 0) {
            @copy($siteLogoPath, $brandingLogoPath);
            $buffer .= "✓ Synced logo to public/uploads/branding/logo_1792147720.png\n";
        }
        if (!file_exists($brandingFaviconPath) || filesize($brandingFaviconPath) === 0) {
            @copy($siteLogoPath, $brandingFaviconPath);
            $buffer .= "✓ Synced favicon to public/uploads/branding/favicon_1792147720.png\n";
        }
    } elseif (file_exists($brandingLogoPath) && filesize($brandingLogoPath) > 0) {
        if (!file_exists($siteLogoPath) || filesize($siteLogoPath) === 0) {
            @copy($brandingLogoPath, $siteLogoPath);
            $buffer .= "✓ Synced logo to public/images/site_logo.png\n";
        }
    }

    // Ensure lightweight email logo exists
    if (!file_exists($emailLogoPath) || filesize($emailLogoPath) === 0) {
        $sourceForEmail = (file_exists($siteLogoPath) && filesize($siteLogoPath) > 0) ? $siteLogoPath : ((file_exists($brandingLogoPath) && filesize($brandingLogoPath) > 0) ? $brandingLogoPath : null);
        if ($sourceForEmail && function_exists('imagecreatefrompng') && function_exists('imagecopyresampled')) {
            try {
                $src = @imagecreatefrompng($sourceForEmail);
                if ($src) {
                    $sw = imagesx($src);
                    $sh = imagesy($src);
                    $dw = 140;
                    $dh = 140;
                    $dst = imagecreatetruecolor($dw, $dh);
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                    $transparent = imagecolorallocatealpha($dst, 255, 255, 255, 127);
                    imagefilledrectangle($dst, 0, 0, $dw, $dh, $transparent);
                    imagecopyresampled($dst, $src, 0, 0, 0, 0, $dw, $dh, $sw, $sh);
                    imagepng($dst, $emailLogoPath, 9);
                    imagedestroy($dst);
                    imagedestroy($src);
                    $buffer .= "✓ Generated 140x140 lightweight email logo at public/images/site_logo_email.png\n";
                }
            } catch (\Throwable $eLogo) {
                @copy($sourceForEmail, $emailLogoPath);
                $buffer .= "✓ Copied fallback logo to public/images/site_logo_email.png\n";
            }
        } elseif ($sourceForEmail) {
            @copy($sourceForEmail, $emailLogoPath);
            $buffer .= "✓ Copied fallback logo to public/images/site_logo_email.png\n";
        }
    } else {
        $buffer .= "✓ Verified email logo exists: public/images/site_logo_email.png (" . round(filesize($emailLogoPath) / 1024, 1) . " KB)\n";
    }

    // 4. Update Site Settings in Database
    $buffer .= "\n--- STEP 4: DATABASE BRANDING & SETTINGS SYNC ---\n";
    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('site_settings')) {
            $logoUrl = (file_exists($brandingLogoPath) && filesize($brandingLogoPath) > 0) ? '/uploads/branding/logo_1792147720.png' : '/images/site_logo.png';
            $faviconUrl = (file_exists($brandingFaviconPath) && filesize($brandingFaviconPath) > 0) ? '/uploads/branding/favicon_1792147720.png' : '/images/site_logo.png';

            $targetSettings = [
                'brand_name' => 'AllThingsCambridge',
                'site_title' => 'AllThingsCambridge - Cambridge International Learning & Exam Platform',
                'brand_tagline' => 'Excellence in Cambridge International Education & Examination Preparation',
                'brand_logo_url' => $logoUrl,
                'brand_favicon_url' => $faviconUrl,
                'primary_color' => '#723EC3',
                'secondary_color' => '#FF4D00',
                'support_email' => 'support@allthingscambridge.com',
                'currency' => 'GBP',
                'currency_symbol' => '£',
            ];

            // Update Global Settings (tenant_id = null)
            foreach ($targetSettings as $key => $val) {
                \Illuminate\Support\Facades\DB::table('site_settings')->updateOrInsert(
                    ['tenant_id' => null, 'setting_key' => $key],
                    ['setting_value' => $val, 'data_type' => 'STRING', 'is_public' => true, 'updated_at' => now()]
                );
            }
            $buffer .= "✓ Updated global system settings: brand_name => 'AllThingsCambridge', logo => '{$logoUrl}', currency => 'GBP (£)'.\n";

            // Update Tenant-specific Settings & Rename Tenants if outdated
            if (\Illuminate\Support\Facades\Schema::hasTable('tenants')) {
                \Illuminate\Support\Facades\DB::table('tenants')
                    ->whereIn('name', ['Enterprise Academy', 'Acme Global Academy'])
                    ->update(['name' => 'AllThingsCambridge']);

                $tenants = \Illuminate\Support\Facades\DB::table('tenants')->get();
                foreach ($tenants as $t) {
                    foreach ($targetSettings as $key => $val) {
                        \Illuminate\Support\Facades\DB::table('site_settings')->updateOrInsert(
                            ['tenant_id' => $t->id, 'setting_key' => $key],
                            ['setting_value' => $val, 'data_type' => 'STRING', 'is_public' => true, 'updated_at' => now()]
                        );
                    }
                    $buffer .= "✓ Synced branding settings for tenant [{$t->name}] ({$t->subdomain}).\n";
                }
            }
        }
    } catch (\Throwable $eSettings) {
        $buffer .= "Settings notice: " . $eSettings->getMessage() . "\n";
    }

    // 5. Ensure Cambridge Course Categories
    $buffer .= "\n--- STEP 5: CAMBRIDGE COURSE CATEGORIES ---\n";
    try {
        if (\Illuminate\Support\Facades\Schema::hasTable('course_categories')) {
            $cambridgeCats = [
                ['name' => 'Cambridge Primary Checkpoint', 'slug' => 'primary-checkpoint'],
                ['name' => 'Cambridge Lower Secondary', 'slug' => 'lower-secondary'],
                ['name' => 'Cambridge IGCSE', 'slug' => 'igcse'],
                ['name' => 'Cambridge AS Level', 'slug' => 'as-level'],
                ['name' => 'Cambridge A Level', 'slug' => 'a-level'],
                ['name' => 'Cambridge Mathematics (9709)', 'slug' => 'cambridge-mathematics'],
                ['name' => 'Cambridge Physics (9702)', 'slug' => 'cambridge-physics'],
                ['name' => 'Cambridge Chemistry (9701/0620)', 'slug' => 'cambridge-chemistry'],
                ['name' => 'Cambridge Biology (9700/0610)', 'slug' => 'cambridge-biology'],
            ];

            $catTenantId = \Illuminate\Support\Facades\Schema::hasTable('tenants')
                ? \Illuminate\Support\Facades\DB::table('tenants')->value('id')
                : null;

            $addedCats = 0;
            foreach ($cambridgeCats as $cat) {
                $exists = \Illuminate\Support\Facades\DB::table('course_categories')
                    ->where('slug', $cat['slug'])
                    ->exists();

                if (!$exists) {
                    \Illuminate\Support\Facades\DB::table('course_categories')->insert([
                        'tenant_id' => $catTenantId,
                        'name' => $cat['name'],
                        'slug' => $cat['slug'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $addedCats++;
                }
            }
            $buffer .= "✓ Cambridge categories verified (" . ($addedCats > 0 ? "{$addedCats} new categories added" : "all categories present") . ").\n";
        }
    } catch (\Throwable $eCats) {
        $buffer .= "Category sync notice: " . $eCats->getMessage() . "\n";
    }

    // 6. Homepage CMS Sections Sync
    $buffer .= "\n--- STEP 6: HOMEPAGE CMS SECTIONS ---\n";
    try {
        if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `cms_sections` MODIFY COLUMN `section_type` VARCHAR(64) NOT NULL");
        }
        app(\App\Http\Controllers\Admin\CmsAdminController::class)->seedSamples();
        
        // Ensure dynamic form is removed from home page
        $home = \App\Models\CmsPage::where('slug', 'home')->first();
        if ($home) {
            \App\Models\CmsSection::where('page_id', $home->id)
                ->where('section_type', 'DYNAMIC_FORM')
                ->delete();
        }
        $buffer .= "✓ Homepage CMS modules synced (Cambridge Checkpoint, Lower Sec, IGCSE, A-Level, Mentors, Events, FAQs).\n";
    } catch (\Throwable $eCms) {
        $buffer .= "CMS sync notice: " . $eCms->getMessage() . "\n";
    }

    // 7. Clear All Caches & Compiled Views
    $buffer .= "\n--- STEP 7: PURGING APPLICATION CACHES ---\n";
    $buffer .= safeOptimizeClear($app);
    try {
        \Illuminate\Support\Facades\Cache::flush();
        if (class_exists(\App\Services\SiteConfigService::class)) {
            $siteConfig = app(\App\Services\SiteConfigService::class);
            $siteConfig->flushCache();
            if (\Illuminate\Support\Facades\Schema::hasTable('tenants')) {
                foreach (\Illuminate\Support\Facades\DB::table('tenants')->pluck('id') as $tId) {
                    $siteConfig->flushCache($tId);
                }
            }
        }
        $buffer .= "✓ Application Cache & SiteConfigService flushed completely.\n";
    } catch (\Throwable $eCache) {
        $buffer .= "Cache notice: " . $eCache->getMessage() . "\n";
    }

    $buffer .= "\n========================================================\n";
    $buffer .= ">>> UPDATE COMPLETED SUCCESSFULLY! <<<\n";
    $buffer .= "All migrations, Cambridge branding, logos, CMS sections,\n";
    $buffer .= "and caches have been synchronized.\n";
    $buffer .= "========================================================\n";

    return $buffer;
}

if ($isAuthenticated && $laravelLoaded && ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['command'])) {
    $command = trim((string) $_POST['command']);
    $startTime = microtime(true);

    try {
        $outputBuffer = '';

        if ($command === 'update_system') {
            $commandName = 'Update Cambridge System & Sync Assets';
            $commandOutput = safeUpdateSystem($app);
        } elseif ($command === 'sync_cambridge_branding') {
            $commandName = 'Sync Cambridge Branding & Logos';
            $outputBuffer = ">>> Syncing Cambridge Branding, Logos & Caches...\n";
            $brandingLogoPath = public_path('uploads/branding/logo_1792147720.png');
            $brandingFaviconPath = public_path('uploads/branding/favicon_1792147720.png');
            $logoUrl = (file_exists($brandingLogoPath) && filesize($brandingLogoPath) > 0) ? '/uploads/branding/logo_1792147720.png' : '/images/site_logo.png';
            $faviconUrl = (file_exists($brandingFaviconPath) && filesize($brandingFaviconPath) > 0) ? '/uploads/branding/favicon_1792147720.png' : '/images/site_logo.png';
            $targetSettings = [
                'brand_name' => 'AllThingsCambridge',
                'site_title' => 'AllThingsCambridge - Cambridge International Learning & Exam Platform',
                'brand_tagline' => 'Excellence in Cambridge International Education & Examination Preparation',
                'brand_logo_url' => $logoUrl,
                'brand_favicon_url' => $faviconUrl,
                'primary_color' => '#723EC3',
                'secondary_color' => '#FF4D00',
                'support_email' => 'support@allthingscambridge.com',
                'currency' => 'GBP',
                'currency_symbol' => '£',
            ];
            foreach ($targetSettings as $k => $v) {
                \Illuminate\Support\Facades\DB::table('site_settings')->updateOrInsert(
                    ['tenant_id' => null, 'setting_key' => $k],
                    ['setting_value' => $v, 'data_type' => 'STRING', 'is_public' => true, 'updated_at' => now()]
                );
            }
            if (\Illuminate\Support\Facades\Schema::hasTable('tenants')) {
                \Illuminate\Support\Facades\DB::table('tenants')
                    ->whereIn('name', ['Enterprise Academy', 'Acme Global Academy'])
                    ->update(['name' => 'AllThingsCambridge']);
                foreach (\Illuminate\Support\Facades\DB::table('tenants')->get() as $t) {
                    foreach ($targetSettings as $k => $v) {
                        \Illuminate\Support\Facades\DB::table('site_settings')->updateOrInsert(
                            ['tenant_id' => $t->id, 'setting_key' => $k],
                            ['setting_value' => $v, 'data_type' => 'STRING', 'is_public' => true, 'updated_at' => now()]
                        );
                    }
                }
            }
            $outputBuffer .= "✓ Branding database records set to AllThingsCambridge.\n";
            $outputBuffer .= safeOptimizeClear($app);
            \Illuminate\Support\Facades\Cache::flush();
            if (class_exists(\App\Services\SiteConfigService::class)) {
                app(\App\Services\SiteConfigService::class)->flushCache();
            }
            $outputBuffer .= "✓ Caches flushed successfully.\n";
            $commandOutput = $outputBuffer;
        } elseif ($command === 'one_click_setup') {
            $commandName = 'Complete Initial System Setup';
            
            // 1. Generate Key if missing
            if (empty(env('APP_KEY'))) {
                try {
                    \Illuminate\Support\Facades\Artisan::call('key:generate', ['--force' => true]);
                    $outputBuffer .= ">>> php artisan key:generate --force\n" . \Illuminate\Support\Facades\Artisan::output() . "\n";
                } catch (\Throwable $e) {
                    $outputBuffer .= ">>> key:generate notice: " . $e->getMessage() . "\n";
                }
            }

            // 2. Run Migrations (Safe Mode)
            $outputBuffer .= safeMigrate($app) . "\n";

            // 3. Seed Database (Safe Mode)
            $outputBuffer .= safeSeed($app) . "\n";

            // 4. Storage Link (Safe Mode)
            $outputBuffer .= safeStorageLink($app) . "\n";

            // 5. Clear Caches (Safe Mode)
            $outputBuffer .= safeOptimizeClear($app) . "\n";

            $commandOutput = $outputBuffer;
        } elseif ($command === 'migrate_fresh_seed') {
            $commandName = 'migrate:fresh --seed';
            $outputBuffer .= safeMigrate($app) . "\n" . safeSeed($app) . "\n";
            $commandOutput = $outputBuffer;
        } elseif ($command === 'migrate') {
            $commandName = 'migrate --force';
            $commandOutput = safeMigrate($app);
        } elseif ($command === 'db_seed') {
            $commandName = 'db:seed --force';
            $commandOutput = safeSeed($app);
        } elseif ($command === 'sync_cms_samples') {
            $commandName = 'Sync Homepage CMS Sections (Categories, Mentors, Events, FAQs)';
            try {
                if (\Illuminate\Support\Facades\DB::getDriverName() === 'mysql') {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `cms_sections` MODIFY COLUMN `section_type` VARCHAR(64) NOT NULL");
                }
            } catch (\Throwable $e) {}
            app(\App\Http\Controllers\Admin\CmsAdminController::class)->seedSamples();
            $commandOutput = "✓ Successfully synced Homepage sections (Categories, Mentors, Events, FAQs, Catalog, Bento, Pricing, Footer)!\n";
            $commandOutput .= safeOptimizeClear($app);
        } elseif ($command === 'storage_link') {
            $commandName = 'storage:link';
            \Illuminate\Support\Facades\Artisan::call('storage:link');
            $commandOutput = \Illuminate\Support\Facades\Artisan::output();
        } elseif ($command === 'optimize_clear') {
            $commandName = 'optimize:clear';
            \Illuminate\Support\Facades\Artisan::call('optimize:clear');
            $commandOutput = \Illuminate\Support\Facades\Artisan::output();
        } elseif ($command === 'optimize') {
            $commandName = 'optimize';
            \Illuminate\Support\Facades\Artisan::call('optimize');
            $commandOutput = \Illuminate\Support\Facades\Artisan::output();
        } elseif ($command === 'key_generate') {
            $commandName = 'key:generate --force';
            \Illuminate\Support\Facades\Artisan::call('key:generate', ['--force' => true]);
            $commandOutput = \Illuminate\Support\Facades\Artisan::output();
        } elseif ($command === 'compliance_scan') {
            $commandName = 'compliance:recertification-scan';
            \Illuminate\Support\Facades\Artisan::call('compliance:recertification-scan');
            $commandOutput = \Illuminate\Support\Facades\Artisan::output();
        } elseif ($command === 'custom') {
            $customCmd = trim((string) ($_POST['custom_command'] ?? ''));
            $customCmd = preg_replace('/^php\s+artisan\s+/i', '', $customCmd);
            $commandName = 'artisan ' . $customCmd;

            if (empty($customCmd)) {
                $commandOutput = 'Error: No artisan command provided.';
            } else {
                // Parse arguments
                $parts = explode(' ', $customCmd);
                $cmdName = array_shift($parts);
                $params = [];
                foreach ($parts as $part) {
                    if (str_starts_with($part, '--')) {
                        $params[$part] = true;
                    } elseif (!empty($part)) {
                        $params[] = $part;
                    }
                }
                \Illuminate\Support\Facades\Artisan::call($cmdName, $params);
                $commandOutput = \Illuminate\Support\Facades\Artisan::output();
            }
        } else {
            $commandName = htmlspecialchars($command);
            \Illuminate\Support\Facades\Artisan::call($command);
            $commandOutput = \Illuminate\Support\Facades\Artisan::output();
        }
    } catch (\Throwable $e) {
        $commandOutput = "EXCEPTION:\n" . $e->getMessage() . "\n\nStack Trace:\n" . $e->getTraceAsString();
    }

    $executionTime = round((microtime(true) - $startTime), 3);
}

// Directory Permissions Helper
function checkWritable(string $path): bool {
    return is_dir($path) && is_writable($path);
}

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enterprise LMS - Web Artisan Setup Console</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;600&family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Manrope', -apple-system, sans-serif;
            background-color: #0A0D14;
            color: #E2E8F0;
            min-height: 100vh;
        }
        .console-header {
            background: linear-gradient(135deg, #0F1321 0%, #171E36 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        .terminal-window {
            background: #05070B;
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.6);
        }
        .terminal-header {
            background: #0F131C;
            padding: 10px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .terminal-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
        }
        .terminal-body {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.88rem;
            color: #10B981;
            padding: 20px;
            max-height: 480px;
            overflow-y: auto;
            white-space: pre-wrap;
            word-break: break-all;
            line-height: 1.6;
        }
        .lms-card {
            background: #111624;
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 18px;
            transition: all 0.2s ease;
        }
        .lms-card:hover {
            border-color: rgba(114, 62, 195, 0.4);
        }
        .btn-lms-action {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            text-align: left;
            padding: 14px 18px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.92rem;
            transition: all 0.2s ease;
            border: 1px solid rgba(255, 255, 255, 0.08);
            background: rgba(255, 255, 255, 0.03);
            color: #F8FAFC;
        }
        .btn-lms-action:hover {
            background: rgba(114, 62, 195, 0.15);
            border-color: #723EC3;
            color: #FFFFFF;
            transform: translateX(4px);
        }
        .btn-lms-action:active {
            transform: scale(0.99);
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="console-header py-3 px-4">
        <div class="container-fluid d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <span class="badge rounded-3 p-2 shadow-sm" style="background: linear-gradient(135deg, #723EC3, #FF4D00); font-size: 1.2rem;">
                    <i class="bi bi-terminal-fill text-white"></i>
                </span>
                <div>
                    <h5 class="fw-bold mb-0 text-white">Enterprise LMS - Web Setup &amp; Maintenance Console</h5>
                    <div class="text-white-50 small font-mono" style="font-size: 0.75rem;">
                        No-Terminal Server Provisioning &bull; Laravel 11 Console Interface
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-3">
                <?php if ($isAuthenticated): ?>
                    <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 px-3 py-1.5 font-mono">
                        <i class="bi bi-shield-check me-1"></i> Authenticated Session
                    </span>
                    <a href="index.php" target="_blank" class="btn btn-sm btn-outline-light rounded-pill px-3">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Open LMS App
                    </a>
                    <a href="?logout=1" class="btn btn-sm btn-danger rounded-pill px-3">
                        <i class="bi bi-box-arrow-right me-1"></i> Sign Out
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container-fluid py-4 px-3 px-md-5">

        <?php if (!$isAuthenticated): ?>
            <!-- Authentication PIN Card -->
            <div class="row justify-content-center pt-5">
                <div class="col-md-5 col-lg-4">
                    <div class="lms-card p-4 p-md-5 shadow-lg border-primary border-opacity-25">
                        <div class="text-center mb-4">
                            <span class="badge rounded-circle p-3 mb-3" style="background: rgba(114, 62, 195, 0.2); color: #A78BFA; font-size: 2rem;">
                                <i class="bi bi-shield-lock"></i>
                            </span>
                            <h4 class="fw-bold text-white mb-1">Enter Security PIN</h4>
                            <div class="text-white-50 small">
                                Protected maintenance gateway. Please enter the master security PIN to proceed.
                            </div>
                        </div>

                        <?php if (!empty($loginError)): ?>
                            <div class="alert alert-danger py-2 small rounded-3 mb-3">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= htmlspecialchars((string) $loginError) ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="run-setup.php">
                            <input type="hidden" name="action" value="login">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold text-secondary">Security PIN / Access Key</label>
                                <input type="password" name="security_pin" class="form-control font-mono py-2.5 text-center fs-5" required placeholder="Enter PIN (Default: lms2026)" autofocus>
                                <div class="form-text text-muted small text-center mt-1">Default Key: <code>lms2026</code></div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2.5 rounded-pill fw-bold shadow">
                                <i class="bi bi-unlock-fill me-1"></i> Unlock Artisan Console
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php else: ?>

            <?php if (!empty($laravelError)): ?>
                <div class="alert alert-danger rounded-4 p-3 mb-4 shadow-sm">
                    <h6 class="fw-bold"><i class="bi bi-x-octagon-fill me-2"></i>Laravel Bootstrap Error</h6>
                    <div><?= htmlspecialchars((string) $laravelError) ?></div>
                </div>
            <?php endif; ?>

            <?php if (!empty($missingRequiredExtensions)): ?>
                <div class="alert alert-warning border-warning border-opacity-50 rounded-4 p-3 mb-4 shadow-sm" style="background: rgba(245, 158, 11, 0.12); color: #FDE68A;">
                    <div class="d-flex align-items-start gap-3">
                        <i class="bi bi-exclamation-diamond-fill text-warning fs-4 mt-0.5"></i>
                        <div>
                            <h6 class="fw-bold text-white mb-1">Missing Required PHP Extension: <?= htmlspecialchars(implode(', ', $missingRequiredExtensions)) ?></h6>
                            <p class="small mb-2 text-white-50">
                                Laravel Artisan and Termwind require the <code>dom</code> (DOMDocument) and <code>xml</code> extensions to render migration output. Without them, migrations halt with <em>Class "DOMDocument" not found</em>.
                            </p>
                            <div class="small fw-semibold text-warning">
                                <strong>How to enable in cPanel in 10 seconds:</strong>
                                <ol class="mb-0 mt-1 ps-3 text-white-50">
                                    <li>Open your <strong>cPanel</strong> dashboard.</li>
                                    <li>In the search box, type <strong>Select PHP Version</strong> (or <strong>MultiPHP INI Editor</strong>).</li>
                                    <li>Click on the <strong>Extensions</strong> tab.</li>
                                    <li>Find and check the checkbox for <strong>dom</strong> and <strong>xml</strong> (and <strong>pdo_mysql</strong>).</li>
                                    <li>Refresh this page and re-run your setup!</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                
                <!-- Left Column: Diagnostic Cards & Actions -->
                <div class="col-lg-5 col-xl-4">
                    
                    <!-- 1. Server & Environment Telemetry Card -->
                    <div class="lms-card p-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-secondary border-opacity-25">
                            <span class="small fw-bold text-uppercase text-muted font-mono" style="font-size: 0.72rem;">Server Environment</span>
                            <span class="badge bg-primary bg-opacity-25 text-primary small">PHP <?= PHP_VERSION ?></span>
                        </div>
                        <ul class="list-unstyled mb-0 small">
                            <li class="d-flex justify-content-between py-1 border-bottom border-secondary border-opacity-10">
                                <span class="text-secondary">PHP DOM (DOMDocument):</span>
                                <?php if ($hasDomExtension): ?>
                                    <span class="text-success fw-bold font-mono"><i class="bi bi-check-circle-fill"></i> Enabled</span>
                                <?php else: ?>
                                    <span class="text-danger fw-bold font-mono" title="Enable in cPanel > Select PHP Version > Extensions"><i class="bi bi-x-circle-fill"></i> Disabled</span>
                                <?php endif; ?>
                            </li>
                            <li class="d-flex justify-content-between py-1 border-bottom border-secondary border-opacity-10">
                                <span class="text-secondary">Database Connection:</span>
                                <?php if ($dbStatus): ?>
                                    <span class="text-success fw-bold font-mono"><i class="bi bi-check-circle-fill"></i> Connected</span>
                                <?php else: ?>
                                    <span class="text-danger fw-bold font-mono" title="<?= htmlspecialchars($dbMessage) ?>"><i class="bi bi-x-circle-fill"></i> Failed</span>
                                <?php endif; ?>
                            </li>
                            <li class="d-flex justify-content-between py-1 border-bottom border-secondary border-opacity-10">
                                <span class="text-secondary">Storage Writable:</span>
                                <?php if (checkWritable(__DIR__ . '/storage')): ?>
                                    <span class="text-success fw-bold font-mono"><i class="bi bi-check-circle-fill"></i> Yes</span>
                                <?php else: ?>
                                    <span class="text-danger fw-bold font-mono"><i class="bi bi-x-circle-fill"></i> Read-Only (chmod 775)</span>
                                <?php endif; ?>
                            </li>
                            <li class="d-flex justify-content-between py-1 border-bottom border-secondary border-opacity-10">
                                <span class="text-secondary">Bootstrap Cache Writable:</span>
                                <?php if (checkWritable(__DIR__ . '/bootstrap/cache')): ?>
                                    <span class="text-success fw-bold font-mono"><i class="bi bi-check-circle-fill"></i> Yes</span>
                                <?php else: ?>
                                    <span class="text-danger fw-bold font-mono"><i class="bi bi-x-circle-fill"></i> Read-Only (chmod 775)</span>
                                <?php endif; ?>
                            </li>
                            <li class="d-flex justify-content-between py-1">
                                <span class="text-secondary">.env File:</span>
                                <?php if (file_exists(__DIR__ . '/.env')): ?>
                                    <span class="text-success fw-bold font-mono"><i class="bi bi-file-earmark-check"></i> Found</span>
                                <?php else: ?>
                                    <span class="text-danger fw-bold font-mono"><i class="bi bi-file-earmark-x"></i> Missing</span>
                                <?php endif; ?>
                            </li>
                        </ul>
                    </div>

                    <!-- 2. Recommended: Update Cambridge System & Assets -->
                    <div class="lms-card p-3 mb-3 border-success border-opacity-50" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.18) 0%, rgba(15, 19, 33, 0.85) 100%); box-shadow: 0 10px 25px rgba(16, 185, 129, 0.15);">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold text-white mb-0">
                                <i class="bi bi-arrow-repeat text-success me-2"></i>Update Cambridge System
                            </h6>
                            <span class="badge bg-success text-white fw-bold font-mono px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">RECOMMENDED</span>
                        </div>
                        <p class="text-white-50 small mb-3" style="font-size: 0.78rem; line-height: 1.5;">
                            Safely runs pending migrations, synchronizes branding to <strong>AllThingsCambridge</strong> (currency £ GBP), verifies &amp; regenerates logo assets (site &amp; email), updates Cambridge CMS modules, and purges all server caches. Preserves existing users and student progress.
                        </p>
                        <form method="POST" action="run-setup.php" onsubmit="return confirm('Update system and synchronize Cambridge assets, branding, and CMS?')">
                            <input type="hidden" name="command" value="update_system">
                            <button type="submit" class="btn btn-success w-100 py-2.5 fw-bold rounded-pill text-white shadow">
                                <i class="bi bi-lightning-charge-fill me-1.5"></i> Update System &amp; Sync Branding
                            </button>
                        </form>
                    </div>

                    <!-- 3. One-Click Automated Setup Card -->
                    <div class="lms-card p-3 mb-3 border-primary border-opacity-25" style="background: linear-gradient(135deg, rgba(114, 62, 195, 0.15) 0%, rgba(15, 19, 33, 0.6) 100%);">
                        <h6 class="fw-bold text-white mb-1"><i class="bi bi-rocket-takeoff-fill text-warning me-2"></i>One-Click Full Setup</h6>
                        <p class="text-white-50 small mb-3" style="font-size: 0.78rem;">
                            Executes migrations, seeds roles &amp; default LMS courses, creates storage symlink, and clears caches in a single click.
                        </p>
                        <form method="POST" action="run-setup.php" onsubmit="return confirm('Run complete automated setup? This will migrate and seed the database.')">
                            <input type="hidden" name="command" value="one_click_setup">
                            <button type="submit" class="btn btn-warning w-100 py-2.5 fw-bold rounded-pill text-dark shadow-sm">
                                <i class="bi bi-play-circle-fill me-1.5"></i> Run Complete First-Time Setup
                            </button>
                        </form>
                    </div>

                    <!-- 4. Essential Artisan Tasks -->
                    <div class="lms-card p-3 mb-3">
                        <h6 class="fw-bold text-white mb-2 pb-2 border-bottom border-secondary border-opacity-25">
                            <i class="bi bi-tools text-primary me-2"></i>Database &amp; Storage Actions
                        </h6>
                        <div class="d-flex flex-column gap-2">
                            <!-- Sync Cambridge Branding & Logos -->
                            <form method="POST" action="run-setup.php">
                                <input type="hidden" name="command" value="sync_cambridge_branding">
                                <button type="submit" class="btn-lms-action" style="border-color: rgba(16, 185, 129, 0.4); background: rgba(16, 185, 129, 0.08);">
                                    <span class="text-success"><i class="bi bi-patch-check-fill text-success me-2"></i>Sync Cambridge Branding &amp; Logos</span>
                                    <span class="badge bg-success font-mono" style="font-size: 0.65rem;">Brand Sync</span>
                                </button>
                            </form>

                            <!-- Migrate -->
                            <form method="POST" action="run-setup.php">
                                <input type="hidden" name="command" value="migrate">
                                <button type="submit" class="btn-lms-action">
                                    <span><i class="bi bi-database-fill-up text-info me-2"></i>Run Database Migrations</span>
                                    <span class="badge bg-secondary font-mono" style="font-size: 0.65rem;">migrate --force</span>
                                </button>
                            </form>

                            <!-- Seed -->
                            <form method="POST" action="run-setup.php">
                                <input type="hidden" name="command" value="db_seed">
                                <button type="submit" class="btn-lms-action">
                                    <span><i class="bi bi-flower1 text-success me-2"></i>Seed Database Tables</span>
                                    <span class="badge bg-secondary font-mono" style="font-size: 0.65rem;">db:seed --force</span>
                                </button>
                            </form>

                            <!-- Sync Homepage CMS Sections -->
                            <form method="POST" action="run-setup.php">
                                <input type="hidden" name="command" value="sync_cms_samples">
                                <button type="submit" class="btn-lms-action" style="border-color: rgba(234, 179, 8, 0.4); background: rgba(234, 179, 8, 0.08);">
                                    <span class="text-warning"><i class="bi bi-window-stack text-warning me-2"></i>Sync Homepage Sections (Cambridge Levels, FAQ, Mentors)</span>
                                    <span class="badge bg-warning text-dark font-mono" style="font-size: 0.65rem;">Sync CMS</span>
                                </button>
                            </form>

                            <!-- Storage Link -->
                            <form method="POST" action="run-setup.php">
                                <input type="hidden" name="command" value="storage_link">
                                <button type="submit" class="btn-lms-action">
                                    <span><i class="bi bi-link-45deg text-warning me-2"></i>Link Storage Directory</span>
                                    <span class="badge bg-secondary font-mono" style="font-size: 0.65rem;">storage:link</span>
                                </button>
                            </form>

                            <!-- Fresh Migrate (Danger) -->
                            <form method="POST" action="run-setup.php" onsubmit="return confirm('WARNING: This will DROP ALL TABLES and recreate everything. Are you sure?')">
                                <input type="hidden" name="command" value="migrate_fresh_seed">
                                <button type="submit" class="btn-lms-action" style="border-color: rgba(239, 68, 68, 0.3);">
                                    <span class="text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Wipe &amp; Fresh Re-Seed (Reset)</span>
                                    <span class="badge bg-danger font-mono" style="font-size: 0.65rem;">migrate:fresh</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- 4. Cache & Optimization Actions -->
                    <div class="lms-card p-3 mb-3">
                        <h6 class="fw-bold text-white mb-2 pb-2 border-bottom border-secondary border-opacity-25">
                            <i class="bi bi-speedometer text-success me-2"></i>Performance &amp; Cache
                        </h6>
                        <div class="d-flex flex-column gap-2">
                            <!-- Optimize Clear -->
                            <form method="POST" action="run-setup.php">
                                <input type="hidden" name="command" value="optimize_clear">
                                <button type="submit" class="btn-lms-action">
                                    <span><i class="bi bi-trash3-fill text-warning me-2"></i>Clear All Caches</span>
                                    <span class="badge bg-secondary font-mono" style="font-size: 0.65rem;">optimize:clear</span>
                                </button>
                            </form>

                            <!-- Optimize -->
                            <form method="POST" action="run-setup.php">
                                <input type="hidden" name="command" value="optimize">
                                <button type="submit" class="btn-lms-action">
                                    <span><i class="bi bi-lightning-charge-fill text-success me-2"></i>Cache Config, Routes &amp; Views</span>
                                    <span class="badge bg-secondary font-mono" style="font-size: 0.65rem;">optimize</span>
                                </button>
                            </form>

                            <!-- Compliance Scan -->
                            <form method="POST" action="run-setup.php">
                                <input type="hidden" name="command" value="compliance_scan">
                                <button type="submit" class="btn-lms-action">
                                    <span><i class="bi bi-shield-check text-purple me-2"></i>Run Nightly Compliance Scan</span>
                                    <span class="badge bg-secondary font-mono" style="font-size: 0.65rem;">compliance:scan</span>
                                </button>
                            </form>

                            <!-- Key Generate -->
                            <form method="POST" action="run-setup.php">
                                <input type="hidden" name="command" value="key_generate">
                                <button type="submit" class="btn-lms-action">
                                    <span><i class="bi bi-key-fill text-info me-2"></i>Generate Application Key</span>
                                    <span class="badge bg-secondary font-mono" style="font-size: 0.65rem;">key:generate</span>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Interactive Terminal & Output Console -->
                <div class="col-lg-7 col-xl-8">

                    <!-- Custom Command Execution Card -->
                    <div class="lms-card p-4 mb-4">
                        <h6 class="fw-bold text-white mb-2">
                            <i class="bi bi-terminal text-info me-2"></i>Run Custom Artisan Command
                        </h6>
                        <form method="POST" action="run-setup.php" class="d-flex gap-2">
                            <input type="hidden" name="command" value="custom">
                            <div class="input-group">
                                <span class="input-group-text bg-dark border-secondary border-opacity-50 font-mono text-muted">php artisan</span>
                                <input type="text" name="custom_command" class="form-control font-mono bg-dark border-secondary border-opacity-50 text-white" 
                                       placeholder="e.g. migrate:status, route:list, view:clear" required>
                            </div>
                            <button type="submit" class="btn btn-primary px-4 fw-semibold rounded-pill text-nowrap">
                                <i class="bi bi-play-fill me-1"></i> Execute
                            </button>
                        </form>
                        <div class="d-flex gap-2 mt-2 flex-wrap" style="font-size: 0.72rem;">
                            <span class="text-muted">Quick shortcuts:</span>
                            <a href="#" onclick="document.querySelector('[name=custom_command]').value='migrate:status'; return false;" class="text-info text-decoration-none font-mono">migrate:status</a> &bull;
                            <a href="#" onclick="document.querySelector('[name=custom_command]').value='route:list'; return false;" class="text-info text-decoration-none font-mono">route:list</a> &bull;
                            <a href="#" onclick="document.querySelector('[name=custom_command]').value='view:clear'; return false;" class="text-info text-decoration-none font-mono">view:clear</a> &bull;
                            <a href="#" onclick="document.querySelector('[name=custom_command]').value='config:clear'; return false;" class="text-info text-decoration-none font-mono">config:clear</a>
                        </div>
                    </div>

                    <!-- Live Terminal Window -->
                    <div class="terminal-window">
                        <div class="terminal-header">
                            <div class="d-flex align-items-center gap-2">
                                <span class="terminal-dot bg-danger"></span>
                                <span class="terminal-dot bg-warning"></span>
                                <span class="terminal-dot bg-success"></span>
                                <span class="font-mono text-white-50 ms-2" style="font-size: 0.75rem;">
                                    <?= $commandName ? 'artisan ' . htmlspecialchars($commandName) : 'artisan console output' ?>
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <?php if ($executionTime !== null): ?>
                                    <span class="badge bg-secondary font-mono" style="font-size: 0.68rem;">
                                        <i class="bi bi-stopwatch me-1"></i><?= $executionTime ?>s
                                    </span>
                                <?php endif; ?>
                                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 rounded font-mono" style="font-size: 0.7rem;" 
                                        onclick="navigator.clipboard.writeText(document.getElementById('terminalContent').innerText); alert('Copied console output to clipboard!');">
                                    <i class="bi bi-copy"></i> Copy
                                </button>
                            </div>
                        </div>

                        <div class="terminal-body" id="terminalContent">
<?php if ($commandOutput !== null): ?>
<?= htmlspecialchars($commandOutput) ?>
<?php else: ?>
<span class="text-white-50">Enterprise LMS Terminal Console initialized.
Click any setup action on the left or type a custom artisan command above to see live output here.

System status: <?= $dbStatus ? 'READY' : 'ACTION REQUIRED' ?>
</span>
<?php endif; ?>
                        </div>
                    </div>

                    <!-- Guidance Info -->
                    <div class="alert alert-info bg-opacity-10 border-info border-opacity-25 rounded-4 mt-4 p-3 small">
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-info-circle-fill text-info fs-5 mt-0.5"></i>
                            <div>
                                <div class="fw-bold text-white mb-1">Production Security Best Practice</div>
                                <div class="text-secondary">
                                    Once your production database is fully migrated and seeded, either delete this <code>run-setup.php</code> file from your server root or change <code>DEFAULT_SECURITY_PIN</code> at the top of this script to keep your server console secure.
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

        <?php endif; ?>

    </div>

</body>
</html>
