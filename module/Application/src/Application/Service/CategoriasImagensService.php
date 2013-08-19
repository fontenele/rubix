<?php

namespace Application\Service;

use FS\Model\Service;

class CategoriasImagensService extends Service {

    public function init() {
        $this->entity = new \FS\Entity\Entity('Application', 'categoriasImagens');
        $this->configureEntity();
    }

    public static function prepararCategorias($arrCategorias) {

        $_categorias = array();

        foreach ($arrCategorias as $_row) {
            $cod = $_row['int_cod'];
            $pai = $_row['int_cod_pai'];

            if (!$pai) {
                $_categorias[$cod] = array_merge($_row, array('items' => array()));
            } else {
                if (isset($_categorias[$pai])) {
                    $_categorias[$pai]['items'][$cod] = array_merge($_row, array('items' => array()));
                } else {
                    $_categorias = self::iterarCategorias($_categorias, $_row);
                }
            }
        }

        return $_categorias;
    }

    public static function iterarCategorias($arrCategorias, $row) {

        $pai = $row['int_cod_pai'];

        foreach ($arrCategorias as $_cod => &$_categoria) {
            if ($_cod == $pai) {
                $_categoria['items'][$row['int_cod']] = array_merge($row, array('items' => array()));
            } else {
                if (count($_categoria['items'])) {
                    $_categoria['items'] = self::iterarCategorias($_categoria['items'], $row);
                }
            }
        }

        return $arrCategorias;
    }

}