<?php

namespace Main\Controller;

use Rubix\Mvc\Controller;
use Main\Form\PerfilForm;
use Main\Entity\Perfis;
use Zend\Paginator\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;

class PerfilController extends Controller {

    /**
     * Init
     */
    public function init() {
        
    }

    public function indexAction() {
        $this->setViewMessages();
        
        $perfis = $this->getEntityManager()->createQueryBuilder()->select('p')->from('Main\Entity\Perfis', 'p');
        
        $doctrinePaginator = new DoctrinePaginator($perfis);
        $paginatorAdapter = new PaginatorAdapter($doctrinePaginator);
        
        $paginator = new Paginator($paginatorAdapter);
        
        $paginator->setCurrentPageNumber($this->request->getQuery('page'));
        $paginator->setItemCountPerPage(5);
        
        $this->view->setVariable('datagrid', $paginator);
        return $this->view;
    }

    public function addAction() {
        $this->setViewMessages();
        $form = new PerfilForm();

        $request = $this->getRequest();

        if ($request->isPost()) {
            $perfil = new Perfis();

            $form->setInputFilter($perfil->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $perfil->exchangeArray($form->getData());

                $this->getEntityManager()->persist($perfil);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addSuccessMessage(array('message' => 'Registro salvo com sucesso!'));
                return $this->redirect()->toUrl(APPLICATION_URL . 'main/perfil');
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
            return $this->redirect()->toUrl(APPLICATION_URL . 'main/perfil');
        }

        $post = $this->getEntityManager()->find('\Main\Entity\Perfis', $id);
        $request = $this->getRequest();

        $form = new PerfilForm();
        $form->bind($post);

        if ($request->isPost()) {

            $form->setInputFilter($post->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist($post);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addSuccessMessage(array('message' => 'Registro salvo com sucesso!'));
                return $this->redirect()->toUrl(APPLICATION_URL . 'main/perfil');
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
            return $this->redirect()->toUrl(APPLICATION_URL . 'main/perfil');
        }
        
        $post = $this->getEntityManager()->find('\Main\Entity\Perfis', $id);
        $this->getEntityManager()->remove($post);
        $this->getEntityManager()->flush();
        
        $this->flashMessenger()->addSuccessMessage(array('message' => 'Registro removido com sucesso!'));
        return $this->redirect()->toUrl(APPLICATION_URL . 'main/perfil');
    }

}
