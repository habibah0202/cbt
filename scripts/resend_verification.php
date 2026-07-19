<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

if ($argc < 2) {
    echo "Usage: php scripts/resend_verification.php user@example.com\n";
    exit(1);
}

$email = $argv[1];
$user = App\Models\User::where('email', $email)->first();
if (! $user) {
    echo "User not found: $email\n";
    exit(1);
}

try {
    $user->sendEmailVerificationNotification();
    echo "Verification notification dispatched to $email\n";
} catch (Exception $e) {
    echo "Exception sending verification: " . $e->getMessage() . "\n";
}
