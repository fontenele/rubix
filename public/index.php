<?php
/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
if(2==1) { session_start(); session_destroy();die; }
chdir(dirname(__DIR__));
require_once('vendor/fdebug.php');

// Setup autoloading
require 'init_autoloader.php';

define('APPLICATION_URL', "http://$_SERVER[HTTP_HOST]/");
define('APPLICATION_DIR', dirname(__DIR__) . '/');
define('APPLICATION_LOCKED', true);

// Run the application!
Zend\Mvc\Application::init(require 'config/application.config.php')->run();
