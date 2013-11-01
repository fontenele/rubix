<?php

namespace Main\Form;

use Rubix\Mvc\Form;
use Main\Service\Perfil;

class UsuarioForm extends Form {

    public function configure() {
        
        $this->setName('usuario');
        
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
            'name' => 'strLogin',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control input-3'
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'control-label col-lg-2'
                ),
                'label' => 'Login'
            )
        ));
        $this->add(array(
            'name' => 'strSenha',
            'attributes' => array(
                'type' => 'password',
                'class' => 'form-control input-sm input-3'
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'control-label col-lg-2'
                ),
                'label' => 'Senha'
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
            'name' => 'strEmail',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control input-4'
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'control-label col-lg-2'
                ),
                'label' => 'E-mail'
            )
        ));
        $svcPerfil = $this->getService('perfil');
        $this->add(array(
            'name' => 'intPerfil',
            'type' => 'Zend\Form\Element\Select',
            'attributes' => array(
                'type' => 'text',
                'class' => 'form-control input-4'
            ),
            'options' => array(
                'label_attributes' => array(
                    'class' => 'control-label col-lg-2'
                ),
                'label' => 'Perfil',
                'value_options' => $svcPerfil->perfil2select()
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
