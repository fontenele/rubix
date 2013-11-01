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

    public function indexAction() {
        $this->setViewMessages();

        $usuarios = $this->getEntityManager()->createQueryBuilder()->select('u')->from('Main\Entity\Usuarios', 'u');

        $doctrinePaginator = new DoctrinePaginator($usuarios);
        $paginatorAdapter = new PaginatorAdapter($doctrinePaginator);

        $paginator = new Paginator($paginatorAdapter);

        $paginator->setCurrentPageNumber($this->request->getQuery('page'));
        $paginator->setItemCountPerPage(5);

        $this->view->setVariable('datagrid', $paginator);
        return $this->view;
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
