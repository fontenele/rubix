<?php

namespace Gerencial\Controller;

use Zend\View\Model\ViewModel;
use FS\Controller\Controller;

/**
 * Página principal do painel gerencial
 *
 * @package Gerencial
 * @subpackage Controller
 * @version 1.0
 * @author Guilherme Fontenele
 * @copyright FonteSolutions
 *
 * @property Gerencial\Service\GerencialService $gerencialService
 */
class IndexController extends Controller {

    protected $gerencialService;

    public function init() {
        $menuView = new ViewModel();
        $menuView->setTemplate('gerencial/index/menu_principal.phtml');
        $menuView->controller = $this->params('controller');
        $menuView->action = $this->params('action');
        $menuView->controllerAction = $this->getRequest()->getUri()->getPath();
        $this->view->addChild($menuView, 'menu_principal');
    }

    /**
     * Tela principal do módulo Gerencial
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction() {
        $this->view->setVariable('totais', $this->getGerencialService()->getTotals());
        return $this->view;
    }

    /**
     * Recuperar Gerencial Service
     * @return Gerencial\Service\GerencialService
     */
    public function getGerencialService() {
        if (!$this->gerencialService) {
            $this->gerencialService = $this->getTable('gerencial');
        }
        return $this->gerencialService;
    }

}
