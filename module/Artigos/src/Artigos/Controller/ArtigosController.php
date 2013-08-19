<?php

namespace Artigos\Controller;

use FS\Controller\Controller;
use FS\View\DataGrid;
use FS\View\DatagridFilter;
use FS\View\SearchFilter;
use Artigos\Form\ArtigosForm;
use Artigos\Model\Artigos;

/**
 * Artigos
 *
 * @package Artigos
 * @subpackage Controller
 * @version 1.0
 * @author Guilherme Fontenele
 * @copyright FonteSolutions
 *
 * @property \Artigos\Service\TagsService $tagsService
 * @property \Artigos\Service\ArtigosService $artigosService
 */
class ArtigosController extends Controller {

    protected $tagsService;
    protected $artigosService;

    public function init() {
        $this->entity = new \FS\Entity\Entity('Artigos', 'artigos');
        $this->addBreadcrumb($this->translate('Artigo'), '/artigos/artigo');
        $this->addExtraJavascript('jquery.jstree.js');
        $this->addExtraJavascript('galeria-fotos.js');
    }

    public function indexAction() {
        try {
            $form = new ArtigosForm('artigos', $this->getServiceLocator(), $this->entity);

            $filters = new SearchFilter();
            $filters->setForm($form);
            $filters->setEntity($this->entity);
            $filters->setRequest($this->getRequest());

            $datagrid = new DataGrid('artigos');
            $datagrid->setLinkEdit('/artigos/artigo/edit/%d', array('int_cod'));
            $datagrid->setEntity($this->entity);

            $dgFilter = new DatagridFilter($filters, $datagrid, $this->getArtigosService(), 'fetchAll');

            $this->view->filters = $filters;
            $this->view->datagrid = $datagrid;

            return $this->view;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function addAction() {
        $this->addBreadcrumb($this->translate('Novo Artigo'));
        $request = $this->getRequest();

        $form = new ArtigosForm('artigos', $this->getServiceLocator(), $this->entity);
        $form->setAttribute('action', '/artigos/artigo/add');
        $form->get('submit')->setValue($this->translate('Adicionar'));

        if ($request->isPost()) {
            $artigo = new Artigos();
            $form->setInputFilter($artigo->getInputFilter());

            $data = $request->getPost();

            $objUsuario = \unserialize($this->getAuthStorage()->read());
            $data->offsetSet('int_redator', $objUsuario->cod);
            $data->offsetSet('int_situacao', \Artigos\Service\ArtigosService::SITUACAO_PENDENTE);

            $form->setData($data);

            if ($form->isValid()) {
                $artigo->exchangeArray($form->getData());
                $this->getArtigosService()->save($artigo);
                $this->addSuccessMessage($this->translate('Artigo criado com sucesso.'));
                return $this->redir('artigos', 'artigo', 'index');
            } else {
                $this->addErrorMessage($this->translate('Falha ao salvar Artigo.'));
            }
        }

        $this->view->form = $form;
        return $this->view;
    }

    public function editAction() {
        $this->addBreadcrumb($this->translate('Editar Artigo'));
        $request = $this->getRequest();
        $cod = (int) $this->getParam('id', 'int_cod');

        if (!$this->validateNotNull($cod, $this->translate('Código não informado.'))) {
            return $this->redir('artigos', 'artigo', 'index');
        }

        $artigo = $this->getArtigosService()->get(array($cod));

        $form = new ArtigosForm('artigos', $this->getServiceLocator(), $this->entity);
        $form->setAttribute('action', '/artigos/artigo/edit');
        $form->get('submit')->setAttribute('value', 'Editar');
        $artigo->dataInicioPublicacao = \FS\View\Helper\DateFormat::getDatetime($artigo->dataInicioPublicacao);
        $artigo->dataFimPublicacao = \FS\View\Helper\DateFormat::getDatetime($artigo->dataFimPublicacao);
        
        $form->setValues($artigo);

        if ($request->isPost()) {
            $form->setInputFilter($artigo->getInputFilter());

            $data = $request->getPost();

            $objUsuario = \unserialize($this->getAuthStorage()->read());
            $data->offsetSet('int_editor', $objUsuario->cod);
            $data->offsetSet('int_situacao', \Artigos\Service\ArtigosService::SITUACAO_PENDENTE);
            $data->offsetSet('dat_ultima_edicao', 'now()');

            $form->setData($data);

            if ($form->isValid()) {
                $this->getArtigosService()->save($form->getData());
                $this->addSuccessMessage($this->translate('Artigo editado com sucesso.'));
                return $this->redir('artigos', 'artigo', 'index');
            } else {
                $this->addErrorMessage($this->translate('Falha ao salvar Artigo.'));
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
            return $this->redir('artigos', 'artigo', 'index');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $cod = (int) $request->getPost('int_cod');
            $this->getArtigosService()->remove($cod);

            $this->addSuccessMessage($this->translate('Artigo excluído com sucesso.'));
            return $this->redir('artigos', 'artigo', 'index');
        }

        $layout = $this->layout();
        $layout->setTemplate('artigos/artigo/delete.phtml');
        $layout->artigo = $this->getArtigosService()->get(array($cod));
    }

    public function readAction() {
        try {
            $id = $this->getParam('id');

            if(!$id) {
                $this->redir('home');
            }

            $artigo = $this->getService('artigos')->get($id);
            $this->view->setVariable('artigo', $artigo->getArrayCopy());

            $this->addBreadcrumb($artigo->chamada);

            return $this->view;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Recuperar Tags Service
     * @return Artigos\Service\TagsService
     */
    public function getTagsService() {
        if (!$this->tagsService) {
            $this->tagsService = $this->getTable('tags');
        }
        return $this->tagsService;
    }

    /**
     * Recuperar Artigos Service
     * @return Artigos\Service\ArtigosService
     */
    public function getArtigosService() {
        if (!$this->artigosService) {
            $this->artigosService = $this->getTable('artigos');
        }
        return $this->artigosService;
    }

}
