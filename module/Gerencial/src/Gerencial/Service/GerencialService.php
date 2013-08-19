<?php

namespace Gerencial\Service;

use FS\Model\Service;

class GerencialService extends Service {

    public function init() {
        $this->schema = 'gerencial';
    }

    public function getTotals() {
        $sql = <<<SQL
            select
                (select COUNT({$this->schema}.usuarios.int_cod) from {$this->schema}.usuarios) as usuarios,
                (select COUNT({$this->schema}.perfis.int_cod) from {$this->schema}.perfis) as perfis,
                (select COUNT({$this->schema}.menus.int_cod) from {$this->schema}.menus) as menus,
                (select COUNT({$this->schema}.menus_items.int_cod) from {$this->schema}.menus_items) as menus_items,
                (select COUNT({$this->schema}.acessos.int_cod) from {$this->schema}.acessos) as acessos
SQL;

        $stm = $this->getAdapter()->query($sql);

        $return = array();
        foreach ($stm->execute() as $_value) {
            $return = $_value;
        }

        return $return;
    }

}
