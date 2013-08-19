<?php

namespace Gerencial\Service;

use FS\Model\Service;

class UsuariosService extends Service {

    public function init() {
        $this->entity = new \FS\Entity\Entity('Gerencial', 'usuarios');
        $this->configureEntity();
    }

    public function prepareForSave($data, $map = null) {
        $data = parent::prepareForSave($data, $map);
        $data['dat_ultimo_login'] = 'now()';
        return $data;
    }

}
