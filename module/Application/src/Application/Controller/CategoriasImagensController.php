<?php

namespace Application\Controller;

use FS\Controller\Controller;

/**
 * Categorias de Imagens do Sistema
 *
 * @package Gerencial
 * @subpackage Controller
 * @version 1.0
 * @author Guilherme Fontenele
 * @copyright FonteSolutions
 */
class CategoriasImagensController extends Controller {

    public function init() {
        $this->entity = new \FS\Entity\Entity('Application', 'categoriasImagens');
    }

    public function salvarCategoriaAction() {
        if($this->request->getPost('str_nome')) {
            $categorias = new \Application\Model\CategoriasImagens();

            $form = new \Application\Form\CategoriasImagensForm('frm-categorias', $this->getServiceLocator(), $this->entity);

            $arrItems = $this->getService('categoriasImagens')->fetchAll();
            $lstItems = array();

            foreach ($arrItems as $_item) {
                if ($_item['int_cod_pai'] && !in_array($_item['int_cod'], $lstItems)) {
                    $lstItems[$_item['int_cod']] = $_item['str_nome'];
                }
            }

            $form->get('int_cod_pai')->setOptions(array(
                'value_options' => array('' => 'Não informado') + $lstItems,
            ));

            $form->setInputFilter($categorias->getInputFilter());
            $form->setData($this->request->getPost());
            
            if ($form->isValid()) {
                $categorias->exchangeArray($form->getData());
                $this->getService('categoriasImagens')->save($categorias);

                echo \Zend\Json\Json::encode(array(
                    'status' => 'success',
                    'message' => 'Categoria de imagem cadastrada com sucesso.',
                    'cod' => $categorias->cod
                ));
            }else{
                echo \Zend\Json\Json::encode(array(
                    'status' => 'error',
                    'message' => 'Falha ao cadastrar categoria de imagem.'
                ));
            }
        }else{
            echo \Zend\Json\Json::encode(array(
                'status' => 'error',
                'message' => 'Falha ao cadastrar categoria de imagem.'
            ));
        }

        die;
    }

}
