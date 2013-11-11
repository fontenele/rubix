<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rubix\Mvc;

use Zend\Mvc\Controller\AbstractRestfulController as BaseController;
use Zend\View\Model\JsonModel;

abstract class ControllerRestful extends BaseController {

    /**
     * @var array
     */
    protected $collectionOptions = array('GET', 'POST');

    /**
     * @var array
     */
    protected $resourceOptions = array('GET', 'PUT', 'DELETE');

    /**
     * @var array
     */
    protected $auth = array('user' => '', 'pass' => '');

    /**
     * @var ViewModel
     */
    public $view;

    /**
     *  Método de inicialização
     */
    abstract public function init();

    /**
     * Configure class
     */
    final public function configure() {
        $this->view = new JsonModel();
        $this->parseAuth();
        $this->init();
    }

    /**
     * Parse auth params
     */
    private function parseAuth() {
        if ($this->getRequest()->getServer()->offsetExists('PHP_AUTH_USER')) {
            $this->auth['user'] = $this->getRequest()->getServer()->offsetGet('PHP_AUTH_USER');
        }
        if ($this->getRequest()->getServer()->offsetExists('PHP_AUTH_PW')) {
            $this->auth['pass'] = $this->getRequest()->getServer()->offsetGet('PHP_AUTH_PW');
        }
    }

    final public function getOptions() {
        if ($this->params()->fromRoute('id', false)) {
            return $this->resourceOptions;
        }

        return $this->collectionOptions;
    }

    final public function options() {
        $response = $this->getResponse();

        $response->getHeaders()->addHeaderLine('Allow', implode(',', $this->getOptions()));
        return $response;
    }

    // configure response
    public function getResponseWithHeader() {
        $response = $this->getResponse();
        $response->getHeaders()
                //make can accessed by *
                ->addHeaderLine('Access-Control-Allow-Origin', '*')
                //set allow methods
                ->addHeaderLine('Access-Control-Allow-Methods', 'POST PUT DELETE GET');

        return $response;
    }

    /**
     *
     * @param type $parameter
     * @param type $postParameter
     * @return type
     */
    public function getParam($parameter) {
        $param = null;
        switch ($this->getRequest()->getMethod()) {
            case 'GET':
                $param = $this->getRequest()->getQuery()->get($parameter);
                break;
            case 'POST':
            case 'PUT':
            case 'DELETE':
                $param = $this->getRequest()->getPost()->get($parameter);
                break;
        }

        return $param;
    }

    /**
     *
     */
    private function configureAcl() {
        if (!self::$acl) {
            // Acl
            $acl = new \Zend\Permissions\Acl\Acl();

            // Session Acl
            $sessionAcl = new \Zend\Session\Container('acl');
            if ($sessionAcl->offsetExists('roles')) {
                $roles = $sessionAcl->offsetGet('roles');
            } else {
                $roles = include APPLICATION_PATH . '/config/acl.config.php';
            }

            $allResources = array();

            foreach ($roles as $perfil => $resources) {
                $role = new \Zend\Permissions\Acl\Role\GenericRole($perfil);
                $acl->addRole($role);

                $allResources = array_merge($resources, $allResources);

                foreach ($resources as $resource) {
                    if (!$acl->hasResource($resource)) {
                        $acl->addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
                    }
                }

                foreach ($allResources as $resource) {
                    $acl->allow($role, $resource);
                }
            }

            self::$acl = $acl;
        }
    }

    public function getAcl() {
        return self::$acl;
    }

    /**
     *
     * @return type
     */
    protected function isAllowed() {
        if (APPLICATION_LOCKED) {
            $identity = $this->identity();
            $module = strtolower($this->params('module'));
            $controller = strtolower($this->params('controller'));
            $action = strtolower($this->params('action'));

            $resource = "{$module}|{$controller}|{$action}";

            if ($identity && $identity->getIntCod()) {
                $codPerfil = $identity->getIntPerfil()->getIntCod();
                return self::$acl->hasResource($resource) ? self::$acl->isAllowed($codPerfil, $resource) : false;
            } else {
                return self::$acl->hasResource($resource) ? self::$acl->isAllowed('guess', $resource) : false;
            }

            return false;
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
        return $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
    }

    /**
     *
     * @param string $name
     * @return \Rubix\Mvc\Entity
     */
    public function getEntity($name) {
        $entity = new $name();
        $entity->setServiceManager($this->getServiceLocator());
        return $entity;
    }

    /**
     *
     * @param string $name
     * @return \Rubix\Mvc\Form
     */
    public function getForm($name = null, $options = array()) {
        $form = new $name(null, $options, $this->getServiceLocator());
        return $form;
    }

    /**
     * Translate strings
     * @param type $message
     * @param type $textDomain
     * @param type $locale
     * @return type
     */
    protected function translate($message, $textDomain = 'default', $locale = null) {
        return $this->getServiceLocator()->get('translator')->translate($message, $textDomain, $locale);
    }

    /**
     * Get client IP
     * @return string
     */
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

    /**
     * Throw exception message
     * @param type $e
     * @return \Zend\View\Model\JsonModel
     */
    protected function fault($e) {
        return new \Zend\View\Model\JsonModel(array(
            'meta' => array(
                'code' => 500,
                'error-message' => $e->getMessage(),
                'error-num' => $e->getCode()
            )
        ));
    }

}
