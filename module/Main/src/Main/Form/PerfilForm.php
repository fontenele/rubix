<?php

namespace Main\Form;

use Rubix\Mvc\Form;

class PerfilForm extends Form {

    public function configure() {
        $this->setName('perfil');
        $this->setAttribute('method', 'post')
                ->setAttribute('class', 'form-horizontal')
                ->setAttribute('role', 'form');

        $this->add(array(
            'name' => 'intCod',
            'attributes' => array(
                'type' => 'hidden'
            )
        ));
        $this->add(array(
            'name' => 'strNome',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control'
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'control-label col-lg-2'
                ),
                'label' => 'Nome'
            )
        ));
        $this->add(array(
            'name' => 'intSituacao',
            'type' => 'Zend\Form\Element\Checkbox',
            'attributes' => array(
            //'class' => 'form-control'
            ),
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => true,
                'unchecked_value' => false,
                'label_attributes' => array(
                    'class' => 'control-label col-lg-2'
                ),
                'label' => 'Situação'
            )
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'class' => 'btn btn-default',
                'value' => 'Salvar',
                'id' => 'btn-submit'
            )
        ));
    }

}
