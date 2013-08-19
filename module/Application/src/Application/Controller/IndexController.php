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
            $this->view->destaques = $this->getService('artigos')->fetchAll('int_destaque = 1');
            $arItens = $arItensTmp = array();
            $buscarNoticias = true;

            if ($buscarNoticias) {

                // Notícias
                $urlNoticias = 'http://g1.globo.com/dynamo/tecnologia/rss2.xml';

                $rss = new \FS\Feed\Rss();

                $rss->load($urlNoticias);
                $arItensTmp = $rss->getItems();

                $maxItens = 40;

                for ($i = 0; $i < $maxItens; $i++) {
                    unset($arItensTmp[$i]['description']);
                    unset($arItensTmp[$i]['category']);
                    unset($arItensTmp[$i]['pubDate']);
                    $arItens[] = $arItensTmp[$i];
                }
            }

            $this->view->setVariable('noticias', $arItens);
            return $this->view;
        } catch (Exception $e) {
            //throw $e;
        }
    }

    public function quemSomosAction() {

    }

    public function servicosAction() {

    }

    public function clientesAction() {

    }

    public function appsIosAction() {

    }

    public function sistemasGestaoAction() {

    }

    public function sitesEmpresariaisAction() {

    }

    public function advergamesAction() {

    }

    public function suporteAction() {

    }

    public function contatoAction() {
        if ($this->getRequest()->isPost()) {
            $booEnviouEmail = false;
            $userMail = 'contato@fontesolutions.com.br';
            $passMail = 'M$OCd+i]9Mv!';

            $headers = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

            // Additional headers
            $headers .= "From: FonteSolutions <{$userMail}>\r\n";

            $to = 'guilherme@fontenele.net';
            $subject = '[FonteSolutions::Contato] Contato direto do site';

            $txComentarios = str_replace("\n", '<br />', $_POST['txComentarios']);

            $message = <<<HTML
            <html>
            <head>
            <title>Contato</title>
            </head>
                <body>
                    <p>Contato</p>
                    <table>
                        <tr>
                            <th>Nome</th><th>Telefone</th><th>Email</th>
                        </tr>
                        <tr>
                            <td>{$_POST['nmNome']}</td><td>{$_POST['nrTelefone']}</td><td>{$_POST['nmEmail']}</td>
                        </tr>
                    </table>
                    <hr />
                    <h4>Comentários e informações adicionais</h4>
                    <p>{$txComentarios}</p>
                </body>
            </html>
HTML;

            $booEnviouEmail = mail($to, $subject, $message, $headers);

            if ($booEnviouEmail) {
                $this->addSuccessMessage($this->translate('Contato enviado com sucesso.'));
            } else {
                $this->addErrorMessage($this->translate('Falha ao enviar contato. Tente direto pelo email ' . $userMail));
            }

            return $this->redir('contato');
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
