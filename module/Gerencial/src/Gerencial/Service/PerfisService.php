<?php

namespace Gerencial\Service;

use FS\Model\Service;

class PerfisService extends Service {

    const SITUACAO_ATIVO = 1;
    const SITUACAO_INATIVO = 2;

    public function init() {
        $this->entity = new \FS\Entity\Entity('Gerencial', 'perfis');
        $this->configureEntity();
    }

    public function beforeRemove($cod = null, $where = null) {
        $acessos = $this->getService('Gerencial\Service\Acessos');
        $acessos->remove(null, array('int_perfil' => $cod));
        return true;
    }

}
