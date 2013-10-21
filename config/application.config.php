<?php

if(!defined('CONTROLLER_ROUTE_HOST')) {
    define('CONTROLLER_ROUTE_HOST', '');
}

if(!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', dirname(__DIR__) . '/');
}

return array(
    'modules' => array(
        'ZendDeveloperTools',
        'DoctrineModule',
        'DoctrineORMModule',
        'BjyProfiler',
        'Main',
        'Rubix',
        'Services',
        'Test',
    ),
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
        ),
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
    ),
);
