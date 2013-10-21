<?php

/**
 * Validate
 * ./vendor/bin/doctrine-module orm:validate-schema
 *
 * Create DB
 * ./vendor/bin/doctrine-module orm:schema-tool:create
 *
 * Create Entities
 * ./vendor/bin/doctrine-module orm:convert-mapping --namespace="Main\\Entity\\" --force --from-database annotation ./module/Main/src/
 *
 * Create Setters/Getters
 * ./vendor/bin/doctrine-module orm:generate-entities ./module/Main/src/ --generate-annotations=true
 *
 */

class Rubix {

    /**
     * @var array
     */
    public static $sslPorts = array('443');

    /**
     * Configure application
     */
    public static function configure() {
        // Change dir
        chdir(dirname(__DIR__));

        // Decline CLI Request
        self::declineCliRequest();

        // Do includes
        self::doIncludes();

        // Define constants
        self::defineConstants();
    }

    /**
     * Decline static file requests back to the PHP built-in webserver
     * @return boolean
     */
    private static function declineCliRequest() {
        if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
            return false;
        }
    }

    /**
     * Do includes
     */
    public static function doIncludes() {
        // Load Debugger
        require_once('vendor/fdebug.php');

        // Setup autoloading
        require 'init_autoloader.php';
    }

    /**
     * Define constants
     */
    public static function defineConstants() {
        $httpScheme = self::isSSL() ? 'https' : 'http';
        if ($_SERVER['SCRIPT_NAME'] == '/index.php') {
            define('APPLICATION_URL', "{$httpScheme}://{$_SERVER['SERVER_NAME']}/");
            define('CONTROLLER_ROUTE_HOST', '');
        } else {
            $host = str_replace('/public/index.php', '', $_SERVER['SCRIPT_NAME']);
            define('APPLICATION_URL', "{$httpScheme}://{$_SERVER['HTTP_HOST']}{$host}/");
            define('CONTROLLER_ROUTE_HOST', $host);
        }

        define('APPLICATION_PATH', dirname(__DIR__) . '/');
        define('APPLICATION_LOCKED', false);
    }

    /**
     * Check if is SSL connection
     * @return boolean
     */
    public static function isSSL() {
        switch (true) {
            case isset($_SERVER['HTTPS']) && in_array($_SERVER['HTTPS'], array('on', '1')):
            case isset($_SERVER['SERVER_PORT']) && in_array($_SERVER['SERVER_PORT'], self::$sslPorts):
                return true;
            default:
                return false;
        }
    }

    /**
     * Run the application
     */
    public static function init() {
        // Configure application
        self::configure();

        // Run the application
        Zend\Mvc\Application::init(require 'config/application.config.php')->run();
    }

}

// Run the application
Rubix::init();