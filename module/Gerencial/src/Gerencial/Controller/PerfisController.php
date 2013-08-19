<?php

namespace Gerencial\Controller;

use Zend\View\Model\ViewModel;
use FS\Controller\Controller;
use FS\View\DataGrid;
use FS\View\DatagridFilter;
use FS\View\SearchFilter;
use Gerencial\Form\PerfisForm;
use Gerencial\Model\Perfis;

/**
 * Perfis do Sistema
 *
 * @package Gerencial
 * @subpackage Controller
 * @version 1.0
 * @author Guilherme Fontenele
 * @copyright FonteSolutions
 *
 * @property Gerencial\Service\PerfisService $perfisService
 */
class PerfisController extends Controller {

    protected $perfisService;

    public function init() {
        $menuView = new ViewModel();
        $menuView->setTemplate('gerencial/index/menu_principal.phtml');
        $menuView->controller = $this->params('controller');
        $menuView->action = $this->params('action');
        $menuView->controllerAction = $this->getRequest()->getUri()->getPath();
        $this->view->addChild($menuView, 'menu_principal');

        $this->entity = new \FS\Entity\Entity('Gerencial', 'perfis');
        $this->addBreadcrumb($this->translate('Perfis'), '/gerencial/perfis');
    }

    public function indexAction() {

        $form = new PerfisForm('perfis', $this->getServiceLocator(), $this->entity);

        $filters = new SearchFilter();
        $filters->setForm($form);
        $filters->setEntity($this->entity);
        $filters->setRequest($this->getRequest());

        $datagrid = new DataGrid('perfis');
        $datagrid->setLinkEdit('/gerencial/perfis/edit/%d', array('int_cod'));
        $datagrid->setEntity($this->entity);

        $dgFilter = new DatagridFilter($filters, $datagrid, $this->getPerfisService(), 'fetchAll');

        $this->view->filters = $filters;
        $this->view->datagrid = $datagrid;

        return $this->view;
    }

    public function addAction() {
        $this->addBreadcrumb($this->translate('Novo Perfil'));
        $request = $this->getRequest();

        $form = new PerfisForm('perfis', $this->getServiceLocator(), $this->entity);
        $form->setAttribute('action', '/gerencial/perfis/add');
        $form->get('submit')->setValue($this->translate('Adicionar'));

        if ($request->isPost()) {
            $perfil = new Perfis();
            $form->setInputFilter($perfil->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $perfil->exchangeArray($form->getData());
                $this->getPerfisService()->save($perfil);
                $this->addSuccessMessage($this->translate('Perfil criado com sucesso.'));
                return $this->redir('gerencial', 'perfis', 'index');
            } else {
                $this->addErrorMessage($this->translate('Falha ao salvar Perfil.'));
            }
        }

        $this->view->form = $form;
        return $this->view;
    }

    public function editAction() {
        $this->addBreadcrumb($this->translate('Editar Perfil'));
        $request = $this->getRequest();
        $cod = (int) $this->getParam('id', 'int_cod');

        if (!$this->validateNotNull($cod, $this->translate('Código não informado.'))) {
            return $this->redir('gerencial', 'perfis', 'index');
        }

        $perfil = $this->getPerfisService()->get(array($cod));

        $form = new PerfisForm('perfis', $this->getServiceLocator(), $this->entity);
        $form->setAttribute('action', '/gerencial/perfis/edit');
        $form->get('submit')->setAttribute('value', 'Editar');
        $form->setValues($perfil);

        if ($request->isPost()) {
            $form->setInputFilter($perfil->getInputFilter());

            $data = $request->getPost();
            $form->setData($data);

            if ($form->isValid()) {
                $this->getPerfisService()->save($form->getData());
                $this->addSuccessMessage($this->translate('Perfil editado com sucesso.'));
                return $this->redir('gerencial', 'perfis', 'index');
            } else {
                $this->addErrorMessage($this->translate('Falha ao salvar Perfil.'));
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
            return $this->redir('gerencial', 'perfis', 'index');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $cod = (int) $request->getPost('int_cod');
            $this->getPerfisService()->remove($cod);

            $this->addSuccessMessage($this->translate('Perfil excluído com sucesso.'));
            return $this->redir('gerencial', 'perfis', 'index');
        }

        $layout = $this->layout();
        $layout->setTemplate('gerencial/perfis/delete.phtml');
        $layout->perfil = $this->getPerfisService()->get(array($cod));
    }

    /**
     * Recuperar Perfis Service
     * @return Gerencial\Service\PerfisService
     */
    public function getPerfisService() {
        if (!$this->perfisService) {
            $this->perfisService = $this->getTable('perfis');
        }
        return $this->perfisService;
    }

}
