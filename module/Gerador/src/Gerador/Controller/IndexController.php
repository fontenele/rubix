<?php

namespace Gerador\Controller;

use Zend\View\Model\ViewModel;
use FS\Controller\Controller;

/**
 * Página principal do módulo Gerador
 *
 * @package Gerador
 * @subpackage Controller
 * @version 1.0
 * @author Guilherme Fontenele
 * @copyright FonteSolutions
 */
class IndexController extends Controller {

    public function init() {
        $this->addBreadcrumb('Gerador', '/gerador');
    }

    public function indexAction() {
        $modules = array();
        $models = array();

        $dir = dir(APPLICATION_DIR . 'module');
        $modelsDir = '%s/src/%s/Model/%s.php';

        while (false !== ($_module = $dir->read())) {
            if (!in_array($_module, array('.', '..'))) {
                if ($_module == 'Application') {
                    $modules[] = '';
                } else {
                    $modules[] = $_module;
                }

                if(file_exists(APPLICATION_DIR . "module/{$_module}/src/{$_module}/Model")) {
                    $dirModel = dir(APPLICATION_DIR . "module/{$_module}/src/{$_module}/Model");
                    while (false !== ($_model = $dirModel->read())) {
                        if (!in_array($_model, array('.', '..'))) {
                            $models[$_module][] = substr($_model, 0, strlen($_model) - 4);
                        }
                    }
                }
            }
        }

        $this->view->setVariable('models', $models);
        $this->view->setVariable('modules', $modules);
        return $this->view;
    }

}
