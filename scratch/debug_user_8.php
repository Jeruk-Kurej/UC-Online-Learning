<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\User;

try {
    $user = User::findOrFail(8);
    echo "User 8 found: " . $user->name . "\n";
    echo "Loading businesses.products...\n";
    $user->load('businesses.products');
    echo "Loaded successfully.\n";
    foreach ($user->businesses as $business) {
        echo "Business: " . $business->name . " (ID: " . $business->id . ")\n";
        foreach ($business->products as $product) {
            echo "  Product: " . $product->name . "\n";
        }
    }
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
