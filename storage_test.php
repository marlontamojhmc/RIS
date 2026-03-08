<?php

require __DIR__ . '/vendor/autoload.php';

echo "Storage path: " . __DIR__ . '/storage/test.txt' . PHP_EOL;

$file = __DIR__ . '/storage/test.txt';

if (!file_exists(dirname($file))) {
    mkdir(dirname($file), 0755, true);
}

file_put_contents($file, "Hello world!");

echo "File created successfully at $file" . PHP_EOL;