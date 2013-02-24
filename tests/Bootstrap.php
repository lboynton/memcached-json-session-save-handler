<?php

error_reporting( E_ALL | E_STRICT );

$root        = realpath(dirname(__DIR__));
$coreLibrary = "$root/library";
$coreTests   = "$root/tests";

$path = array(
    $coreLibrary,
    $coreTests,
    get_include_path(),
);

set_include_path(implode(PATH_SEPARATOR, $path));

/**
 * Setup autoloading
 */
include __DIR__ . '/_autoload.php';

/*
 * Load the user-defined test configuration file, if it exists; otherwise, load
 * the default configuration.
 */
if (is_readable($coreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
    require_once $coreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php';
} else {
    require_once $coreTests . DIRECTORY_SEPARATOR . 'TestConfiguration.php.dist';
}

/*
 * Unset global variables that are no longer needed.
 */
unset($root, $coreLibrary, $coreTests, $path);
