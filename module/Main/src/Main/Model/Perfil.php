<?php

namespace Main\Model;

use Rubix\Mvc\Model;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Perfil extends Model {

    public $intCod;
    public $strNome;
    public $intSituacao;

    public function init() {

    }

    public function exchangeArray($data) {
        $this->intCod = (isset($data['intCod'])) ? $data['intCod'] : null;
        $this->strNome = (isset($data['strNome'])) ? $data['strNome'] : null;
        $this->intSituacao = (isset($data['intSituacao'])) ? $data['intSituacao'] : null;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                        'name' => 'intCod',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'Int'),
                        ),
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'strNome',
                        'required' => true,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim'),
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => 1,
                                    'max' => 100,
                                ),
                            ),
                        ),
            )));

            $inputFilter->add($factory->createInput(array(
                        'name' => 'intSituacao',
                        'required' => false,
                        'filters' => array(
                            array('name' => 'Int'),
                        ),
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }

}
