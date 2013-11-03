<?php

namespace Main\Controller;

use Rubix\Mvc\Controller;
use Zend\Paginator\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;

class UsuarioController extends Controller {

    /**
     * Init
     */
    public function init() {

    }

    /**
     * Primary action
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction() {
        // Set View Error/Success messages
        $this->setViewMessages();

        // Generate Query Builder
        $usuarios = $this->getEntityManager()->createQueryBuilder()
                ->select('u')
                ->from('Main\Entity\Usuarios', 'u')
                ->innerJoin('u.intPerfil', 'p');

        // Datagrid
        $dg = new \Rubix\View\Components\Datagrid($this->getServiceLocator());
        $dg->setTitle('Usuários');

        // Columns
        $dg->addColumn('ID', 'intCod', array('attributes' => array('width' => '5%', 'class' => 'text-center')), null, 'u.intCod')
                ->addColumn('Login', 'strLogin', null, null, 'u.strLogin')
                ->addColumn('Nome', 'strNome', null, null, 'u.strNome')
                ->addColumn('E-mail', 'strEmail', null, null, 'u.strEmail')
                ->addColumn('Perfil', 'intPerfil', null, __CLASS__ . '::dg_Perfil', 'p.strNome')
                ->addColumn('Últ. Acesso', 'datUltimoLogin', null, __CLASS__ . '::dg_DtUltimoAcesso', 'u.datUltimoLogin')
                ->setQueryBuilder($usuarios);

        // Header buttons
        $dg->addHeaderButton('Novo Usuário', array('module' => 'main', 'controller' => 'usuario', 'action' => 'add'), 'plus');

        // Row Actions
        $dg->setGetIdMethodName('getIntCod');
        $dg->addAction(\Rubix\View\Components\Datagrid::ACTION_EDIT, 'edit');
        $dg->addAction(\Rubix\View\Components\Datagrid::ACTION_REMOVE, 'remove');

        // Define Routes
        $dg->url['edit'] = array('module' => 'main', 'controller' => 'usuario', 'action' => 'edit');
        $dg->url['remove'] = array('module' => 'main', 'controller' => 'usuario', 'action' => 'remove');
        $dg->url['paginator'] = array('module' => 'main', 'controller' => 'usuario');

        $this->view->setVariable('dg', $dg);
        return $this->view;
    }

    public static function dg_Perfil($val, $item) {
        return $item->getIntPerfil()->getStrNome();
    }

    public static function dg_DtUltimoAcesso($val, $item) {
        return $val->format('d/m/Y H:i:s');
    }

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
