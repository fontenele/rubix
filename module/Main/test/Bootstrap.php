<?php

namespace MainTest;

use Zend\Loader\AutoloaderFactory;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use RuntimeException;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__ . '/../../../');

/**
 * Test bootstrap, for setting up autoloading
 */
class Bootstrap {

    protected static $serviceManager;
    protected static $config;
    protected static $bootstrap;

    public static function init() {
        self::defineConstants();

        if (file_exists('vendor/autoload.php')) {
            $loader = include 'vendor/autoload.php';
        }

        if (file_exists(__DIR__ . '/../TestConfig.php')) {
            $testConfig = include __DIR__ . '/../TestConfig.php';
        }

        if (isset($testConfig['module_listener_options']['module_paths'])) {
            $modulePaths = $testConfig['module_listener_options']['module_paths'];
            foreach ($modulePaths as $modulePath) {
                if (($path = static::findParentPath($modulePath))) {
                    $zf2ModulePaths[] = $path;
                }
            }
        }

        $zf2ModulePaths = implode(PATH_SEPARATOR, $zf2ModulePaths) . PATH_SEPARATOR;
        $zf2ModulePaths .= getenv('ZF2_MODULES_TEST_PATHS') ? : (defined('ZF2_MODULES_TEST_PATHS') ? ZF2_MODULES_TEST_PATHS : '');

        static::initAutoloader();

        // use ModuleManager to load this module and it's dependencies
        $baseConfig = array(
            'module_listener_options' => array(
                'module_paths' => explode(PATH_SEPARATOR, $zf2ModulePaths),
            ),
        );

        $config = ArrayUtils::merge($baseConfig, $testConfig);

        $serviceManager = new ServiceManager(new ServiceManagerConfig());

        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->setFactory('ServiceListener', 'Zend\Mvc\Service\ServiceListenerFactory');

        $serviceManager->setFactory('SessionStorage', function($sm) {
            return new \Rubix\Session\Storage('sesrubixses');
        });
        $serviceManager->setAllowOverride(true);

        //$serviceManager->setService('SessionStorage', 'Zend\Mvc\Service\ServiceListenerFactory');

        $serviceManager->get('ModuleManager')->loadModules();
        static::$serviceManager = $serviceManager;
        static::$config = $config;
    }

    public static function getServiceManager() {
        return static::$serviceManager;
    }

    protected static function initAutoloader() {
        $vendorPath = static::findParentPath('vendor');

        $zf2Path = getenv('ZF2_PATH');
        if (!$zf2Path) {
            if (defined('ZF2_PATH')) {
                $zf2Path = ZF2_PATH;
            } elseif (is_dir($vendorPath . '/ZF2/library')) {
                $zf2Path = $vendorPath . '/ZF2/library';
            } elseif (is_dir($vendorPath . '/zendframework/zendframework/library')) {
                $zf2Path = $vendorPath . '/zendframework/zendframework/library';
            }
        }

        if (!$zf2Path) {
            throw new RuntimeException('Unable to load ZF2. Run `php composer.phar install` or define a ZF2_PATH environment variable.');
        }

        include $zf2Path . '/Zend/Loader/AutoloaderFactory.php';
        AutoloaderFactory::factory(
                array('Zend\Loader\StandardAutoloader' => array(
                        'autoregister_zf' => true,
                        'namespaces' => array(
                            __NAMESPACE__ => __DIR__ . '/' . __NAMESPACE__,
                        )
                    )
                )
        );
    }

    protected static function findParentPath($path) {
        $dir = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }
        return $dir . '/' . $path;
    }

    /**
     * Define constants
     */
    public static function defineConstants() {
        $httpScheme = self::isSSL() ? 'https' : 'http';

        define('APPLICATION_URL', '');
        define('CONTROLLER_ROUTE_HOST', '');
        define('APPLICATION_PATH', dirname(dirname(dirname(__DIR__))) . '/');
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

    public static function debug() {
        $params = func_get_args();
        foreach ($params as $k => $param) {
            echo "\n\n<b style='color: red'># [{$k}] START</b>\n";
            print_r($param);
            echo "\n<b style='color: red'># [{$k}] END</b>\n\n";
        }
        die;
    }

}

Bootstrap::init();
