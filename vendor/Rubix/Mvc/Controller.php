<?php

namespace Rubix\Mvc;

use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\AbstractActionController;

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
    final public function configure() {
        $this->booMostrarBarraTopo = true;
        $this->booMostrarMenuPrincipal = true;
        $this->booMostrarMenuDireita = true;
        $this->booMostrarRodape = true;

        if (!$this->isAllowed()) {
            $this->flashmessenger()->addErrorMessage('Acesso negado.');
            // @todo fazer redirect correto
            //$this->redirect()->toRoute('home'); //@todo caso esteja no modulo gerencial, redirecionar para /gerencial ou HTTP_Referer
        }

        $this->view = new ViewModel();

        //$plugin = $this->plugin('url');
        //$this->addBreadcrumb($this->getModuleName() == 'Application' ? '' : $this->getModuleName(), $plugin->fromRoute(strtolower($this->getModuleName())));

        $this->init();

        /** @todo configurar caso seja requisicao json, xml, pdf, senão prosseguir */
        if (isset($_SERVER['HTTP_HOST'])) {
            $this->configureView();
        } else {
            $this->setLayoutBlank();
        }
    }

    /**
     *
     */
    private function configureAcl() {
        if (!self::$acl) {

        }
    }

    /**
     *
     */
    protected function configureView() {
        $layout = $this->layout();

        //$layout->strClasseBarraTopo = $this->getModuleName() == 'Gerencial' ? 'navbar-inverse' : '';

        $layout->booMostrarBarraTopo = $this->booMostrarBarraTopo;
        $layout->booMostrarMenuPrincipal = $this->booMostrarMenuPrincipal;
        $layout->booMostrarMenuDireita = $this->booMostrarMenuDireita;
        $layout->booMostrarRodape = $this->booMostrarRodape;

        $_url = "{$this->getRequest()->getUri()->getScheme()}://{$this->getRequest()->getUri()->getHost()}{$this->getRequest()->getUri()->getPath()}";
        $map = str_replace(APPLICATION_URL, '', $_url);

        if (file_exists("public/js/modules/{$map}.js")) {
            $this->lstJavascript[] = "modules/{$map}.js";
        }

        if (file_exists("public/css/modules/{$map}.css")) {
            $this->lstCss[] = "modules/{$map}.css";
        }

        $layout->lstJavascripts = $this->lstJavascript;
        $layout->lstExtraJavascripts = $this->lstExtraJavascript;
        $layout->lstCss = $this->lstCss;

        $this->view->controller = $this->params('controller');
        $this->view->action = $this->params('action');
        $this->view->controllerAction = $this->getRequest()->getUri()->getPath();

        $container = new \Zend\Session\Container('system');

        $layout->usuario = $container->offsetExists('usuario') ? $container->offsetGet('usuario') : new \Rubix\Stdlib\Object();
        $layout->userInfo = $container->offsetExists('userinfo') ? $container->offsetGet('userinfo') : new \Rubix\Stdlib\Object();
    }

    public function setViewMessages() {
        $layout = $this->layout();
        $layout->mensagensErro = $this->flashmessenger()->getErrorMessages();
        $layout->mensagensSucesso = $this->flashmessenger()->getSuccessMessages();
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
        $this->setStyles();
        $this->setScripts();
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
     */
    protected function setStyles() {
        $layout = $this->layout();
        $layout->lstCss = $this->lstCss;
    }

    /**
     *
     */
    protected function setScripts() {
        $layout = $this->layout();
        $layout->lstExtraJavascripts = $this->lstExtraJavascript;
        $layout->lstJavascripts = $this->lstJavascript;
    }

    /**
     * Get Authentication Service
     * @return \Zend\Authentication\AuthenticationService
     */
    protected function getAuthService() {
        return $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
    }

    /**
     *
     * @return \DoctrineModule\Authentication\Storage\ObjectRepository
     */
    protected function getAuthStorage() {
        return $this->getAuthService()->getStorage();
    }

    /**
     *
     * @return \Zend\Session\SessionManager
     */
    protected function getSessionStorage() {
        //xd(get_class( $this->getServiceLocator()->get('session') ));
        return $this->getServiceLocator()->get('session');
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
            // @todo fazer o redirect correto
            //$this->redirect()->toRoute('home');
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
     * @param string $filename
     * @throws \Exception
     */
    public function addJavascript($filename) {
        if (file_exists('public/js/' . $filename)) {
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
            throw new \Exception("CSS não encontrado ({$filename}).");
        }
    }

    /**
     *
     * @param type $model
     * @return
     */
    public function getService($service) {
        return $this->getServiceLocator()->get($service);
    }

    /**
     * Get Entity Manager
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() {
        return $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
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

    /**
     *
     * @return type
     */
    public function setLayoutBlank() {
        return $this->layout('layout/blank.phtml');
    }

    /**
     *
     * @return type
     */
    public function setLayoutLight() {
        return $this->layout('layout/light.phtml');
    }

    public function getIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //check ip from share internet
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //to check ip is pass from proxy
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

}