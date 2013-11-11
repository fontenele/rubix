<?php

namespace Api\Controller;

use Rubix\Mvc\ControllerRestful as Restful;

class IndexController extends Restful {

    public function init() {

    }

    public function getListAction() {
        try {
            $user = $this->auth['user'];
            $pass = $this->auth['pass'];
            $msgErro = array();

            if (!$user) {
                throw new \Exception($this->translate('Informe o nome do usuário.'));
            }
            if (!$pass) {
                throw new \Exception($this->translate('Informe a senha.'));
            }

            $authService = $this->getServiceLocator()->get('Zend\Authentication\AuthenticationService');
            $adapter = $authService->getAdapter();
            $adapter->setIdentityValue($user);
            $adapter->setCredentialValue($pass);

            $authResult = $authService->authenticate();

            if ($authResult->isValid()) {
                $usuario = $authResult->getIdentity();
                $usuario->setDatUltimoLogin(new \DateTime());
                $this->getEntityManager()->persist($usuario);
                $this->getEntityManager()->flush();
                $this->view->usuario = $usuario->getArrayCopy();
            } else {
                throw new \Exception($this->translate('Usuário/Senha inválidos.'));
            }

            //xd($_REQUEST, $_SERVER);

            //$this->view->id = $id;
            //$this->view->name = $name;

            return $this->view;
        } catch (\Exception $e) {
            return $this->fault($e);
        }
    }

}
