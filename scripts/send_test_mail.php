<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\Mail::raw('Test email body from CBT app', function ($m) {
        $m->to('hbhnfah@gmail.com')->subject('SMTP test from CBT app');
    });
    echo "Mail sent (no exception thrown)\n";
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
