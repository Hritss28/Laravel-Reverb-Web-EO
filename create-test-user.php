<?php

require 'vendor/autoload.php';

use App\Models\User;

// Bootstrap Laravel
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Creating test user..." . PHP_EOL;

$user = User::firstOrCreate(
    ['email' => 'test@test.com'],
    [
        'name' => 'Test User',
        'password' => bcrypt('password')
    ]
);

echo "✅ User created/found: " . $user->email . " (ID: " . $user->id . ")" . PHP_EOL;
echo "🔑 Password: password" . PHP_EOL;
echo "🌐 Login at: http://localhost:8000/login" . PHP_EOL;
