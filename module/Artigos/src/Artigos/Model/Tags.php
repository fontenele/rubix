<?php

namespace Artigos\Model;

use FS\Model\Model as BaseModel;

class Tags extends BaseModel {

    public function configure() {
        $this->_entity = new \FS\Entity\Entity('Artigos', 'tags');
    }

}