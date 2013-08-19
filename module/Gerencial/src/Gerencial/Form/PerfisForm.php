<?php

namespace Gerencial\Form;

use FS\View\Form;

class PerfisForm extends Form {

    public function __construct($name = null, $serviceLocator = null, $entity = null) {
        parent::__construct($serviceLocator, $name, null, $entity);
    }

}
