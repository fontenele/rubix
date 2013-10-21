<?php

namespace Rubix\Mvc;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\AdapterAwareInterface;
//use Zend\Db\Sql\TableIdentifier;
use Zend\Db\Adapter\Adapter;

abstract class Service extends AbstractTableGateway implements AdapterAwareInterface {
    /**
     * DB Owner
     * @var string
     */
    public $owner;

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
        /*$this->table = new TableIdentifier($this->tableName, $this->schema);
        $this->resultSetPrototype = new HydratingResultSet();

        $this->initialize();*/
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
}
