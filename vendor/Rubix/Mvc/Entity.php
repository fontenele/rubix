<?php

namespace Rubix\Mvc;

use Zend\ServiceManager\ServiceManager;

abstract class Entity extends \ArrayObject {

    protected $sm;
    protected $inputFilter;

    const GETTER = 'get%s';
    const DOCTRINE_CLASS = 'DoctrineORMModule\Proxy\__CG__\\';

    public function getArrayCopy($filter = true) {
        $copy = array();
        $reflection = new \ReflectionClass($this);
        $attrs = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
        foreach ($attrs as $attr) {
            if ($attr->class == get_class($this)) {
                $method = sprintf(self::GETTER, ucfirst($attr->name));
                $val = $this->$method();
                if ($filter && is_object($val) && substr(get_class($val), 0, 31) == self::DOCTRINE_CLASS) {
                    $_obj = new \stdClass();
                    $_obj->id = $val->{$this->getId()}();
                    $_obj->desc = $val->{$this->getDesc()}();
                    $val = $_obj;
                }

                $copy[$attr->name] = $val;
            }
        }
        return $copy;
    }

    /**
     * Set Entity Manager
     * @param \Zend\ServiceManager\ServiceManager $sm
     */
    public function setServiceManager(ServiceManager $sm) {
        $this->sm = $sm;
    }

    /**
     * Get Entity Manager
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        if(!$this->sm) {
            throw new \Exception('ServiceManager not found. (' . get_class($this) . '::getEntityManager())');
        }
        return $this->sm->get('doctrine.entitymanager.orm_default');
    }

    public function getId() {
        return $this->id;
    }

    public function getDesc() {
        return $this->desc;
    }

}
