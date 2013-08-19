<?php

namespace Artigos\Service;

use FS\Model\Service;

class ArtigosService extends Service {

    const SITUACAO_ATIVO = 1;
    const SITUACAO_PENDENTE = 2;
    const SITUACAO_INATIVO = 3;

    public function init() {
        $this->entity = new \FS\Entity\Entity('Artigos', 'artigos');
        $this->configureEntity();
    }

    public function prepareForSave($data, $map = null) {
        $data = parent::prepareForSave($data, $map);
        $data['dat_criacao'] = trim($data['dat_criacao']) ? $data['dat_criacao'] : 'now()';
        $data['dat_ultima_edicao'] = 'now()';
        $data['dat_inicio_publicacao'] = \FS\View\Helper\DateFormat::prepareForSave($data['dat_inicio_publicacao']);
        $data['dat_fim_publicacao'] = \FS\View\Helper\DateFormat::prepareForSave($data['dat_fim_publicacao']);
        
        return $data;
    }

}