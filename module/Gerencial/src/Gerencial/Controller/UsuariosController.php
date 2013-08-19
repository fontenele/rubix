<?php

namespace Gerencial\Controller;

use Zend\View\Model\ViewModel;
use FS\Controller\Controller;
use FS\View\DataGrid;
use FS\View\DatagridFilter;
use FS\View\SearchFilter;
use Gerencial\Form\UsuariosForm;
use Gerencial\Model\Usuarios;

/**
 * Usuários do Sistema
 *
 * @package Gerencial
 * @subpackage Controller
 * @version 1.0
 * @author Guilherme Fontenele
 * @copyright FonteSolutions
 *
 * @property Gerencial\Service\UsuariosService $usuariosService
 */
class UsuariosController extends Controller {

    protected $usuariosService;

    public function init() {
        $menuView = new ViewModel();
        $menuView->setTemplate('gerencial/index/menu_principal.phtml');
        $menuView->controller = $this->params('controller');
        $menuView->action = $this->params('action');
        $menuView->controllerAction = $this->getRequest()->getUri()->getPath();
        $this->view->addChild($menuView, 'menu_principal');

        $this->entity = new \FS\Entity\Entity('Gerencial', 'usuarios');
        $this->addBreadcrumb($this->translate('Usuários'), '/gerencial/usuarios');
    }

    public function indexAction() {

        $form = new UsuariosForm('usuarios', $this->getServiceLocator(), $this->entity);

        $filters = new SearchFilter();
        $filters->setForm($form);
        $filters->setEntity($this->entity);
        $filters->setRequest($this->getRequest());

        $datagrid = new DataGrid('usuarios');
        $datagrid->setLinkEdit('/gerencial/usuarios/edit/%d', array('int_cod'));
        $datagrid->setEntity($this->entity);

        $dgFilter = new DatagridFilter($filters, $datagrid, $this->getUsuariosService(), 'fetchAll');

        $this->view->filters = $filters;
        $this->view->datagrid = $datagrid;

        return $this->view;
    }

    public function addAction() {
        $this->addBreadcrumb($this->translate('Novo Usuário'));
        $request = $this->getRequest();

        $form = new UsuariosForm('usuarios', $this->getServiceLocator(), $this->entity);
        $form->setAttribute('action', '/gerencial/usuarios/add');
        $form->get('submit')->setValue($this->translate('Adicionar'));

        if ($request->isPost()) {
            $usuario = new Usuarios();
            $form->setInputFilter($usuario->getInputFilter());

            $data = $request->getPost();
            $data->set('str_senha', md5($data->get('str_senha')));
            $form->setData($data);

            if ($form->isValid()) {
                $usuario->exchangeArray($form->getData());
                $this->getUsuariosService()->save($usuario);
                $this->addSuccessMessage($this->translate('Usuário criado com sucesso.'));
                return $this->redir('gerencial', 'usuarios', 'index');
            } else {
                $this->addErrorMessage($this->translate('Falha ao salvar Usuário.'));
            }
        }

        $this->view->form = $form;
        return $this->view;
    }

    public function editAction() {
        $this->addBreadcrumb($this->translate('Editar Usuário'));
        $request = $this->getRequest();
        $cod = (int) $this->getParam('id', 'int_cod');

        if (!$this->validateNotNull($cod, $this->translate('Código não informado.'))) {
            return $this->redir('gerencial', 'usuarios', 'index');
        }

        $usuario = $this->getUsuariosService()->get(array($cod));

        $form = new UsuariosForm('usuarios', $this->getServiceLocator(), $this->entity);
        $form->setAttribute('action', '/gerencial/usuarios/edit');
        $form->get('submit')->setAttribute('value', 'Editar');
        $form->setValues($usuario);

        if ($request->isPost()) {
            $form->setInputFilter($usuario->getInputFilter());

            $data = $request->getPost();
            $data->set('str_senha', md5($data->get('str_senha')));
            $form->setData($data);

            if ($form->isValid()) {
                $this->getUsuariosService()->save($form->getData());
                $this->addSuccessMessage($this->translate('Usuário editado com sucesso.'));
                return $this->redir('gerencial', 'usuarios', 'index');
            } else {
                $this->addErrorMessage($this->translate('Falha ao salvar Usuário.'));
            }
        }

        $this->view->cod = $cod;
        $this->view->form = $form;
        return $this->view;
    }

    public function deleteAction() {
        $cod = (int) $this->getParam('id', 'int_cod');

        if (!$cod) {
            $this->addErrorMessage($this->translate('Código não informado.'));
            return $this->redir('gerencial', 'usuarios', 'index');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $cod = (int) $request->getPost('int_cod');
            $this->getUsuariosService()->remove($cod);

            $this->addSuccessMessage($this->translate('Usuário excluído com sucesso.'));
            return $this->redir('gerencial', 'usuarios', 'index');
        }

        $layout = $this->layout();
        $layout->setTemplate('gerencial/usuarios/delete.phtml');
        $layout->usuario = $this->getUsuariosService()->get(array($cod));
    }

    /**
     * Recuperar Usuários Service
     * @return Gerencial\Service\UsuariosService
     */
    public function getUsuariosService() {
        if (!$this->usuariosService) {
            $this->usuariosService = $this->getService('usuarios');
        }
        return $this->usuariosService;
    }

}
