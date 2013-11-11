<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Rubix\Mvc;

use Zend\Mvc\Controller\AbstractRestfulController as BaseController;
use Zend\View\Model\JsonModel;

abstract class ControllerRestful extends BaseController {

    /**
     *
     * @var array
     */
    protected $collectionOptions = array('GET', 'POST');

    /**
     *
     * @var array
     */
    protected $resourceOptions = array('GET', 'PUT', 'DELETE');

    /**
     *  Método de inicialização
     */
    abstract public function init();

    /**
     *
     * @var ViewModel
     */
    public $view;

    /**
     *
     */
    final public function configure() {
        $this->view = new JsonModel();

        $this->init();
    }

    final public function getOptions() {
        if ($this->params->fromRoute('id', false)) {
            return $this->resourceOptions;
        }

        return $this->collectionOptions;
    }

    final public function options() {
        $response = $this->getResponse();

        $response->getHeaders()->addHeaderLine('Allow', implode(',', $this->getOptions()));
        return $response;
    }

    // configure response
    public function getResponseWithHeader() {
        $response = $this->getResponse();
        $response->getHeaders()
                //make can accessed by *
                ->addHeaderLine('Access-Control-Allow-Origin', '*')
                //set allow methods
                ->addHeaderLine('Access-Control-Allow-Methods', 'POST PUT DELETE GET');

        return $response;
    }

    /**
     *
     * @param type $parameter
     * @param type $postParameter
     * @return type
     */
    public function getParam($parameter) {
        $param = null;
        switch($this->getRequest()->getMethod()) {
            case 'GET':
                $param = $this->getRequest()->getQuery()->get($parameter);
                break;
            case 'POST':
            case 'PUT':
            case 'DELETE':
                $param = $this->getRequest()->getPost()->get($parameter);
                break;
        }

        return $param;
    }

}
