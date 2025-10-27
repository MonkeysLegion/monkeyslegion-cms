<?php

declare(strict_types=1);

use MonkeysLegion\Cli\CliKernel;
use MonkeysLegion\Framework\HttpBootstrap;

/**
 * MonkeysLegion CLI entry point (php ml).
 */

// 1) Check for vendor/autoload.php
$dir = __DIR__;
$candidate = $dir . '/vendor/autoload.php';
$found = false;

if (file_exists($candidate)) {
    require_once $candidate;
    define('ML_BASE_PATH', $dir);
    $found = true;
}

if (! $found) {
    fwrite(STDERR, "Error: could not find vendor/autoload.php above " . __DIR__ . "\n");
    exit(1);
}

// 2) Load your DI definitions from the project config
$configFile = ML_BASE_PATH . '/config/app.php';
if (! file_exists($configFile)) {
    fwrite(STDERR, "Error: missing config/app.php at {$configFile}\n");
    exit(1);
}


// 3) Bootstrap and run the CLI kernel
$container   = HttpBootstrap::buildContainer(base_path());

/** @var CliKernel $kernel */
$kernel = $container->get(CliKernel::class);
exit($kernel->run($argv));
