<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $unit = \App\Models\Unit::create([
        'nm_lmbg' => 'Test Unit Debug',
        'kode_unit' => 'test_debug',
        'a_aktif' => true,
        'id_creator' => \App\Models\User::first()->UUID ?? null
    ]);
    echo "Success: " . $unit->UUID;
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
