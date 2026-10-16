<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = \App\Models\Tenant::first();
if (!$tenant) {
    echo "No tenant found\n";
    exit(1);
}

$teachers = \App\Models\User::whereHas('roleAssignments.role', function ($rq) {
    $rq->whereIn('name', [\App\Models\Role::TEACHER, \App\Models\Role::INSTRUCTOR]);
})->get();

$aLevelCat = \App\Models\CourseCategory::where('slug', 'a-level')->first();
$igcseCat = \App\Models\CourseCategory::where('slug', 'igcse')->first();

foreach ($teachers as $t) {
    echo "Found Teacher: {$t->name} (ID {$t->id}) - Status: {$t->approval_status}\n";
    if ($aLevelCat) {
        $t->teachingLevels()->syncWithoutDetaching([$aLevelCat->id]);
        echo "  -> Attached Cambridge A Level\n";
    }
}

echo "Done!\n";
