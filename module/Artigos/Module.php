<?php

namespace Artigos;

use FS\ModuleManager\Module as BaseModule;

class Module extends BaseModule {

    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function __construct() {

        parent::__construct();

        $this->addInvokableController(array(
                'artigos' => 'Artigos\Controller\IndexController',
                'artigo' => 'Artigos\Controller\ArtigosController',
        ));

        $this->addInvokableService(array(
                'tags' => 'Artigos\Service\TagsService',
                'artigos' => 'Artigos\Service\ArtigosService',
        ));
    }

}
