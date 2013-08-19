<?php

namespace Gerencial;

use FS\ModuleManager\Module as BaseModule;

class Module extends BaseModule {

    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function __construct() {

        parent::__construct();

        $this->addInvokableController(array(
            'gerencial' => 'Gerencial\Controller\IndexController',
            'usuarios' => 'Gerencial\Controller\UsuariosController',
            'perfis' => 'Gerencial\Controller\PerfisController',
            'acessos' => 'Gerencial\Controller\AcessosController',
            'menus' => 'Gerencial\Controller\MenusController',
        ));

        $this->addInvokableService(array(
            'gerencial' => 'Gerencial\Service\GerencialService',
            'usuarios' => 'Gerencial\Service\UsuariosService',
            'perfis' => 'Gerencial\Service\PerfisService',
            'acessos' => 'Gerencial\Service\AcessosService',
            'menus' => 'Gerencial\Service\MenusService',
            'menusItems' => 'Gerencial\Service\MenusItemsService',
        ));
    }

}
