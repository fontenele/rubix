<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Main\Controller;

use Rubix\Mvc\Controller;

/**
 *
 */
class IndexController extends Controller {

    /**
     * Init
     */
    public function init() {

    }

    /**
     * Index Action
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction() {
        $user = $this->identity();

        if ($user && $user->getIntCod()) {
            $this->setViewMessages();

            $this->addCss('modules/main/home/index.css');
            $this->addJavascript('modules/main/home/index.js');

            return $this->view;
        } else {
            return $this->redirect()->toUrl(APPLICATION_URL . 'main/home/login');
        }
    }

    /**
     * View Login
     * @return \Zend\View\Model\ViewModel
     */
    public function loginAction() {
        $this->setViewMessages();

        $layout = $this->setLayoutLight();
        $this->addCss('modules/main/home/login.css');

        return $this->view;
    }

    /**
     * Try Login with POST data
     * @return \Zend\View\Model\ViewModel
     */
    public function tryLoginAction() {
        $user = $this->getRequest()->getPost('usuario');
        $pass = $this->getRequest()->getPost('senha');
        $msgErro = array();

        if (!$user) {
            $msgErro[] = $this->translate('Informe o nome do usuário.');
        }
        if (!$pass) {
            $msgErro[] = $this->translate('Informe a senha.');
        }

        if (count($msgErro)) {
            foreach ($msgErro as $_msg) {
                $this->flashMessenger()->addErrorMessage(array('message' => $_msg));
            }
            return $this->indexAction();
        }

        $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
        $adapter = $authService->getAdapter();
        $adapter->setIdentityValue($user);
        $adapter->setCredentialValue($pass);

        $authResult = $authService->authenticate();
        if ($authResult->isValid()) {
            // Save user
            $usuario = $authResult->getIdentity();
            $usuario->setDatUltimoLogin(new \DateTime());
            $this->getEntityManager()->persist($usuario);
            $this->getEntityManager()->flush();
            $authService->getStorage()->write($usuario);

            // Save resources
            $acessos = array();
            $acessosBanco = $this->getEntityManager()->getRepository('\Main\Entity\Acessos')->findBy(array('intPerfil' => $usuario->getIntPerfil()->getIntCod()));

            foreach ($acessosBanco as $acesso) {
                $acessos[] = $acesso->getStrNomeAcesso();
            }

            $roles = include APPLICATION_PATH . '/config/acl.config.php';
            $roles[$usuario->getIntPerfil()->getIntCod()] = $acessos;

            $sessionAcl = new \Zend\Session\Container('acl');
            $sessionAcl->offsetSet('roles', $roles);

            $this->flashMessenger()->addSuccessMessage(array('message' => $this->translate('Bem vindo!'), 'timeout' => 5000));
            return $this->redirect()->toUrl(APPLICATION_URL);
        } else {
            $this->flashMessenger()->addErrorMessage(array('message' => $this->translate('Usuário/Senha inválidos.')));
        }

        return $this->indexAction();
    }

    /**
     * Do logout
     * @return \Zend\View\Model\ViewModel
     */
    public function doLogoutAction() {
        $auth = $this->getAuthService();
        $auth->clearIdentity();

        $this->flashMessenger()->addSuccessMessage(array('message' => $this->translate('Até logo!'), 'timeout' => 5000));

        return $this->indexAction();
    }

    public function restAction() {
        $method = $this->params()->fromQuery('method', 'get');

        $client = new \Zend\Http\Client();
        $client->setAdapter('Zend\Http\Client\Adapter\Curl');
        $client->setUri('http://rubix:80' . $this->getRequest()->getBaseUrl() . '/api/u/get-list');
        $client->setAuth('fontenele', '123456');
        //$client->setMethod('POST');
        //$client->setParameterPost(array('id' => 1,'name' => 'Guilherme Fontenele'));
        $client->setMethod('GET');
        //$client->setParameterGet(array('id' => 1,'name' => \Zend\Uri\Uri::encodePath('Guilherme Fontenele')));

        $response = $client->send();

        if (!$response->isSuccess()) {
            // report failure
            $message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();

            $response = $this->getResponse();
            $response->setContent($message);
            return $response;
        }

        $body = urldecode($response->getBody());
        $obj = json_decode($body);
        //xd($body, $obj);

        $response = $this->getResponse();
        $response->setContent($body);

        return $response;

    }

}
