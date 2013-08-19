<?php

namespace Gerador\Controller;

use FS\Controller\Controller;

class ModulosController extends Controller {

    public function init() {
        $this->addBreadcrumb('Módulos', '/gerador/modulos');
    }

    /**
     * Tela principal de entidades do gerador
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction() {
        if ($this->getRequest()->isPost()) {
            $nmModulo = $this->getRequest()->getPost()->nmModulo;
            $nmModuloLower = strtolower($nmModulo);

            $baseDir = APPLICATION_DIR . 'module/' . $nmModulo;

            if (!is_dir($baseDir)) {
                mkdir($baseDir . '/config', 0775, true);
                mkdir($baseDir . "/src/{$nmModulo}/Controller", 0775, true);
                mkdir($baseDir . "/src/{$nmModulo}/Form", 0775, true);
                mkdir($baseDir . "/src/{$nmModulo}/Service", 0775, true);
                mkdir($baseDir . "/src/{$nmModulo}/Model", 0775, true);
                mkdir($baseDir . "/view/{$nmModuloLower}/index", 0775, true);
                mkdir($baseDir . "/xml", 0775, true);
            }

            // Criar module.config.php
            $moduleConfig = file_get_contents(APPLICATION_DIR . 'module/Gerador/config/module.config.tpl.php');
            $moduleConfig = sprintf($moduleConfig, $nmModuloLower, $nmModuloLower, $nmModuloLower, $nmModuloLower);
            file_put_contents("{$baseDir}/config/module.config.php", $moduleConfig);

            // Criar autoload_classmap.php
            $classmap = file_get_contents(APPLICATION_DIR . 'module/Gerador/autoload_classmap.tpl.php');
            file_put_contents("{$baseDir}/autoload_classmap.php", $classmap);

            // Criar Module.php
            $module = file_get_contents(APPLICATION_DIR . 'module/Gerador/Module.tpl.php');
            $module = sprintf($module, $nmModulo, $nmModuloLower, $nmModulo, $nmModulo, $nmModulo);
            file_put_contents("{$baseDir}/Module.php", $module);

            // Criar IndexController.php
            $controller = file_get_contents(APPLICATION_DIR . 'module/Gerador/src/Gerador/Controller/IndexController.tpl.php');
            $controller = sprintf($controller, $nmModulo, 'Página principal do módulo ' . $nmModulo, $nmModulo, $nmModulo, $nmModuloLower);
            file_put_contents("{$baseDir}/src/{$nmModulo}/Controller/IndexController.php", $controller);

            // Criar index.phtml
            file_put_contents("{$baseDir}/view/{$nmModuloLower}/index/index.phtml", "<strong>Bem vindo ao módulo {$nmModulo}!</strong>");

            $this->flashMessenger()->addSuccessMessage("Módulo {$nmModulo} gerado com sucesso.");
            $this->redir('gerador', 'modulos');
        }

        return $this->view;
    }

}
