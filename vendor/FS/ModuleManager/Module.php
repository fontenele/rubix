<?php

namespace FS\ModuleManager;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

class Module implements AutoloaderProviderInterface {

    public $config;
    protected $initializers = array();
    protected $invokables = array(
        'controllers' => array('invokables' => array()),
        'services' => array('invokables' => array())
    );

    public function __construct() {
        $this->config = include $this->dir . '/config/module.config.php';
        $this->initializers = array(
            function ($instance, $sm) {
                if ($instance instanceof \Zend\Db\Adapter\AdapterAwareInterface) {
                    $instance->setDbAdapter($sm->get('Zend\Db\Adapter\Adapter'));
                }
            }
        );
    }

    public function init(ModuleManager $moduleManager) {
        $sharedEvents = $moduleManager->getEventManager()->getSharedManager();
        $sharedEvents->attach($this->namespace, 'dispatch', function($e) {
                    /* $result = $e->getResult();
                      if ($result instanceof \Zend\View\Model\ViewModel) {
                      $result->setTerminal($e->getRequest()->isXmlHttpRequest());
                      //if you want no matter request is, the layout is disabled, you can
                      //set true : $result->setTerminal(true);
                      } */
                    $controller = $e->getTarget();
                    $controller->configure();
                }, 100);
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                $this->dir . '/autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    $this->namespace => $this->dir . '/src/' . $this->namespace,
                ),
            ),
        );
    }

    public function onBootstrap(MvcEvent $e) {
        $app = $e->getApplication();

        $serviceManager = $app->getServiceManager();
        $serviceManager->get('translator');

        $plugins = $serviceManager->get('ViewHelperManager');
        $plugins->setInvokableClass('FormRow', 'FS\View\Helper\FormRow');
        $plugins->setInvokableClass('FormSubmit', 'FS\View\Helper\FormSubmit');
        $plugins->setInvokableClass('FormLabel', 'FS\View\Helper\FormLabel');

        $eventManager = $app->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager->attach(
                'dispatch', function($e) {
                    $routeMatch = $e->getRouteMatch();
                    $viewModel = $e->getViewModel();
                    $viewModel->setVariable('controller', $routeMatch->getParam('controller'));
                    $viewModel->setVariable('action', $routeMatch->getParam('action'));
                }, -100
        );
    }

    public function getConfig() {
        return $this->config;
    }

    public function getControllerConfig() {
        return $this->invokables['controllers'];
    }

    public function getServiceConfig() {
        return $this->invokables['services'];
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
