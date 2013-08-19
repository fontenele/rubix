<?php

namespace Application\Model;

use FS\Model\Model as BaseModel;

class Imagens extends BaseModel {

    public function configure() {
        $this->_entity = new \FS\Entity\Entity('Application', 'imagens');
    }

}
