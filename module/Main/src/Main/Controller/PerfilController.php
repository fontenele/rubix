<?php

namespace Main\Controller;

use Rubix\Mvc\Controller;
use Main\Form\PerfilForm;
use Main\Model\Perfil;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use DoctrineORMModule\Form\Annotation\AnnotationBuilder;

class PerfilController extends Controller {

    /**
     * Init
     */
    public function init() {

    }

    public function indexAction() {
        xd(123);
    }

    public function addAction() {
        $form = new PerfilForm();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $perfil = new Perfil();
            $form->setInputFilter($perfil->getInputFilter());
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $perfil->exchangeArray($form->getData());
                xd($form->isValid(), $perfil);
            }
            xd($form->isValid(), $_POST);
        }
        $this->view->setVariable('form', $form);
        return $this->view;
    }

}
