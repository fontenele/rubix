<?php

namespace Gerencial\Service;

use FS\Model\Service;

class MenusService extends Service {

    const DIR_MENU_PRINCIPAL = 'config/menuPrincipal.xml';
    const DIR_MENU_DIREITA = 'config/menuDireita.xml';

    public function init() {
        $this->entity = new \FS\Entity\Entity('Gerencial', 'menus');
        $this->configureEntity();
    }

}
