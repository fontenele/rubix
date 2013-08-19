<?php

namespace Gerencial\Model;

use FS\Model\Model as BaseModel;

class MenusItems extends BaseModel {

    public function configure() {
        $this->_entity = new \FS\Entity\Entity('Gerencial', 'menusItems');
    }

}
