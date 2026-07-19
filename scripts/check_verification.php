<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$user = App\Models\User::where('email', 'hbhnfah@gmail.com')->first();
if (! $user) {
    echo "user not found\n";
    exit(1);
}
echo $user->hasVerifiedEmail() ? 'verified' : 'not verified';
