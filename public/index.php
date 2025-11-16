<?php
/**
 * Simple front controller for Apache when DocumentRoot is set to the `public` folder.
 * It forwards requests to the project's root index.php bootstrap.
 */

// Ensure the file exists one level up
if (! is_file(__DIR__ . '/../index.php')) {
    header('HTTP/1.1 500 Internal Server Error', true, 500);
    echo 'Cannot find application front controller. Please ensure index.php exists in the project root.';
    exit(1);
}

require __DIR__ . '/../index.php';
