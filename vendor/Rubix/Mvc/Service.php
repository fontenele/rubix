<?php

namespace Rubix\Mvc;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\Db\Adapter\Adapter;

abstract class Service extends AbstractTableGateway implements AdapterAwareInterface {

    /**
     * DB Owner
     * @var string
     */
    public $owner;
    protected $sm;

    /**
     * Constutor
     */
    public function __construct() {
        $this->init();
    }

    /**
     * Método de inicialização da classe
     */
    abstract public function init();

    /**
     * Set DB Adapter
     * @param Adapter $adapter
     */
    public function setDbAdapter(Adapter $adapter) {
        $this->adapter = $adapter;
    }

    /**
     *
     * @param type $name
     * @return \Rubix\Mvc\Model
     */
    public function getModel($name, $module = null) {
        $className = ucfirst($name);
        $obj = new $className();
        $obj->setDbAdapter($this->getAdapter());
        $obj->owner = $this->owner;

        return $obj;
    }

    /**
     * Get Entity Manager
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        return $this->sm->get('doctrine.entitymanager.orm_default');
    }

    /**
     * Set ServiceManager
     * @param \Zend\ServiceManager\ServiceManager $sm
     */
    public function setServiceManager(ServiceManager $sm) {
        $this->sm = $sm;
    }

}
