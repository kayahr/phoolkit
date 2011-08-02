<?php

use PhoolKit\Request;

// Install class autoloader.
spl_autoload_register(function($className)
{
    $className = preg_replace("/\\\\/", "/", $className);
    return include_once "$className.php";
});

$baseDir = dirname(__FILE__);

// Setup the include path so the example can be directly called from the
// browser.
set_include_path(
    "$baseDir/lib" . PATH_SEPARATOR .
    "$baseDir/../../src" . PATH_SEPARATOR .
    get_include_path());

// Set the base directory
Request::setBaseDir($baseDir);

// Start the session
session_start();