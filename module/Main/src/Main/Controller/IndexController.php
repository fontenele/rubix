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
        $this->setViewMessages();

        $user = $this->identity();

        if ($user && $user->getIntCod()) {
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
        /* $objManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');

          $car1 = $objManager->find('Main\Entity\Usuarios', 1);
          xd($car1->getStrNome(), $car1->getIntPerfil()->getStrNome(), $car1->getDatUltimoLogin());

          $car2 = $objManager->find('Main\Entity\Perfis', 6);
          xd($car2); */

        /* $user = new \Main\Entity\Carro();

          $user->setMarca('VW');
          $objManager->persist($user);
          $objManager->flush();
          xd($user->getId()); */

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
            $msgErro[] = 'Informe o nome do usuário.';
        }
        if (!$pass) {
            $msgErro[] = 'Informe a senha.';
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
            $usuario = $authResult->getIdentity();
            $authService->getStorage()->write($usuario);
            $this->flashMessenger()->addSuccessMessage(array('message' => 'Bem vindo!', 'timeout' => 5000));
            return $this->redirect()->toUrl(APPLICATION_URL);
        } else {
            $this->flashMessenger()->addErrorMessage(array('message' => 'Usuário/Senha inválidos.'));
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

        $this->flashMessenger()->addSuccessMessage(array('message' => 'Até logo!', 'timeout' => 5000));

        return $this->indexAction();
    }

}
