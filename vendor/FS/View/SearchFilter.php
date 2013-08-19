<?php

namespace FS\View;

class SearchFilter {

    protected $items = array();

    /**
     * Configurações da Entidade
     * @var \FS\Entity\Entity
     */
    protected $entity;

    /**
     * Form
     * @var \FS\View\Form
     */
    public $form;

    /**
     * Request
     * @var \Zend\Http\PhpEnvironment\Request
     */
    public $request;

    /**
     *
     * @param \FS\Entity\Entity $entity
     * @param bool $configure
     */
    public function setEntity($entity, $configure = true) {
        $this->entity = $entity;
        if ($configure) {
            $this->configureEntity();
        }
    }

    /**
     * Set form
     * @param \FS\View\Form $form
     */
    public function setForm($form) {
        $this->form = $form;
    }

    /**
     *
     * @param \Zend\Http\PhpEnvironment\Request $request
     */
    public function setRequest($request) {
        $this->request = $request;
        foreach ($this->items as $item) {
            if (trim($request->getPost($item->getAttribute('name')))) {
                $item->setAttribute('value', $request->getPost($item->getAttribute('name')));
            }
        }
    }

    /**
     * 
     */
    protected function configureEntity() {
        $view = $this->entity->getView();

        foreach ($view['items'] as $db => $item) {
            if ((isset($item['type']) && $item['type'] != 'submit') && isset($item['search']) && $item['search'] > 0) {
                $this->items[$item['search']] = $this->form->get($db);
                $this->items[$item['search']]->setAttribute('required', false);
            }
        }

        ksort($this->items);
    }

    public function getItems() {
        return $this->items;
    }

}
