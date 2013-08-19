<?php

namespace Gerencial\Service;

use FS\Model\Service;

class AcessosService extends Service {

    public function init() {
        $this->entity = new \FS\Entity\Entity('Gerencial', 'acessos');
        $this->configureEntity();
    }

}