<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
$query = App\Models\Submission::whereHas('riwayat')->with('riwayat')->first();
var_dump($query->toArray());
