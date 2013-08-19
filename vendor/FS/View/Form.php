<?php

namespace FS\View;

use Zend\Form\Form as BaseForm;

class Form extends BaseForm {

    /**
     * Entity
     * @var \FS\Entity\Entity
     */
    public $entity;
    protected $attributes = array(
        'method' => 'POST',
        'class' => 'form-horizontal'
    );
    protected $serviceLocator;

    public function __construct($serviceLocator = null, $name = null, $options = array(), $entity = null) {
        parent::__construct($name, $options);

        $this->serviceLocator = $serviceLocator;
        $this->entity = $entity;
        $this->configureEntity();
    }

    public function configureEntity() {
        $view = $this->entity->getView();

        foreach ($view['items'] as $_attr => $_item) {
            if (isset($_item['type'])) {
                $element = array();
                $element['id'] = $_attr;
                $element['name'] = isset($_item['name']) ? $_item['name'] : $_attr;
                $element['type'] = $_item['type'];

                if (isset($_item['class'])) {
                    $element['class'] = $_item['class'];
                }

                switch ($_item['type']) {
                    case 'richtext':
                        $element['class'] = isset($element['class']) ? $element['class'] . ' richtext' : 'richtext';
                        $element['type'] = 'textarea';
                        if (isset($_item['label'])) {
                            $element['label'] = $_item['label'];
                        }
                        break;
                    case 'textarea':
                        $element['type'] = 'textarea';
                        if (isset($_item['label'])) {
                            $element['label'] = $_item['label'];
                        }
                        break;
                    case 'hidden':

                        break;
                    case 'submit':
                        if (isset($_item['label'])) {
                            $element['value'] = $_item['label'];
                        }
                        break;
                    case 'datetime':
                        if (isset($_item['label'])) {
                            $element['label'] = $_item['label'];
                        }
                        if (isset($_item['hint'])) {
                            $element['hint'] = $_item['hint'];
                        }
                        if (isset($_item['required'])) {
                            $element['required'] = $_item['required'];
                        }
                        if (isset($_item['hintview'])) {
                            $element['hint_trigger'] = $_item['hintview'];
                        }
                        $element['class'] = isset($element['class']) ? $element['class'] . ' datetime input-medium' : 'datetime input-medium';
                        $element['type'] = 'text';
                        $element['data-mask'] = '99/99/9999 99:99';
                        break;
                    case 'checkbox':
                    case 'check':
                        $element['type'] = 'checkbox';
                        if (isset($_item['label'])) {
                            $element['label'] = $_item['label'];
                        }
                        if (isset($_item['hint'])) {
                            $element['hint'] = $_item['hint'];
                        }
                        if (isset($_item['required'])) {
                            $element['required'] = $_item['required'];
                        }
                        if (isset($_item['hintview'])) {
                            $element['hint_trigger'] = $_item['hintview'];
                        }
                        break;
                    case 'image':
                        if (isset($_item['label'])) {
                            $element['label'] = $_item['label'];
                        }
                        if (isset($_item['required'])) {
                            $element['required'] = $_item['required'];
                        }
                        break;
                    case 'email':
                    case 'password':
                    case 'text':
                    case 'select':
                    case 'radio': /** @todo Implementar componente radio */
                        if (isset($_item['label'])) {
                            $element['label'] = $_item['label'];
                        }
                        if (isset($_item['hint'])) {
                            $element['hint'] = $_item['hint'];
                        }
                        if (isset($_item['placeholder'])) {
                            $element['placeholder'] = $_item['placeholder'];
                        }
                        if (isset($_item['required'])) {
                            $element['required'] = $_item['required'];
                        }
                        if (isset($_item['maxlength'])) {
                            $element['maxlength'] = $_item['maxlength'];
                        }
                        if (isset($_item['hintview'])) {
                            $element['hint_trigger'] = $_item['hintview'];
                        }
                        switch ($_item['type']) {
                            case 'select':
                                if (isset($_item['valueoptions'])) {
                                    $options = array();
                                    $valueOptions = $_item['valueoptions'];

                                    if (isset($valueOptions['option'])) {
                                        if (isset($valueOptions['option']['label'])) {
                                            $options[$valueOptions['option']['value']] = $valueOptions['option']['label'];
                                        } else {
                                            foreach ($valueOptions['option'] as $_attrOption => $_option) {
                                                $index = isset($_option['value']) ? $_option['value'] : null;
                                                $options[$index] = $_option['label'];
                                            }
                                        }
                                    }

                                    if (isset($valueOptions['caller'])) {
                                        $caller = $valueOptions['caller'];
                                        $args = '';
                                        if (isset($valueOptions['args'])) {
                                            if (is_array($valueOptions['args']['arg'])) {
                                                foreach ($valueOptions['args']['arg'] as $_i => $_arg) {
                                                    if ($_i != 0) {
                                                        $args.= ",";
                                                    }
                                                    $args.= "'{$_arg}'";
                                                }
                                            } else {
                                                $args.= "'{$valueOptions['args']['arg']}'";
                                            }
                                        }
                                        $tmp = eval("return {$caller}({$args});");
                                        $options = $options + $tmp;
                                    }

                                    $element['value_options'] = $options;
                                }
                                break;
                        }
                        break;
                }
                $this->add($element);
            }
        }
    }

    public function add($elementOrFieldset, array $flags = array()) {
        if (is_array($elementOrFieldset)) {
            $element = array(
                'name' => $elementOrFieldset['name'],
                'attributes' => array('type' => $elementOrFieldset['type']),
                'options' => array(),
            );

            if (isset($elementOrFieldset['label']) && $elementOrFieldset['label']) {
                $element['options']['label'] = $elementOrFieldset['label'];
                $element['options']['label_attributes'] = array(
                    'class' => 'control-label',
                    'for' => $elementOrFieldset['name']
                );
            }
            if (isset($elementOrFieldset['required']) && $elementOrFieldset['required']) {
                $element['attributes']['required'] = (bool) $elementOrFieldset['required'];
                if (isset($element['options']['label_attributes'])) {
                    $element['options']['label_attributes']['required'] = 'required';
                } else {
                    $element['options']['label_attributes'] = array('required' => 'required');
                }
            }

            if (isset($elementOrFieldset['hint']) && $elementOrFieldset['hint']) {
                $element['options']['hint'] = $elementOrFieldset['hint'];
                $element['attributes']['title'] = $elementOrFieldset['hint'];
                $element['attributes']['data-trigger'] = isset($elementOrFieldset['hint_trigger']) ? $elementOrFieldset['hint_trigger'] : 'hover';
            }

            if (isset($elementOrFieldset['class']) && $elementOrFieldset['class']) {
                $element['attributes']['class'] = isset($element['attributes']['class']) ? $element['attributes']['class'] . $elementOrFieldset['class'] : $elementOrFieldset['class'];
            }

            switch ($elementOrFieldset['type']) {
                case 'hidden':
                    if (isset($elementOrFieldset['id']) && $elementOrFieldset['id']) {
                        $element['attributes']['id'] = $elementOrFieldset['id'];
                    }
                    break;
                case 'password':
                case 'text':
                    if (isset($elementOrFieldset['id']) && $elementOrFieldset['id']) {
                        $element['attributes']['id'] = $elementOrFieldset['id'];
                    }
                    if (isset($elementOrFieldset['maxlength']) && $elementOrFieldset['maxlength']) {
                        $element['attributes']['maxlength'] = $elementOrFieldset['maxlength'];
                    }
                    break;
                case 'select':
                    $element['type'] = 'Zend\Form\Element\Select';

                    if (isset($elementOrFieldset['id']) && $elementOrFieldset['id']) {
                        $element['attributes']['id'] = $elementOrFieldset['id'];
                    }
                    if (isset($elementOrFieldset['value_options']) && is_array($elementOrFieldset['value_options'])) {
                        $element['options']['value_options'] = $elementOrFieldset['value_options'];
                    }
                    break;
                case 'check':
                case 'checkbox':
                    $element['type'] = 'Zend\Form\Element\Checkbox';
                    if (isset($elementOrFieldset['id']) && $elementOrFieldset['id']) {
                        $element['attributes']['id'] = $elementOrFieldset['id'];
                    }
                    //x($element);
                    break;
                case 'submit':
                    $element['attributes']['class'] = 'btn ' . $element['attributes']['class'];
                    if (isset($elementOrFieldset['id']) && $elementOrFieldset['id']) {
                        $element['attributes']['id'] = $elementOrFieldset['id'];
                    }
                    break;
            }

            foreach($elementOrFieldset as $attr => $val) {
                if(substr($attr, 0, 5) == 'data-') {
                    $element['attributes'][$attr] = $val;
                }
            }

            $elementOrFieldset = $element;
        }

        parent::add($elementOrFieldset, $flags);
    }

    public function populateValues($data) {
        foreach ($data as $key => $row) {
            if (is_array(@json_decode($row))) {
                $data[$key] = new \ArrayObject(\Zend\Json\Json::decode($row), \ArrayObject::ARRAY_AS_PROPS);
            }
        }

        parent::populateValues($data);
    }

    public function setValues($object, $flags = null) {
        return parent::bind($object->model2db());
    }

    public function getAttributeArray($table, $indexField, $valueField) {
        if (!$this->serviceLocator) {
            throw new \Zend\Form\Exception\InvalidArgumentException('ServiceLocator não definido.');
        }
        return $this->serviceLocator->get($table)->getAttributeArray($indexField, $valueField);
    }

}
