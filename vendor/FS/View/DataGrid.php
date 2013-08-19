<?php

namespace FS\View;

class DataGrid {

    protected $id;
    public $bordered = true;
    public $hover = true;
    public $condensed = true;
    public $striped = true;
    public $defaultActions = true;
    public $linkEdit;
    public $linkEditArgs = array();
    protected $headers = array();
    protected $footers = array();
    protected $actions = array();
    protected $data = array();
    protected $columnsRenders = array();

    const IMG_CHECKED = '<i class="icon-star"></i>';
    const IMG_UNCHECKED = '<i class="icon-star-empty"></i>';

    /**
     * Configurações da Entidade
     * @var \FS\Entity\Entity
     */
    protected $entity;

    public function __construct($id = null, $defaultActions = true) {
        $this->id = $id;
        if ($defaultActions) {
            $this->defaultActions = $defaultActions;
        }
    }

    public function addHeaders(array $headers = array()) {
        foreach ($headers as $attribute => $_header) {
            //$this->addHeader($attribute, $label);
        }
    }

    public function addHeader($attribute, $label, $position, $size = null, $align = 'left', $type = 'text') {
        $this->headers[$position] = array(
            'attribute' => $attribute,
            'label' => $label,
            'size' => $size,
            'align' => $align,
            'type' => $type,
        );
    }

    public function setEntity($entity, $configure = true) {
        $this->entity = $entity;
        if ($configure) {
            $this->configureEntity();
        }
    }

    protected function configureEntity() {
        $view = $this->entity->getView();

        foreach ($view['items'] as $db => $item) {
            if (isset($item['datagrid']) && $item['datagrid'] > 0) {
                $size = isset($item['datagridsize']) ? $item['datagridsize'] : null;
                $align = isset($item['datagridalign']) ? $item['datagridalign'] : null;
                $type = isset($item['type']) ? $item['type'] : null;
                $this->addHeader($db, $item['label'], $item['datagrid'], $size, $align, $type);
            }
        }

        ksort($this->headers);
    }

    public function setDefaultActions($edit = true, $delete = true) {
        if ($edit) {
            $this->addAction('', 1, $this->linkEdit, $this->linkEditArgs, array(
                'class' => 'btn btn-small btn-inverse',
                'data-placement' => 'left',
                'title' => 'Editar'
                    ), 'edit');
        }
        if ($delete) {
            $this->addAction('', 2, '#', null, array(
                'class' => 'lnk-excluir btn btn-small btn-danger',
                'data-id' => array('%d', array('int_cod')),
                'data-toggle' => 'popover',
                'data-placement' => 'left',
                'title' => 'Excluir'
                    ), 'trash');
        }
    }

    public function addAction($label, $position, $link, $args = array(), $attributes = array(), $icon = null) {
        $this->actions[$position] = array(
            'label' => $label,
            'link' => $link,
            'args' => $args,
            'attributes' => $attributes,
            'icon' => $icon
        );
    }

    public function setData($data) {
        $this->data = $data;
    }

    public function __toString() {

        if ($this->defaultActions) {
            $this->setDefaultActions();
        }

        $view = new \Zend\View\Renderer\PhpRenderer;
        $map = $this->entity->getView();

        $class = 'table ';

        if ($this->bordered) {
            $class.= 'table-bordered ';
        }
        if ($this->condensed) {
            $class.= 'table-condensed ';
        }
        if ($this->hover) {
            $class.= 'table-hover ';
        }
        if ($this->striped) {
            $class.= 'table-striped ';
        }

        $return = "<table class='{$class}'>";
        $return.= "<thead><tr>";

        $headers = $this->headers;
        $actions = $this->actions;

        // add headers
        foreach ($headers as $_header) {
            $return.= "<th class='{$_header['align']}' width='{$_header['size']}'>{$_header['label']}</th>";
        }

        // add header actions
        if (count($actions)) {
            $return.= "<th width='8%'></th>";
        }

        $return.= "</tr></thead><tbody>";

        $data = $this->data;

        // iterating data
        foreach ($data as $_data) {
            $return.= "<tr>";

            // add td data
            foreach ($headers as $_header) {

                $value = $_data[$_header['attribute']];

                if (isset($this->columnsRenders[$_header['attribute']])) {
                    $method = "return {$this->columnsRenders[$_header['attribute']]}(\$_data);";
                    $value = eval($method);
                } elseif (isset($map['items'][$_header['attribute']]['valueoptions'])) {
                    $options = $map['items'][$_header['attribute']]['valueoptions']['option'];

                    if (isset($options[$value]) && isset($options[$value]['label'])) {
                        $value = $options[$value]['label'];
                    }
                } else {

                    $value = $_data[$_header['attribute']];
                    $escapeHtml = true;

                    switch ($_header['type']) {
                        case 'text':
                            break;
                        case 'check':
                        case 'checkbox':
                            $check = new \Zend\Form\Element\Checkbox();
                            $check->setValue($value);
                            $value = $check->isChecked() ? self::IMG_CHECKED : self::IMG_UNCHECKED;
                            $escapeHtml = false;
                            break;
                        case 'datetime':
                            $value = Helper\DateFormat::getDatetime($value);
                            break;
                    }

                    if($escapeHtml) {
                        $value = $view->escapeHtml($value);
                    }
                }

                $return.= "<td class='{$_header['align']}'>{$value}</td>";
            }

            // add td actions
            if (count($actions)) {

                ksort($actions);
                $return.= "<td class='center'>";

                foreach ($actions as $_action) {
                    $link = $_action['link'];
                    $attributes = '';

                    if (count($_action['args'])) {
                        $args = array();

                        foreach ($_action['args'] as $_arg) {
                            $args[] = $_data[$_arg];
                        }

                        $link = vsprintf($_action['link'], $args);
                    }

                    if (count($_action['attributes'])) {

                        foreach ($_action['attributes'] as $_attr => $_value) {
                            $args = array();

                            if (is_array($_value)) {

                                foreach ($_value[1] as $_arg) {
                                    $args[] = $_data[$_arg];
                                }

                                $_value = vsprintf($_value[0], $args);
                            }
                            $attributes.= "{$_attr}='{$_value}' ";
                        }
                    }

                    $icon = $_action['icon'] ? "<i class='icon-{$_action['icon']} icon-white'></i> " : null;
                    $return.= "<a {$attributes} href='{$link}'>{$icon}{$_action['label']}</a> ";
                }

                $return.= "</td>";
            }

            $return.= "</tr>";
        }

        $return.= "</tbody>";
        $return.= "</table>";

        return $return;
    }

    public function setLinkEdit($link, $args = array()) {
        $this->linkEdit = $link;
        $this->linkEditArgs = $args;
    }

    public function setColumnRender($column, $method) {
        $this->columnsRenders[$column] = '\\' . $method;
    }

}
