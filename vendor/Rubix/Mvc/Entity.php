<?php

namespace Rubix\Mvc;

class Entity extends \ArrayObject {

    const GETTER = 'get%s';
    const DOCTRINE_CLASS = 'DoctrineORMModule\Proxy\__CG__\\';

    public function getArrayCopy() {
        $copy = array();
        $reflection = new \ReflectionClass($this);
        $attrs = $reflection->getProperties(\ReflectionProperty::IS_PRIVATE);
        foreach ($attrs as $attr) {
            if ($attr->class == get_class($this)) {
                $method = sprintf(self::GETTER, ucfirst($attr->name));
                $val = $this->$method();
                if (is_object($val) && substr(get_class($val), 0, 31) == self::DOCTRINE_CLASS) {
                    $val = '{' . str_replace(self::DOCTRINE_CLASS, '', get_class($val)) . '}';
                }

                $copy[$attr->name] = $val;
            }
        }
        return $copy;
    }

}