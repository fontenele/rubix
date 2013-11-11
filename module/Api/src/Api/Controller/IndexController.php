<?php

namespace Api\Controller;

use Rubix\Mvc\ControllerRestful as Restful;

class IndexController extends Restful {

    public function init() {

    }

    public function helloAction() {
        $id = $this->getParam('id');
        $name = $this->getParam('name');

        $this->view->id = $id;
        $this->view->name = $name;

        return $this->view;
    }

}
