<?php

namespace Gerencial\Model;

use FS\Model\Model as BaseModel;

class Perfis extends BaseModel {

    public function configure() {
        $this->_entity = new \FS\Entity\Entity('Gerencial', 'perfis');
    }

}
