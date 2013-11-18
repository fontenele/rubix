<?php

namespace Main\Controller;

use Rubix\Mvc\Controller;

/**
 * Usuario Controller
 * @package Main\Controller
 * @name UsuarioController
 */
class UsuarioController extends Controller {

    /**
     * Init
     */
    public function init() {

    }

    /**
     * List items action
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction() {
        // Set View Error/Success messages
        $this->setViewMessages();

        $dg = $this->createDatagrid();

        $this->view->setVariable('dg', $dg);
        return $this->view;
    }

    /**
     * Create DataGrid
     * @return \Rubix\View\Components\Datagrid
     */
    protected function createDatagrid() {
        // Datagrid
        $dg = new \Rubix\View\Components\Datagrid($this->getServiceLocator());
        //$dg->debug = true;
        $dg->setTitle('Usuários')
                ->setForm($this->getForm('\Main\Form\UsuarioForm'));

        // Columns
        $dg->addColumn('ID', 'intCod', array('attributes' => array('width' => '5%', 'class' => 'text-center')), null, 'u.intCod')
                ->addColumn('Login', 'strLogin', null, null, 'u.strLogin')
                ->addColumn('Nome', 'strNome', null, null, 'u.strNome')
                ->addColumn('E-mail', 'strEmail', null, null, 'u.strEmail')
                ->addColumn('Perfil', 'intPerfil', null, __CLASS__ . '::dg_Perfil', 'p.strNome')
                ->addColumn('Últ. Acesso', 'datUltimoLogin', null, __CLASS__ . '::dg_DtUltimoAcesso', 'u.datUltimoLogin');

        // Filters
        $dg->addFilterField('strLogin', 'LIKE', "'%%%s%%'", 'input', 'u.strLogin')
                ->addFilterField('strNome', 'LIKE', "'%%%s%%'",  'input', 'u.strNome')
                ->addFilterField('intPerfil', '=', '%s',  'select', 'u.intPerfil')
                ->addFilterField('submit', '', '', 'submit');

        // Define Form method and action
        $dg->getForm()->setAttribute('action', APPLICATION_URL . 'main/usuario/?' . $this->getRequest()->getQuery()->toString());
        $dg->getForm()->setAttribute('method', 'get');
        $dg->getForm()->get('submit')->setValue('Pesquisar');

        // Generate Query Builder
        $usuarios = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from('Main\Entity\Usuarios', 'u')
                ->innerJoin('u.intPerfil', 'p');
        $dg->setQueryBuilder($usuarios);

        // Header buttons
        $dg->addHeaderButton('Novo Usuário', array('module' => 'main', 'controller' => 'usuario', 'action' => 'add'), 'plus');

        // Actions column
        $dg->addAction(\Rubix\View\Components\Datagrid::ACTION_EDIT, 'edit');
        $dg->addAction(\Rubix\View\Components\Datagrid::ACTION_REMOVE, 'remove');

        // Define Routes
        $dg->url['edit'] = array('module' => 'main', 'controller' => 'usuario', 'action' => 'edit');
        $dg->url['remove'] = array('module' => 'main', 'controller' => 'usuario', 'action' => 'remove');
        $dg->url['paginator'] = array('module' => 'main', 'controller' => 'usuario');

        return $dg;
    }

    /**
     * DataGrid - Column render callback
     * @param string $val
     * @param mixed $item
     * @return string
     */
    public static function dg_Perfil($val, $item) {
        return $item->getIntPerfil()->getStrNome();
    }

    /**
     * DataGrid - Column render callback
     * @param string $val
     * @param mixed $item
     * @return string
     */
    public static function dg_DtUltimoAcesso($val, $item) {
        return $val->format('d/m/Y H:i:s');
    }

    /**
     * Create new item action
     * @return \Zend\View\Model\ViewModel
     */
    public function addAction() {
        $this->setViewMessages();
        $form = $this->getForm('\Main\Form\UsuarioForm');

        $request = $this->getRequest();

        if ($request->isPost()) {
            $usuario = $this->getEntity('Main\Entity\Usuarios');

            $form->setInputFilter($usuario->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $usuario->exchangeArray($form->getData());

                $this->getEntityManager()->persist($usuario);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addSuccessMessage(array('message' => 'Registro salvo com sucesso!'));
                return $this->redirect()->toUrl(APPLICATION_URL . 'main/usuario');
            } else {
                xd($form->getMessages());
            }
        }

        $this->view->setVariable('form', $form);
        return $this->view;
    }

    /**
     * Edit item action
     * @return \Zend\View\Model\ViewModel
     */
    public function editAction() {
        $this->setViewMessages();
        $id = $this->getParam('id') ? (int) $this->getParam('id') : null;

        if ($id == null) {
            $this->flashMessenger()->addErrorMessage(array('message' => 'Parâmetro não informado.'));
            return $this->redirect()->toUrl(APPLICATION_URL . 'main/usuario');
        }

        $post = $this->getEntityManager()->find('\Main\Entity\Usuarios', $id);
        $post->setServiceManager($this->getServiceLocator());
        $request = $this->getRequest();

        $form = $this->getForm('\Main\Form\UsuarioForm');
        $form->bind($post);

        if ($request->isPost()) {

            $form->setInputFilter($post->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist($post);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addSuccessMessage(array('message' => 'Registro salvo com sucesso!'));
                return $this->redirect()->toUrl(APPLICATION_URL . 'main/usuario');
            } else {
                xd($form->getMessages());
            }
        }

        $this->view->setVariable('form', $form);
        $this->view->setVariable('id', $id);
        return $this->view;
    }

    /**
     * Delete item action
     * @return \Zend\Mvc\Controller\Plugin\Redirect
     */
    public function removeAction() {
        $id = $this->getParam('id') ? (int) $this->getParam('id') : null;

        if ($id == null) {
            $this->flashMessenger()->addErrorMessage(array('message' => 'Parâmetro não informado.'));
            return $this->redirect()->toUrl(APPLICATION_URL . 'main/usuario');
        }

        $post = $this->getEntityManager()->find('\Main\Entity\Usuarios', $id);
        $this->getEntityManager()->remove($post);
        $this->getEntityManager()->flush();

        $this->flashMessenger()->addSuccessMessage(array('message' => 'Registro removido com sucesso!'));
        return $this->redirect()->toUrl(APPLICATION_URL . 'main/usuario');
    }

}
