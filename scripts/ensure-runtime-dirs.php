<?php

declare(strict_types=1);

$directories = [
    __DIR__ . '/../bootstrap/cache',
    __DIR__ . '/../storage/framework/cache',
    __DIR__ . '/../storage/framework/sessions',
    __DIR__ . '/../storage/framework/views',
];

foreach ($directories as $directory) {
    if (is_dir($directory)) {
        continue;
    }

    if (!mkdir($directory, 0777, true) && !is_dir($directory)) {
        fwrite(STDERR, "Failed to create required directory: {$directory}" . PHP_EOL);
        exit(1);
    }
}
