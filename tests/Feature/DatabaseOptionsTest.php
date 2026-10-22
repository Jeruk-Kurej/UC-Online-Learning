<?php

// --- The persistent database connection is a switch that stays off unless the environment turns it on ---

function setDbPersistent(string|false|null $value): void
{
    if ($value === null || $value === false) {
        putenv('DB_PERSISTENT');
        unset($_ENV['DB_PERSISTENT'], $_SERVER['DB_PERSISTENT']);

        return;
    }

    putenv('DB_PERSISTENT='.$value);
    $_ENV['DB_PERSISTENT'] = $_SERVER['DB_PERSISTENT'] = $value;
}

test('the mysql connection is not persistent unless DB_PERSISTENT is set', function () {
    $options = fn () => (require config_path('database.php'))['connections']['mysql']['options'];
    $original = $_SERVER['DB_PERSISTENT'] ?? getenv('DB_PERSISTENT');

    try {
        setDbPersistent(null);
        expect($options())->not->toHaveKey(PDO::ATTR_PERSISTENT);

        setDbPersistent('true');
        expect($options()[PDO::ATTR_PERSISTENT] ?? null)->toBeTrue();

        setDbPersistent('false');
        expect($options())->not->toHaveKey(PDO::ATTR_PERSISTENT);
    } finally {
        setDbPersistent($original);
    }
});
