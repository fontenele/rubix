<?php

namespace Gerencial\Model;

use FS\Model\Model as BaseModel;

class Usuarios extends BaseModel {

    public function configure() {
        $this->_entity = new \FS\Entity\Entity('Gerencial', 'usuarios');
    }

}
