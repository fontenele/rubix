<?php

namespace FS\Model;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
//use Zend\Validator\NotEmpty;
use FS\Types\ArrayUtil;

/**
 * Model
 *
 * @abstract
 * @property \FS\Model\Service $_service
 * @property \array $_inputFiler
 * @property \array $_validators
 */
abstract class Model implements InputFilterAwareInterface {

    protected $_validators;
    protected $_inputFilter;
    protected $_service;

    /**
     * Configurações da Entidade
     * @var \FS\Entity\Entity
     */
    protected $_entity;

    public function __construct($data = null) {

        $this->_validators['NotEmpty'] = array(
            'name' => 'NotEmpty',
            'options' => array(
                'messages' => array(
                    \Zend\Validator\NotEmpty::IS_EMPTY => 'O campo é obrigatório.'
                )
            )
        );

        $this->_validators['StringLength'] = array(
            'name' => 'StringLength',
            'options' => array(
                'encoding' => 'UTF-8',
                'min' => '%d',
                'max' => '%d',
                'messages' => array(
                    \Zend\Validator\StringLength::TOO_SHORT => "Informe no mínimo %min% caractere(s).",
                    \Zend\Validator\StringLength::TOO_LONG => "Informe no máximo %max% caractere(s).",
                    \Zend\Validator\StringLength::INVALID => "Informe somente caracteres alfanuméricos.",
                )
            )
        );

        $this->_validators['InArray'] = array(
            'name' => 'InArray',
            'options' => array(
                'haystack' => array(),
                'messages' => array(
                    \Zend\Validator\InArray::NOT_IN_ARRAY => 'O campo é obrigatório.'
                )
            )
        );

        $this->configure();

        if ($this->_entity) {
            $service = $this->_entity->getService();
            if (isset($service['name'])) {
                $this->_service = $service['name'];
            }
        }
    }

    protected function getValidator($validator, $args = null) {
        $_validator = array();
        if ($args) {
            if ($validator == 'InArray') {
                $_validator = $this->_validators[$validator];
                //$_validator['options']['haystack'] = $args;
            } else {
                $_validator = ArrayUtil::sprintf($this->_validators[$validator], $args);
            }
        } else {
            $_validator = $this->_validators[$validator];
        }

        return $_validator;
    }

    public function model2db() {
        $object = new $this;
        $service = new $object->_service();

        foreach ($service->getMap() as $classAttribute => $dbAttribute) {
            $object->$dbAttribute = $this->$classAttribute;
        }

        return $object;
    }

    public function getArrayCopy() {
        $copy = get_object_vars($this);
        unset($copy['_entity']);
        unset($copy['_service']);
        unset($copy['_validators']);
        unset($copy['_inputFilter']);
        return $copy;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->_inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $service = $this->_entity->getService();

            foreach ($service['items'] as $_attr => $_item) {

                if (isset($_item['type'])) {
                    $item = array();

                    $item['name'] = $_item['name'];

                    $item['validators'] = array();
                    $item['filters'] = array();

                    if (isset($_item['required']) && $_item['required']) {
                        //$item['validators'][] = $this->getValidator('NotEmpty');
                        $item['required'] = true;
                    } else {
                        $item['required'] = false;
                    }

                    switch ($_item['type']) {
                        case 'int':
                        case 'integer':
                        case 'numeric':
                            $item['filters'][] = array('name' => 'Int');
                            break;
                        case 'richtext':
                            if (isset($_item['length']) && (int) $_item['length'] > 0) {
                                $item['validators'][] = $this->getValidator('StringLength', array(1, (int) $_item['length']));
                            }
                            break;
                        case 'text':
                            $item['filters'][] = array('name' => 'StripTags');
                            $item['filters'][] = array('name' => 'StringTrim');
                            if (isset($_item['length']) && (int) $_item['length'] > 0) {
                                $item['validators'][] = $this->getValidator('StringLength', array(1, (int) $_item['length']));
                            }
                            break;
                        case 'timestamp':
                            $item['filters'][] = array('name' => 'StripTags');
                            $item['filters'][] = array('name' => 'StringTrim');
                            break;
                    }

                    if (count($item['validators']) == 0) {
                        unset($item['validators']);
                    }
                    if (count($item['filters']) == 0) {
                        unset($item['filters']);
                    }

                    $inputFilter->add($factory->createInput($item));
                }
            }

            $this->_inputFilter = $inputFilter;
        }

        return $this->_inputFilter;
    }

    public function exchangeArray($data) {

        $service = new $this->_service;
        $map = array_flip($service->getMap(false));

        if (!is_object($data) && is_array($data)) {
            $data = new \ArrayObject($data);
        }

        foreach ($map as $_attrPost => $_attrModel) {
            if ($data->offsetExists($_attrPost) || $data->offsetExists(substr($_attrPost, 4))) {
                $prefix = substr($_attrPost, 0, 3);
                switch ($prefix) {
                    case 'str':
                        $this->$_attrModel = $data->offsetGet($_attrPost);
                        break;
                    case 'dat':
                        $this->$_attrModel = $data->offsetGet($_attrPost);
                        break;
                    case 'int':
                        $this->$_attrModel = $data->offsetGet($_attrPost) > 0 ? $data->offsetGet($_attrPost) : null;
                        break;
                    case 'aux':
                        if ($data->offsetExists(substr($_attrPost, 4))) {
                            $this->$_attrModel = $data->offsetGet(substr($_attrPost, 4));
                        }
                        break;
                }
            }
        }
    }

    public

    function configure() {

    }

}

