<?php

namespace Artigos\Service;

use FS\Model\Service;

class TagsService extends Service {

    public function init() {
        $this->entity = new \FS\Entity\Entity('Artigos', 'tags');
        $this->configureEntity();
    }

}