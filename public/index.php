<?php

chdir(dirname(__DIR__));

// Debug
require_once('vendor/fdebug.php');

// Setup autoloading
require 'init_autoloader.php';

$httpScheme = $_SERVER['REQUEST_SCHEME'];
if ($_SERVER['SCRIPT_NAME'] == '/index.php') {
    define('APPLICATION_URL', "{$httpScheme}://{$_SERVER['SERVER_NAME']}/");
    define('CONTROLLER_ROUTE_HOST', '');
} else {
    $host = str_replace('/public/index.php', '', $_SERVER['SCRIPT_NAME']);
    define('APPLICATION_URL', "{$httpScheme}://{$_SERVER['HTTP_HOST']}{$host}/");
    define('CONTROLLER_ROUTE_HOST', $host);
}

define('APPLICATION_DIR', dirname(__DIR__) . '/');
define('APPLICATION_LOCKED', true);

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
