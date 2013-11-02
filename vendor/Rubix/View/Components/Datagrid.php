<?php

namespace Rubix\View\Components;

use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceManager;
use Zend\Paginator\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;

class Datagrid {

    /**
     *
     * @var ViewModel
     */
    public $view;

    /**
     *
     * @var \Doctrine\ORM\QueryBuilder
     */
    public $queryBuilder;

    /**
     * Paginator
     * @var Zend\Paginator\Paginator
     */
    public $paginator;

    /**
     *
     * @var integer
     */
    public $itensPerPage = 10;

    /**
     *
     * @var string
     */
    public $pageGetName = 'page';

    /**
     * Datagrid Columns
     * @var array
     */
    public $columns = array();

    /**
     *
     * @var string
     */
    public $getIdMethodName;

    /**
     *
     * @var array
     */
    public $actions = array();

    const ACTION_EDIT = 1;
    const ACTION_REMOVE = 2;

    /**
     *
     * @var array
     */
    public $url = array(
        'edit' => array(
            'module' => null,
            'controller' => null,
            'action' => 'edit'
        ),
        'remove' => array(
            'module' => null,
            'controller' => null,
            'action' => 'remove'
        ),
        'paginator' => array(
            'module' => null,
            'controller' => null,
            'action' => null
        ),
    );

    /**
     *
     * @var \Zend\ServiceManager\ServiceManager
     */
    public $sm;

    public function __construct($sm = null) {
        if ($sm) {
            $this->setServiceManager($sm);
        }

        $view = new ViewModel();
        $view->setTemplate('components/datagrid.phtml');
        $this->setView($view);
    }

    public function getView() {
        return $this->view;
    }

    public function setView(ViewModel $view) {
        $this->view = $view;
        return $this;
    }

    public function getQueryBuilder() {
        return $this->queryBuilder;
    }

    public function setQueryBuilder(\Doctrine\ORM\QueryBuilder $queryBuilder) {
        $doctrinePaginator = new DoctrinePaginator($queryBuilder);
        $paginatorAdapter = new PaginatorAdapter($doctrinePaginator);

        $paginator = new Paginator($paginatorAdapter);

        $request = new \Zend\Http\PhpEnvironment\Request();
        $paginator->setCurrentPageNumber($request->getQuery($this->getPageGetName()));
        $paginator->setItemCountPerPage($this->getItensPerPage());

        $this->queryBuilder = $queryBuilder;
        $this->setPaginator($paginator);

        return $this;
    }

    public function getPaginator() {
        return $this->paginator;
    }

    public function setPaginator(Paginator $paginator) {
        $this->paginator = $paginator;
        return $this;
    }

    public function getPageGetName() {
        return $this->pageGetName;
    }

    public function getItensPerPage() {
        return $this->itensPerPage;
    }

    public function setItensPerPage($itensPerPage) {
        $this->itensPerPage = $itensPerPage;
        return $this;
    }

    public function setPageGetName($pageGetName) {
        $this->pageGetName = $pageGetName;
        return $this;
    }

    /**
     *
     * @param \Zend\ServiceManager\ServiceManager $sm
     */
    public function setServiceManager(ServiceManager $sm) {
        $this->sm = $sm;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getGetIdMethodName() {
        return $this->getIdMethodName;
    }

    public function setGetIdMethodName($getIdMethodName) {
        $this->getIdMethodName = $getIdMethodName;
        return $this;
    }

    public function addColumn($label, $entityAttr, $options = array(), $methodGet = null) {
        $this->columns[$entityAttr] = array(
            'label' => $label,
            'options' => $options,
            'methodGet' => $methodGet
        );
    }

    public function addAction($type, $icon = null) {
        $this->actions[$type] = array(
            'icon' => $icon
        );
    }

    /**
     *
     * @return string
     */
    public function __toString() {
        try {
            $renderer = $this->sm->get('Zend\View\Renderer\PhpRenderer');
            $this->getView()->setVariable('actions', $this->actions);
            $this->getView()->setVariable('columns', $this->columns);
            $this->getView()->setVariable('data', $this->getPaginator());
            $this->getView()->setVariable('url', $this->getUrl());
            $this->getView()->setVariable('getIdMethodName', $this->getGetIdMethodName());

            $helper = $this->sm->get('viewhelpermanager');
            $this->getView()->setVariable('this', $helper);

            return $renderer->render($this->getView());
        } catch (\Exception $exception) {
            xd($exception->getMessage());
            return '';
        }
    }

}
