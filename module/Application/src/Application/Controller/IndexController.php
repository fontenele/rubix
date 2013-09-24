<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use FS\Controller\ControllerLivre;

class IndexController extends ControllerLivre {

    public function init() {
        
    }

    public function indexAction() {
        try {
            return $this->view;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function tryLoginAction() {
        $this->getAuthService()
                ->getAdapter()
                ->setIdentity($this->getRequest()->getPost('str-login'))
                ->setCredential($this->getRequest()->getPost('str-senha'));

        $result = $this->getAuthService()->authenticate();

        if ($result->isValid()) {
            $this->flashmessenger()->addSuccessMessage($this->translate('Autenticado com sucesso.'));

            if ($this->getRequest()->getPost('rememberme') == 1) {
                $this->getAuthStorage()->setRememberMe(1);
            }

            $this->getAuthService()->setStorage($this->getSessionStorage());

            $usuario = $this->getServiceLocator()->get('usuarios')->get(null, array('usuarios.str_login' => $this->getRequest()->getPost('str-login')));
            $this->getService('usuarios')->save($usuario);
            $this->getAuthStorage()->write(serialize($usuario));
        } else {
            $mensagem = '';
            switch ($result->getCode()) {
                case -4:
                    $mensagem = $this->translate('Falha devido a razões desconhecidas.');
                    break;
                case -3:
                case -1:
                    $mensagem = $this->translate('Usuário inválido.');
                    break;
                case -2:
                    $mensagem = $this->translate('Login ambíguo.');
                    break;
                case 0:
                    break;
            }
            $this->flashmessenger()->addErrorMessage($mensagem);
        }

        $arrHeader = $this->params()->fromHeader();
        return $this->redirect()->toUrl($arrHeader['Referer']);
    }

    public function tryLogoutAction() {
        $this->getAuthService()->getStorage()->forgetMe();
        $this->getAuthService()->clearIdentity();

        $this->getSessionStorage()->clear();

        $this->flashmessenger()->addSuccessMessage($this->translate("Obrigado e volte sempre!"));
        return $this->redirect()->toRoute('home');
    }

}
