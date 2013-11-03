<?php

namespace Main\Service;

use Rubix\Mvc\Service;

class Perfil extends Service {

    public function init() {

    }

    public function perfil2select() {
        return array('' => 'Selecione') + $this->getEntityManager()->createQueryBuilder()->select('p.intCod as value, p.strNome as label')->from('Main\Entity\Perfis', 'p')->getQuery()->execute();
    }

}
