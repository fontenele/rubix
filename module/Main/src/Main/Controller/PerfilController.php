<?php

namespace Main\Controller;

use Rubix\Mvc\Controller;
use Main\Form\PerfilForm;
use Main\Entity\Perfis;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;

class PerfilController extends Controller {

    /**
     * Init
     */
    public function init() {

    }

    public function indexAction() {
        $this->setViewMessages();
        $repository = $this->getEntityManager()->getRepository('Main\Entity\Perfis');
        $perfis = $repository->findAll();

        $this->view->setVariable('datagrid', $perfis);
        return $this->view;
    }

    public function addAction() {
        $this->setViewMessages();
        $form = new PerfilForm();
        $id = $this->getParam('id') ? (int) $this->getParam('id') : null;

        if($id) {
            $post = $this->getEntityManager()->find('\Main\Entity\Perfis', $id);
            $form->bind($post);
        }
        
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

}
