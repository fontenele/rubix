<?php

namespace Gerencial\Controller;

use Zend\View\Model\ViewModel;
use FS\Controller\Controller;
use Gerencial\Form\AcessosForm;
use Gerencial\Model\Acessos;

/**
 * Acessos do Sistema
 *
 * @package Gerencial
 * @subpackage Controller
 * @version 1.0
 * @author Guilherme Fontenele
 * @copyright FonteSolutions
 *
 * @property Gerencial\Service\AcessosService $acessosService
 */
class AcessosController extends Controller {

    protected $acessosService;

    public function init() {
        $menuView = new ViewModel();
        $menuView->setTemplate('gerencial/index/menu_principal.phtml');
        $menuView->controller = $this->params('controller');
        $menuView->action = $this->params('action');
        $menuView->controllerAction = $this->getRequest()->getUri()->getPath();
        $this->view->addChild($menuView, 'menu_principal');

        $this->entity = new \FS\Entity\Entity('Gerencial', 'acessos');
        $this->addBreadcrumb('Acessos', '/gerencial/acessos');
    }

    public function indexAction() {

        $codPerfil = $this->getParam('id') ? (int) $this->getParam('id') : null;

        $form = new AcessosForm('acessos', $this->getServiceLocator(), $this->entity);
        $form->setAttribute('action', '/gerencial/acessos/salvar');
        $form->get('submit')->setValue($this->translate('Salvar'));

        if ($codPerfil) {
            $form->get('int_perfil')->setAttribute('value', $codPerfil);
        }

        $todosAcessos = $codPerfil ? $this->lerControllers() : array();
        $acessos = $actions = array();

        $actions = array_shift($todosAcessos);
        $comments = array_shift($todosAcessos);

        foreach ($this->getAcessosService()->fetchAll(array('perfis.int_cod' => $codPerfil)) as $_acesso) {
            $_tmp = explode('::', $_acesso['str_nome_acesso']);
            $acessos[$_tmp[0]][$_tmp[1]] = $_tmp[1];
        }

        $this->view->codPerfil = $codPerfil;
        $this->view->actions = is_array($actions) ? $actions : array();
        $this->view->comments = $comments;
        $this->view->acessos = $acessos;
        $this->view->form = $form;

        return $this->view;
    }

    public function salvarAction() {
        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = $request->getPost();
            $codPerfil = $post->offsetGet('int_perfil');

            $acesso = new Acessos();
            $this->getAcessosService()->remove(null, array('int_perfil' => $codPerfil));

            foreach ($post as $_controller => $_actions) {
                if (is_array($_actions)) {
                    foreach ($_actions as $_action => $_value) {
                        $_acesso = clone $acesso;
                        $_acesso->nome = "{$_controller}::{$_action}";
                        $_acesso->perfil = $codPerfil;
                        $this->getAcessosService()->save($_acesso);
                    }
                }
            }
        }

        $this->addSuccessMessage($this->translate('Item de Menu editado com sucesso.'));
        return $this->redir('gerencial', 'acessos', 'index', array('id' => $codPerfil));
    }

    protected function lerControllers() {
        $actionsForbidden = array(
            'afterExecuteAction',
            'notFoundAction',
            'getMethodFromAction',
        );
        $controllers = $comments = array();
        $_dirInicial = "./module/";

        $dir = dir($_dirInicial);

        while (false !== ($_dir = $dir->read())) {
            if (preg_match('/^[^\.].*/', $_dir, $res) && is_dir($_dir = $_dirInicial . $_dir . '/src')) {

                $controls = dir($_dir);
                $modulo = array_shift($res);
                while (false !== ($_control = $controls->read())) {
                    if (preg_match('/^[^\.].*/', $_control, $res)) {
                        if (is_dir($_dir . '/' . $_control) && is_dir($_dir . '/' . $_control . '/Controller')) {

                            $subcontrols = dir($_dir . '/' . $_control . '/Controller');

                            while (false !== ($_subcontrol = $subcontrols->read())) {
                                if (preg_match('/^[^\.].*/', $_subcontrol, $res) && preg_match('/Controller.php$/', $_subcontrol, $res)) {
                                    $classe = str_replace('.php', '', $_subcontrol);
                                    $controllers["{$modulo}\Controller\\{$classe}"] = array();
                                    $_classe = new \ReflectionClass("{$modulo}\Controller\\{$classe}");

                                    if ($_classe) {
                                        foreach ($_classe->getMethods(\ReflectionMethod::IS_PUBLIC) as $_metodo) {
                                            if (preg_match('/Action$/', $_metodo->getName(), $res) && !in_array($_metodo->getName(), $actionsForbidden)) {
                                                $_comments = trim(str_replace(array('/', '*', '  '), '', $_metodo->getDocComment()));
                                                $_comments = explode("\n", $_comments);

                                                if (trim($_comments[0])) {
                                                    $comments["{$modulo}\Controller\\{$classe}"][$_metodo->getName()] = trim($_comments[0]);
                                                }

                                                $controllers["{$modulo}\Controller\\{$classe}"][] = $_metodo->getName();
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            throw new \Exception('Controller ' . $_dir . '/' . $_control . '/Controller não encontrado.');
                        }
                    }
                }
            }
        }

        return array($controllers, $comments);
    }

    /**
     * Recuperar Acessos Service
     * @return Gerencial\Service\AcessosService
     */
    public function getAcessosService() {
        if (!$this->acessosService) {
            $this->acessosService = $this->getTable('acessos');
        }
        return $this->acessosService;
    }

}
