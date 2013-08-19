<?php

namespace Gerencial\Service;

use FS\Model\Service;

class MenusItemsService extends Service {

    const TIPO_ITEM = 'item';
    const TIPO_DIVISOR = 'divisor';
    const TIPO_TITULO = 'titulo';
    const TIPO_URL = 'url';

    public function init() {
        $this->entity = new \FS\Entity\Entity('Gerencial', 'menusItems');
        $this->configureEntity();
    }

    public static function prepararMenu($arrMenus, $orderBy = false) {

        $_menu = array();

        foreach ($arrMenus as $_row) {
            if ($_row['int_cod_pai']) {
                $_menu[$_row['int_cod_pai']]['items'][$_row['int_cod']] = $_row;
            } else {
                if (!isset($_menu[$_row['int_cod']])) {
                    $_menu[$_row['int_cod']] = $_row;
                } else {
                    $_menu[$_row['int_cod']] = array_merge_recursive($_row, $_menu[$_row['int_cod']]);
                }
            }
        }

        if ($orderBy) {
            return \FS\Types\ArrayUtil::orderBy('int_posicao', $_menu);
        } else {
            return $_menu;
        }
    }

}
