<?php

namespace FS\View;

class Tree {

    /**
     * Identificador
     * @var string
     */
    protected $id;

    /**
     * Código
     * @var integer
     */
    protected static $cod;

    /**
     * Código do Item Pai
     * @var integer
     */
    protected static $owner;

    /**
     * Dados
     * @var array
     */
    protected $data = array();

    public function __construct($id = null, $codAttribute = 'int_cod', $ownerAttribute = 'int_cod_pai') {
        $this->id = $id;
        self::$cod = $codAttribute;
        self::$owner = $ownerAttribute;
    }

    public function setData($data) {
        $this->data = $data;
    }

    public static function prepare($data) {

        $_tree = array();

        $codAttr = self::$cod;
        $ownerAttr = self::$owner;

        foreach ($data as $_row) {
            $cod = $_row[$codAttr];
            $owner = $_row[$ownerAttr];

            if (!$owner) {
                $_tree[$cod] = array_merge($_row, array('items' => array()));
            } else {
                if (isset($_tree[$owner])) {
                    $_tree[$owner]['items'][$cod] = array_merge($_row, array('items' => array()));
                } else {
                    $_tree = self::iterate($_tree, $_row);
                }
            }
        }

        return $_tree;
    }

    public static function iterate($array, $row) {

        $owner = $row[self::$owner];

        foreach ($array as $_cod => &$_array) {
            if ($_cod == $owner) {
                $_array['items'][$row[self::$cod]] = array_merge($row, array('items' => array()));
            } else {
                if (count($_array['items'])) {
                    $_array['items'] = self::iterate($_array['items'], $row);
                }
            }
        }

        return $array;
    }

    protected function getHtmlString($data) {

        $totalFilhos = 0;
        $html = '';

        foreach ($data as $_cod => $_tree) {
            $html.= "<li id='{$this->id}{$_cod}' data-id='{$_cod}'><a href='#'>{$_tree['str_nome']}</a>";

            if (isset($_tree['items']) && count($_tree['items'])) {

                $html.= "<ul>";
                $html.= $this->getHtmlString($_tree['items']);
                $html.= "</ul></li>";
            }else{
                $html.= "</li>";
            }
        }

        return $html;
    }

    public function __toString() {
        $data = $this->prepare($this->data);
        $content = $this->getHtmlString($data);

        $html = <<<HTML
        <div id="{$this->id}">
            <ul>
                {$content}
            </ul>
        </div>
HTML;

        return $html;
    }

}
