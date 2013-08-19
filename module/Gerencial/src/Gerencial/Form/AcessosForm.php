<?php

namespace Gerencial\Form;

use FS\View\Form;

class AcessosForm extends Form {

    protected $attributes = array(
        'method' => 'POST',
        'class' => ''
    );

    public function __construct($name = null, $serviceLocator = null, $entity = null) {
        parent::__construct($serviceLocator, $name, null, $entity);
    }

}
