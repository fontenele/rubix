<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config;
use Zend\EventManager\EventManagerAwareInterface;

class ControllerFactory implements FactoryInterface {

    public function createService(ServiceLocatorInterface $serviceLocator) {
        //var_dump($serviceLocator);die;
        /*xd($config = $serviceLocator->get('ApplicationConfig'));
        $service = $serviceLocator->getServiceLocator()
                ->get('User\Service\User');
        $controller = new UserController();
        $controller->setUserService($service);
        return $controller;*/
    }

}

?>
