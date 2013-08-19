<?php

namespace FS\Controller;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;
use FS\Permissions\Acl;

abstract class Controller extends AbstractActionController {

    /**
     * Deve ou não mostrar a barra do topo
     * @var bool
     */
    public $booMostrarBarraTopo;

    /**
     * Deve ou não mostrar o menu principal
     * @var bool
     */
    public $booMostrarMenuPrincipal;

    /**
     * Deve ou não mostrar o menu lateral
     * @var bool
     */
    public $booMostrarMenuDireita;

    /**
     * Deve ou não mostrar o rodapé
     * @var bool
     */
    public $booMostrarRodape;

    /**
     *
     * @var array
     */
    public $lstJavascript = array();

    /**
     *
     * @var array
     */
    public $lstExtraJavascript = array();

    /**
     *
     * @var array
     */
    public $lstCss = array();

    /**
     *
     * @var ViewModel
     */
    public $view;

    /**
     *
     * @var array
     */
    public $breadcrumbs = array();

    /**
     *
     * @var Acl
     */
    protected static $acl;

    /**
     * Configurações da Entidade
     * @var \FS\Entity\Entity
     */
    protected $entity;

    /**
     *  Método de inicialização
     */
    abstract public function init();

    /**
     * Construtor
     */
    public function __construct() {

    }

    /**
     *
     */
    private function configureAcl() {
        if (!self::$acl) {
            $objUsuario = $this->getAuthStorage()->read();
            $codPerfil = null;
            $_where = array();

            if ($objUsuario) {
                $objUsuario = unserialize($objUsuario);
                $codPerfil = $objUsuario->perfil;
                $_where['perfis.int_cod'] = $codPerfil;
            } else {
                $_where['perfis.int_cod'] = Acl::ACL_COD_PERFIL_CONVIDADO;
            }

            if (!$this->getSessionStorage()->has('acessos') || count($this->getSessionStorage()->get('acessos')) == 0) {
                $acessos = array();
                foreach ($this->getService('acessos')->fetchAll($_where) as $_acesso) {
                    $_tmp = explode('::', $_acesso['str_nome_acesso']);
                    $acessos[$_tmp[0]][$_tmp[1]] = $_tmp[1];
                }
                //$this->getSessionStorage()->add('acessos', $acessos);
            } else {
                $acessos = $this->getSessionStorage()->get('acessos');
            }

            $acl = new Acl();
            $acl->setPerfil($codPerfil);
            $acl->setAcessos($acessos);

            self::$acl = $acl;
        }
    }

    /**
     *
     */
    final public function configure() {
        $this->booMostrarBarraTopo = true;
        $this->booMostrarMenuPrincipal = true;
        $this->booMostrarMenuDireita = true;
        $this->booMostrarRodape = true;

        $this->configureAcl();

        if (!$this->isAllowed()) {
            $this->flashmessenger()->addErrorMessage('Acesso negado.');
            $this->redirect()->toRoute('home'); //@todo caso esteja no modulo gerencial, redirecionar para /gerencial ou HTTP_Referer
        }

        $this->view = new ViewModel();

        $plugin = $this->plugin('url');
        $this->addBreadcrumb($this->getModuleName() == 'Application' ? '' : $this->getModuleName(), $plugin->fromRoute(strtolower($this->getModuleName())));

        $this->init();

        /** @todo configurar caso seja requisicao json, xml, pdf, senão prosseguir */
        $this->configureView();
    }

    /**
     *
     */
    protected function configureView() {
        $layout = $this->layout();

        $layout->strClasseBarraTopo = $this->getModuleName() == 'Gerencial' ? 'navbar-inverse' : '';

        $layout->booMostrarBarraTopo = $this->booMostrarBarraTopo;
        $layout->booMostrarMenuPrincipal = $this->booMostrarMenuPrincipal;
        $layout->booMostrarMenuDireita = $this->booMostrarMenuDireita;
        $layout->booMostrarRodape = $this->booMostrarRodape;

        if ($this->getRequest()->getRequestUri() != '/') {
            $_arrRequest = explode('/', $this->getRequest()->getRequestUri());
            array_shift($_arrRequest);
            if (count($_arrRequest)) {
                $_strJavascript = '';
                for ($i = 0; $i < count($_arrRequest); $i++) {
                    if ($i == 3) {
                        break;
                    } elseif ($i != 0 && !($_arrRequest[$i] > 0)) {
                        $_strJavascript.= '/';
                    }
                    if (!($_arrRequest[$i] > 0)) {
                        $_strJavascript.= "{$_arrRequest[$i]}";
                    }
                }
                if ($_arrRequest[$i - 1] != $this->params('action')/* && $this->params('action') == 'index' */) {
                    $_strJavascript.= "/{$this->params('action')}";
                }
                $_strJavascript.= '.js';

                $_strCss = '';
                for ($i = 0; $i < count($_arrRequest); $i++) {
                    if ($i == 3) {
                        break;
                    } elseif ($i != 0 && !($_arrRequest[$i] > 0)) {
                        $_strCss.= '/';
                    }
                    if (!($_arrRequest[$i] > 0)) {
                        $_strCss.= "{$_arrRequest[$i]}";
                    }
                }
                if ($_arrRequest[$i - 1] != $this->params('action')/* && $this->params('action') == 'index' */) {
                    $_strCss.= "/{$this->params('action')}";
                }
                $_strCss.= '.css';

                if (file_exists("public/js/modules/{$_strJavascript}")) {
                    $this->lstJavascript[] = $_strJavascript;
                }

                if (file_exists("public/css/modules/{$_strCss}")) {
                    $this->lstCss[] = $_strCss;
                }
            }
        } else {
            // home
            if (file_exists("public/js/modules/application/index/index.js")) {
                $this->lstJavascript[] = "application/index/index.js";
            }

            if (file_exists("public/css/modules/application/index/index.css")) {
                $this->lstCss[] = "application/index/index.css";
            }
        }

        $layout->lstJavascripts = $this->lstJavascript;
        $layout->lstExtraJavascripts = $this->lstExtraJavascript;
        $layout->lstCss = $this->lstCss;

        $this->view->controller = $this->params('controller');
        $this->view->action = $this->params('action');
        $this->view->controllerAction = $this->getRequest()->getUri()->getPath();

        $layout->mensagensErro = $this->flashmessenger()->getErrorMessages();
        $layout->mensagensSucesso = $this->flashmessenger()->getSuccessMessages();

        if ($this->booMostrarMenuPrincipal) {
            $menuView = new ViewModel();
            $menuView->setTemplate('layout/menu_principal.phtml');
            //$menuView->controller = $this->params('controller');
            //$menuView->action = $this->params('action');

            if (!$this->getSessionStorage()->has('menu_principal')) {
                $cfgMenu = new \Zend\Config\Config(array(), true);
                $reader = new \Zend\Config\Reader\Xml();
                $arrMenusItems = $reader->fromFile(APPLICATION_DIR . \Gerencial\Service\MenusService::DIR_MENU_PRINCIPAL);

                $this->getSessionStorage()->add('menu_principal', $arrMenusItems);
            } else {
                $arrMenusItems = $this->getSessionStorage()->get('menu_principal');
            }

            $menuView->menusItems = $arrMenusItems;
            $menuView->controllerAction = $this->getRequest()->getUri()->getPath();
            $layout->addChild($menuView, 'menu_principal');
        }
        if ($this->booMostrarMenuDireita) {
            $menuView = new ViewModel();
            $menuView->setTemplate('layout/menu_direita.phtml');
            //$menuView->controller = $this->params('controller');
            //$menuView->action = $this->params('action');
            $menuView->controllerAction = $this->getRequest()->getUri()->getPath();

            if (!$this->getSessionStorage()->has('menu_direita')) {
                $cfgMenu = new \Zend\Config\Config(array(), true);
                $reader = new \Zend\Config\Reader\Xml();
                $arrMenusDireita = $reader->fromFile(APPLICATION_DIR . \Gerencial\Service\MenusService::DIR_MENU_DIREITA);

                $this->getSessionStorage()->add('menu_direita', $arrMenusDireita);
            } else {
                $arrMenusDireita = $this->getSessionStorage()->get('menu_direita');
            }
            $objUsuario = $this->getAuthStorage()->read();
            if ($objUsuario) {
                $objUsuario = unserialize($objUsuario);
            }

            $menuView->menusItems = $arrMenusDireita;
            $menuView->booLogado = $objUsuario ? true : false;
            $menuView->strUsuarioLogado = $objUsuario ? $objUsuario->nome : '';

            $layout->addChild($menuView, 'menu_direita');
        }
    }

    /**
     * Execute the request
     *
     * @param  MvcEvent $e
     * @return mixed
     * @throws Exception\DomainException
     */
    protected function attachDefaultListeners() {
        parent::attachDefaultListeners();

        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'afterExecuteAction'));
    }

    /**
     *
     */
    public function afterExecuteAction() {
        $this->setHeadTitle();
        $this->setBreadcrumbs();
    }

    /**
     *
     */
    protected function setHeadTitle() {
        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        $renderer->headTitle(implode(' &rsaquo; ', array_keys($this->breadcrumbs)));
    }

    /**
     *
     */
    protected function setBreadcrumbs() {
        $layout = $this->layout();
        $layout->breadcrumbs = $this->breadcrumbs;
    }

    /**
     *
     * @return type
     */
    protected function getAuthService() {
        return $this->getServiceLocator()->get('authService');
    }

    /**
     *
     * @return type
     */
    protected function getAuthStorage() {
        return $this->getServiceLocator()->get('authStorage');
    }

    /**
     *
     * @return type
     */
    protected function getSessionStorage() {
        return $this->getServiceLocator()->get('sessionStorage');
    }

    /**
     *
     * @param type $message
     * @param type $textDomain
     * @param type $locale
     * @return type
     */
    protected function translate($message, $textDomain = 'default', $locale = null) {
        return $this->getServiceLocator()->get('translator')->translate($message, $textDomain, $locale);
    }

    /**
     *
     * @return type
     */
    protected function isAllowed() {
        if (APPLICATION_LOCKED) {
            $action = "{$this->params('action')}Action";
            return self::$acl->hasResource(get_class($this)) ? self::$acl->isAllowed(self::$acl->getPerfil(), get_class($this), $action) : false;
        } else {
            return true;
        }
    }

    /**
     *
     */
    public function verifyIsAllowed() {
        if (!$this->isAllowed()) {
            $this->flashmessenger()->addErrorMessage('Acesso negado.');
            $this->redirect()->toRoute('home');
            die();
        }
    }

    /**
     *
     * @param type $strController
     * @return type
     */
    private function explodeController($strController) {
        return explode('\\', $strController);
    }

    /**
     *
     * @param type $strController
     * @return type
     */
    public function getModuleName($strController = null) {
        if (!$strController) {
            $strController = get_class($this);
        }

        $arrController = $this->explodeController($strController);

        if (count($arrController) == 3) {
            return array_shift($arrController);
        }
    }

    /**
     *
     * @param type $filename
     * @throws \Exception
     */
    public function addJavascript($filename) {
        if (file_exists('public/js/modules/' . $filename)) {
            $this->lstJavascript[] = $filename;
        } else {
            throw new \Exception("Javascript não encontrado ({$filename}).");
        }
    }

    public function addExtraJavascript($filename) {
        if (file_exists('public/js/' . $filename)) {
            $this->lstExtraJavascript[] = $filename;
        } else {
            throw new \Exception("Javascript não encontrado ({$filename}).");
        }
    }

    /**
     *
     * @param type $filename
     * @throws \Exception
     */
    public function addCss($filename) {
        if (file_exists('public/css/' . $filename)) {
            $this->lstCss[] = $filename;
        } else {
            throw new \Exception("Css não encontrado ({$filename}).");
        }
    }

    /**
     *
     * @param type $model
     * @return type
     */
    public function getTable($model) {
        return $this->getServiceLocator()->get($model);
    }

    /**
     *
     * @param type $model
     * @return \FS\Model\Service
     */
    public function getService($model) {
        return $this->getTable($model);
    }

    /**
     *
     * @param type $parameter
     * @param type $postParameter
     * @return type
     */
    public function getParam($parameter, $postParameter = null) {
        if ($postParameter) {
            $postParameter = $this->getRequest()->getPost()->get($postParameter);
        }
        return $this->params()->fromRoute($parameter, $postParameter);
    }

    /**
     *
     * @param type $module
     * @param type $controller
     * @param type $action
     * @return type
     */
    protected function redir($module, $controller = null, $action = null, $parameters = array()) {
        if ($controller) {
            $parameters['controller'] = $controller;
        }
        if ($action) {
            $parameters['action'] = $action;
        }
        return $this->redirect()->toRoute($module, $parameters);
    }

    /**
     *
     * @param type $var
     * @param type $flashMessage
     * @return boolean
     */
    protected function validateNotNull($var, $flashMessage = null) {
        if (!trim($var)) {
            if ($flashMessage) {
                $this->flashMessenger()->addErrorMessage($flashMessage);
            }
            return false;
        }
        return true;
    }

    /**
     *
     * @param type $message
     */
    protected function addSuccessMessage($message) {
        $this->flashMessenger()->addSuccessMessage($message);
    }

    /**
     *
     * @param type $message
     */
    protected function addErrorMessage($message) {
        $this->flashMessenger()->addErrorMessage($message);
    }

    /**
     *
     * @param type $name
     * @param type $url
     */
    protected function addBreadcrumb($name, $url = null) {
        if ($name) {
            $this->breadcrumbs[$name] = $url;
        }
    }

    public function setLayoutBlank() {
        return $this->layout('layout/blank.phtml');
    }

}
