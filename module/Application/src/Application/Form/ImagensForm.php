<?php

namespace Application\Form;

use FS\View\Form;

class ImagensForm extends Form {

    public function __construct($name = null, $serviceLocator = null, $entity = null) {
        parent::__construct($serviceLocator, $name, null, $entity);
        $this->setAttribute('enctype', 'multipart/form-data');
    }

}
