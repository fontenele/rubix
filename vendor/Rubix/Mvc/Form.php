<?php

namespace Rubix\Mvc;

use Zend\Form\Form as ZForm;
use Zend\ServiceManager\ServiceManager;

abstract class Form extends ZForm {

    protected $sm;

    public function getService($service) {
        $object = $this->sm->get($service);
        $object->setServiceManager($this->sm);
        return $object;
    }

    public function setServiceManager(ServiceManager $sm) {
        $this->sm = $sm;
    }
    
    public function __construct($name = null, $options = array(), $sm = null) {
        parent::__construct($name, $options);
        if($sm) {
            $this->setServiceManager($sm);
        }
        
        $this->configure();
    }
    
    abstract public function configure();

}
