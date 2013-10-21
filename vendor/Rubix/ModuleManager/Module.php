<?php

namespace Rubix\ModuleManager;

use Zend\Session\SessionManager;
use Zend\Session\Container;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface, ServiceProviderInterface {

    /**
     * Module config
     * @var array
     */
    public $config;

    /**
     * App config
     * @var array
     */
    public $application;

    /**
     * Controllers invokables
     * @var array
     */
    public $controllers;

    /**
     * Services invokables
     * @var array
     */
    public $services;
    protected $initializers = array();
    protected $invokables = array(
        'controllers' => array('invokables' => array()),
        'services' => array('invokables' => array())
    );

    public function __construct() {

        $this->config = include $this->dir . '/config/module.config.php';
        $this->application = include APPLICATION_PATH . 'config/application.config.php';
        $this->controllers = include APPLICATION_PATH . 'config/autoload/controllers.php';
        $this->services = include APPLICATION_PATH . 'config/autoload/services.php';

        $this->initializers = array(
            function ($instance, $sm) {
                if ($instance instanceof \Zend\Db\Adapter\AdapterAwareInterface) {
                    $instance->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                } elseif ($instance instanceof \Rubix\Mvc\Service) {
                    $instance->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                }

                if (is_object($instance) && property_exists($instance, 'owner')) {
                    $config = $sm->get('config');
                    $instance->owner = $config['db']['owner'];
                }
            }
        );
    }

    public function init(ModuleManager $moduleManager) {
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        $sharedEvents->attach($this->namespace, 'dispatch', function(MvcEvent $e) {
                    $controller = $e->getTarget();
                    $controller->configure();
                    if(isset($controller->view) && $controller->view) {
                        $routeMatch = $e->getRouteMatch();
                        $module = strtolower($routeMatch->getParam('module'));
                        $controller->view->setTemplate("{$module}/{$routeMatch->getParam('controller')}/{$routeMatch->getParam('action')}.phtml");
                    }
                }, 100);
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                $this->dir . '/autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    $this->namespace => $this->dir . '/src/' . $this->namespace
                ),
            ),
        );
    }

    public function onBootstrap(MvcEvent $e) {
        $app = $e->getApplication();

        $eventManager = $app->getEventManager();
        $serviceManager = $app->getServiceManager();

        $moduleRouteListener = new ModuleRouteListener();

        $plugins = $serviceManager->get('ViewHelperManager');
        $plugins->setInvokableClass('BasePath', 'Rubix\View\Helper\BasePath');

        $eventManager->attach(\Zend\Mvc\MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'handleError'), 1);

        $eventManager->attach(
                MvcEvent::EVENT_DISPATCH, function($e) use ($serviceManager, $eventManager) {
                    $routeMatch = $e->getRouteMatch();
                    $viewModel = $e->getViewModel();

                    $boExisteModulo = in_array(strtolower($routeMatch->getParam('module')), array_map(function($item) {
                                        return strtolower($item);
                                    }, $this->application['modules']));

                    if (!$boExisteModulo) {
                        $e->setError(\Zend\Mvc\Application::ERROR_ROUTER_NO_MATCH);
                        $this->handleError($e);
                        die;
                    }

                    $controllersConfig = $this->getControllerConfig();
                    $controllers = $controllersConfig['invokables'];
                    $filterCamel = new \Zend\Filter\Word\DashToCamelCase;
                    $action = lcfirst($filterCamel->filter($routeMatch->getParam('action'))) . 'Action';

                    switch (true) {
                        case!key_exists($routeMatch->getParam('controller'), $controllers):
                        case!class_exists($controllers[$routeMatch->getParam('controller')]):
                            $e->setError(\Zend\Mvc\Application::ERROR_CONTROLLER_NOT_FOUND);
                            $this->handleError($e);
                            break;
                        case $routeMatch->getParam('action') == 'not-found':
                        case!method_exists($controllers[$routeMatch->getParam('controller')], $action):
                            $e->setError(\Zend\Mvc\Application::ERROR_ROUTER_NO_MATCH);
                            $this->handleError($e);
                            break;
                    }

                    $viewModel->setVariable('module', $routeMatch->getParam('module'));
                    $viewModel->setVariable('controller', $routeMatch->getParam('controller'));
                    $viewModel->setVariable('action', $routeMatch->getParam('action'));
                }, -100
        );

        $eventManager->attach(
                MvcEvent::EVENT_ROUTE, function($e) use ($serviceManager, $eventManager) {
                    $routeMatch = $e->getRouteMatch();
                    $viewModel = $e->getViewModel();
                    $boExisteModulo = in_array(strtolower($routeMatch->getParam('module')), array_map(function($item) {
                                        return strtolower($item);
                                    }, $this->application['modules']));

                    if (!$boExisteModulo) {
                        $e->setError(\Zend\Mvc\Application::ERROR_ROUTER_NO_MATCH);
                        $this->handleError($e);
                        die;
                    }

                    $viewModel->setVariable('module', $routeMatch->getParam('module'));
                    $viewModel->setVariable('controller', $routeMatch->getParam('controller'));
                    $viewModel->setVariable('action', $routeMatch->getParam('action'));
                }, -100
        );

        $moduleRouteListener->attach($eventManager);
    }

    public function bootstrapSession($e) {
        $session = $e->getApplication()
                ->getServiceManager()
                ->get('Zend\Session\SessionManager');
        $session->start();

        $container = new Container('initialized');
        if (!isset($container->init)) {
            $session->regenerateId(true);
            $container->init = 1;
        }
    }

    public function handleError(MvcEvent $e) {
        $renderer = $e->getApplication()->getServiceManager()->get('Zend\View\Renderer\PhpRenderer');
        $routeMatch = $e->getRouteMatch() ? $e->getRouteMatch() : $e;

        $result = $e->getResult();
        $response = $e->getResponse();

        $viewVariables = array(
            'reason' => $e->getError(),
            'error' => $routeMatch->getParam('error'),
            'identity' => $routeMatch->getParam('identity'),
        );

        switch ($e->getError()) {
            case \Zend\Mvc\Application::ERROR_CONTROLLER_NOT_FOUND:
            case \Zend\Mvc\Application::ERROR_CONTROLLER_CANNOT_DISPATCH:
            case \Zend\Mvc\Application::ERROR_CONTROLLER_INVALID:
                $viewVariables['module'] = $routeMatch->getParam('module');
                $viewVariables['controller'] = $routeMatch->getParam('controller');
                $viewVariables['action'] = $routeMatch->getParam('action');
                break;
            case \Zend\Mvc\Application::ERROR_ROUTER_NO_MATCH:
                $viewVariables['route'] = $e->getParam('route');
                break;
            case \Zend\Mvc\Application::ERROR_EXCEPTION:
                if (!($e->getParam('exception') instanceof UnAuthorizedException)) {
                    return;
                }

                $viewVariables['reason'] = $e->getParam('exception')->getMessage();
                $viewVariables['error'] = 'error-unauthorized';
                break;
            default:
                /*
                 * do nothing if there is no error in the event or the error
                 * does not match one of our predefined errors (we don't want
                 * our 403 template to handle other types of errors)
                 */
                return;
        }

        $renderer->setVars($viewVariables);
        echo $renderer->partial('error/404.phtml');
        die;
    }

    public function getConfig() {
        return $this->config;
    }

    public function getControllerConfig() {
        return $this->controllers;
    }

    public function getServiceConfig() {
        return array(
            'initializers' => $this->initializers,
            'invokables' => $this->services,
            'factories' => array(
                'Zend\Authentication\AuthenticationService' => function($serviceManager) {
                    return $serviceManager->get('doctrine.authenticationservice.orm_default');
                }
            ),
        );
    }

    protected function addInvokableController($alias, $controller = null) {
        if (is_array($alias)) {
            foreach ($alias as $_alias => $_controller) {
                $this->invokables['controllers']['invokables'][$_alias] = $_controller;
            }
        } else {
            $this->invokables['controllers']['invokables'][$alias] = $controller;
        }
    }

    protected function addInvokableService($alias, $service = null) {
        if (is_array($alias)) {
            foreach ($alias as $_alias => $_service) {
                $this->invokables['services']['invokables'][$_alias] = $_service;
            }
        } else {
            $this->invokables['services']['invokables'][$alias] = $service;
        }
    }

}
