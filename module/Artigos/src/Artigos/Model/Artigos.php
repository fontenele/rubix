<?php

namespace Artigos\Model;

use FS\Model\Model as BaseModel;

class Artigos extends BaseModel {

    public function configure() {
        $this->_entity = new \FS\Entity\Entity('Artigos', 'artigos');
    }

}