<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== CATEGORIES ===\n";
foreach (\App\Models\CourseCategory::all() as $c) {
    echo "ID: {$c->id} | Name: {$c->name} | Slug: {$c->slug}\n";
}

echo "\n=== DEPARTMENTS ===\n";
foreach (\App\Models\Department::all() as $d) {
    echo "ID: {$d->id} | Name: {$d->name}\n";
}
