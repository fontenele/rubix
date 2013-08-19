<?php

namespace Artigos\Controller;

use Zend\View\Model\ViewModel;
use FS\Controller\Controller;

/**
 * Página principal do módulo Artigos
 *
 * @package Artigos
 * @subpackage Controller
 * @version 1.0
 * @author Guilherme Fontenele
 * @copyright FonteSolutions
 */
class IndexController extends Controller {

    public function init() {
        $this->addBreadcrumb('Artigos', '/artigos/index');
    }

}
