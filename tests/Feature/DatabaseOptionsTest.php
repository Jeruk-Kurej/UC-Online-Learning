<?php

// --- The persistent database connection is a switch that stays off unless the environment turns it on ---

test('the mysql connection is not persistent unless DB_PERSISTENT is set', function () {
    $options = fn () => (require config_path('database.php'))['connections']['mysql']['options'];

    putenv('DB_PERSISTENT');
    expect($options())->not->toHaveKey(PDO::ATTR_PERSISTENT);

    putenv('DB_PERSISTENT=true');
    expect($options()[PDO::ATTR_PERSISTENT] ?? null)->toBeTrue();

    putenv('DB_PERSISTENT=false');
    expect($options())->not->toHaveKey(PDO::ATTR_PERSISTENT);

    putenv('DB_PERSISTENT');
});
