<?php

declare(strict_types=1);

/**
 * Public proxy to the root run-setup.php console
 * Ensures run-setup.php is accessible whether DocumentRoot is set to root or /public
 */

if (file_exists(__DIR__ . '/../run-setup.php')) {
    require_once __DIR__ . '/../run-setup.php';
} else {
    echo "run-setup.php not found in parent directory.";
}
