<?php

namespace Rubix\Mvc;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class ControllerTest extends AbstractHttpControllerTestCase {

    protected $traceError = false;

    public function setUp() {
        $this->setApplicationConfig(include APPLICATION_PATH . 'config/application.config.php');
        parent::setUp();
    }

    /**
     *
     * @return \Rubix\Session\Storage
     */
    protected function getSessionStorage() {
        return $this->getServiceLocator()->get('sessionStorage');
    }

    /**
     * @todo revise return
     * @return \Zend\ServiceManager\ServiceManager
     */
    public function getServiceLocator() {
        return \MainTest\Bootstrap::getServiceManager();
    }

}
