<?php

namespace %s;

use FS\ModuleManager\Module as BaseModule;

class Module extends BaseModule {

    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function __construct() {

        parent::__construct();

        $this->addInvokableController(array(
                '%s' => '%s\Controller\IndexController',
                /* 'nome' => '%s\Controller\NomeController' */
        ));

        $this->addInvokableService(array(
                /* 'nome' => '%s\Service\NomeService' */
        ));
    }

}
