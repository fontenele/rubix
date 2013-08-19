<?php

namespace Application\Service;

use FS\Model\Service;

class ImagensService extends Service {

    public function init() {
        $this->entity = new \FS\Entity\Entity('Application', 'imagens');
        $this->configureEntity();
    }

    public function prepareForSave($data, $map = null) {
        $data = parent::prepareForSave($data, $map);
        $data['dat_cadastro'] = 'now()';

        return $data;
    }

}