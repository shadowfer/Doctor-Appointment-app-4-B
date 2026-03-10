<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\Artisan::call('migrate:fresh', ['--seed' => true]);
    echo "Migrate and Seed OK";
} catch (\Exception $e) {
    file_put_contents(__DIR__.'/log_error.txt', "EXCEPTION CAUGHT: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    echo "Exception written to log_error.txt";
}
