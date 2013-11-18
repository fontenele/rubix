<?php

namespace Rubix\View\Components;

use Zend\View\Model\ViewModel;
use Zend\ServiceManager\ServiceManager;
use Zend\Paginator\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;

/**
 * @package Rubix\View\Components
 * @name $Datagrid
 */
class Datagrid {

    /**
     * Debug
     * @var boolean
     */
    public $debug = false;

    /**
     * View
     * @var ViewModel
     */
    public $view;

    /**
     * Doctrine QueryBuilder
     * @var \Doctrine\ORM\QueryBuilder
     */
    public $queryBuilder;

    /**
     * Paginator
     * @var Zend\Paginator\Paginator
     */
    public $paginator;

    /**
     * Itens per page
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
    public $title;

    /**
     *
     * @var \Rubix\Mvc\Form
     */
    public $form;

    /**
     *
     * @var array
     */
    public $filter = array();

    /**
     *
     * @var array
     */
    public $header = array();

    /**
     *
     * @var string
     */
    public $getIdMethodName = 'getIntCod';

    /**
     *
     * @var array
     */
    public $actions = array();

    const ACTION_EDIT = 1;
    const ACTION_REMOVE = 2;

    const HEADER_LINK = 1;
    const HEADER_BUTTON = 2;

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

    /**
     * Constructor
     * @param \Zend\Di\ServiceLocator $sm
     */
    public function __construct($sm = null) {
        if ($sm) {
            $this->setServiceManager($sm);
        }

        $view = new ViewModel();
        $view->setTemplate('components/datagrid.phtml');
        $this->setView($view);
    }

    /**
     * Return datagrid view
     * @return ViewModel
     */
    public function getView() {
        return $this->view;
    }

    /**
     * Set datagrid view
     * @param \Zend\View\Model\ViewModel $view
     * @return \Rubix\View\Components\Datagrid
     */
    public function setView(ViewModel $view) {
        $this->view = $view;
        return $this;
    }

    /**
     * Return Doctrine QueryBuilder
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function getQueryBuilder() {
        return $this->queryBuilder;
    }

    /**
     * Set Doctrine QueryBuilder
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @return \Rubix\View\Components\Datagrid
     */
    public function setQueryBuilder(\Doctrine\ORM\QueryBuilder $queryBuilder) {
        $request = new \Zend\Http\PhpEnvironment\Request();

        $sortField = $request->getQuery('_sortfield');
        $sortOrder = $request->getQuery('_sortorder');

        $this->debugQueryBuilder('Starting QueryBuilder', $queryBuilder);

        if($sortField && $this->columns[$sortField]['aliasOrderBy']) {
            $queryBuilder->orderBy($this->columns[$sortField]['aliasOrderBy'], $sortOrder == 'ASC' ? $sortOrder : 'DESC');
            $this->debugQueryBuilder("Adding ORDERBY [{$this->columns[$sortField]['aliasOrderBy']}:" . $sortOrder == 'ASC' ? $sortOrder : 'DESC' . "]", $queryBuilder);
        }

        $get = $request->getQuery()->toArray();
        foreach($this->filter as $filter) {
            if($filter['type'] != 'submit' && \key_exists($filter['name'], $get) && $get[$filter['name']]) {
                $val = urldecode($get[$filter['name']]);
                switch(true) {
                    case \gettype($get[$filter['name']]) == 'integer':
                        $queryBuilder->andWhere("{$filter['alias']} = {$val}");
                        $this->debugQueryBuilder("Adding WHERE [{$filter['alias']} = {$val}]", $queryBuilder);
                        break;
                    case strtoupper($filter['condition']) == 'LIKE':
                        $_val = strtoupper(sprintf($filter['conditionValue'], $val));
                        $queryBuilder->andWhere("UPPER({$filter['alias']}) {$filter['condition']} {$_val}");
                        $this->debugQueryBuilder("Adding WHERE [UPPER({$filter['alias']}) {$filter['condition']} {$_val}]", $queryBuilder);
                        break;
                    default:
                        $_val = sprintf($filter['conditionValue'], $val);
                        $queryBuilder->andWhere("{$filter['alias']} {$filter['condition']} {$_val}");
                        $this->debugQueryBuilder("Adding WHERE [{$filter['alias']} {$filter['condition']} {$_val}]", $queryBuilder);
                        break;
                }
            }
        }

        $doctrinePaginator = new DoctrinePaginator($queryBuilder);
        $paginatorAdapter = new PaginatorAdapter($doctrinePaginator);

        $paginator = new Paginator($paginatorAdapter);

        $paginator->setCurrentPageNumber($request->getQuery($this->getPageGetName()));
        $paginator->setItemCountPerPage($this->getItensPerPage());

        $this->queryBuilder = $queryBuilder;
        $this->setPaginator($paginator);

        return $this;
    }

    /**
     * Debug querys on QueryBuilder
     * @param string $action
     * @param \Doctrine\ORM\QueryBuilder $qb
     */
    protected function debugQueryBuilder($action, $qb) {
        if($this->debug) {
            echo '<pre>';
            echo "<h4>{$action}</h4>";
            print_r($qb->getQuery()->getSQL());
            echo '</pre>';
        }
    }

    /**
     * Get Paginator
     * @return \Zend\Paginator\Paginator
     */
    public function getPaginator() {
        return $this->paginator;
    }

    /**
     * Set Paginator
     * @param \Zend\Paginator\Paginator $paginator
     * @return \Rubix\View\Components\Datagrid
     */
    public function setPaginator(Paginator $paginator) {
        $this->paginator = $paginator;
        return $this;
    }

    /**
     * Return the index name on the url to return actual page
     * @return string
     */
    public function getPageGetName() {
        return $this->pageGetName;
    }

    /**
     * Set the index name on the url to return actual page
     * @param string $pageGetName
     * @return \Rubix\View\Components\Datagrid
     */
    public function setPageGetName($pageGetName) {
        $this->pageGetName = $pageGetName;
        return $this;
    }

    /**
     * Return itens per page
     * @return integer
     */
    public function getItensPerPage() {
        return $this->itensPerPage;
    }

    /**
     * Set itens per page
     * @param integer $itensPerPage
     * @return \Rubix\View\Components\Datagrid
     */
    public function setItensPerPage($itensPerPage) {
        $this->itensPerPage = $itensPerPage;
        return $this;
    }


    /**
     * Return datagrid title
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     * Set datagrid title
     * @param string $title
     * @return \Rubix\View\Components\Datagrid
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }

    /**
     * Return datagrid header
     * @return array
     */
    public function getHeader() {
        return $this->header;
    }

    /**
     * Return search form
     * @return \Rubix\Mvc\Form
     */
    public function getForm() {
        return $this->form;
    }

    /**
     * Set search form
     * @param \Rubix\Mvc\Form $form
     * @return \Rubix\View\Components\Datagrid
     */
    public function setForm(\Rubix\Mvc\Form $form) {
        $this->form = $form;
        return $this;
    }

    /**
     * Add header link
     * @param string $label
     * @param string $link
     * @return \Rubix\View\Components\Datagrid
     */
    public function addHeaderLink($label, $link = array()) {
        $this->header[] = array(
            'type' => self::HEADER_LINK,
            'label' => $label,
            'link' => $link,
            'icon' => null
        );
        return $this;
    }

    /**
     * Add header button
     * @param string $label
     * @param string $link
     * @param string $icon
     * @return \Rubix\View\Components\Datagrid
     */
    public function addHeaderButton($label, $link = array(), $icon = null) {
        $this->header[] = array(
            'type' => self::HEADER_BUTTON,
            'label' => $label,
            'link' => $link,
            'icon' => $icon
        );
        return $this;
    }

    /**
     * Set ServiceLocator
     * @param \Zend\ServiceManager\ServiceManager $sm
     */
    public function setServiceManager(ServiceManager $sm) {
        $this->sm = $sm;
    }

    /**
     * Return URLs configs
     * @return array
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     * Return the method name to return ID from Entity
     * @return string
     */
    public function getGetIdMethodName() {
        return $this->getIdMethodName;
    }

    /**
     * Set the method name to return ID from Entity
     * @param string $getIdMethodName
     * @return \Rubix\View\Components\Datagrid
     */
    public function setGetIdMethodName($getIdMethodName) {
        $this->getIdMethodName = $getIdMethodName;
        return $this;
    }

    /**
     * Add datagrid column
     * @param string $label
     * @param string $entityAttr
     * @param array $options
     * @param string $methodGet
     * @param string $aliasOrderBy
     * @return \Rubix\View\Components\Datagrid
     */
    public function addColumn($label, $entityAttr, $options = array(), $methodGet = null, $aliasOrderBy = null) {
        $this->columns[$entityAttr] = array(
            'label' => $label,
            'options' => $options,
            'methodGet' => $methodGet,
            'aliasOrderBy' => $aliasOrderBy
        );
        return $this;
    }

    /**
     * Add datagrid action on column actions
     * @param string $type
     * @param string $icon
     * @return \Rubix\View\Components\Datagrid
     */
    public function addAction($type, $icon = null, $jsCallback = null, $cssClass = null) {
        switch(true) {
            case $type == self::ACTION_REMOVE:
                $cssClass = 'item-delete';
                break;
            case $type == self::ACTION_EDIT:
                $cssClass = 'item-edit';
                break;
        }
        $this->actions[$type] = array(
            'icon' => $icon,
            'jsCallback' => $jsCallback,
            'cssClass' => $cssClass
        );
        return $this;
    }

    /**
     * Add search field
     * @param string $name
     * @param string $condition
     * @param string $conditionValue
     * @param string $type
     * @param string $alias
     * @param string $label
     * @return \Rubix\View\Components\Datagrid
     */
    public function addFilterField($name, $condition = '=', $conditionValue = "'%s'", $type = 'input', $alias = null, $label = null) {
        $render = '';
        $labelField = $name;
        $class = 'col-lg-6';

        switch($type) {
            case 'input':
                $render = 'formElement';
            break;
            case 'select':
                $render = 'formSelect';
            break;
            case 'submit':
                $render = 'formSubmit';
                $labelField = '';
                $class = 'col-lg-offset-2 col-lg-10';
            break;
        }

        $this->filter[] = array(
            'name' => $name,
            'alias' => $alias,
            'type' => $type,
            'label' => $label,
            'labelField' => $labelField,
            'render' => $render,
            'class' => $class,
            'condition' => $condition,
            'conditionValue' => $conditionValue
        );

        return $this;
    }

    /**
     * Render view
     * @return string
     */
    public function __toString() {
        try {
            $renderer = $this->sm->get('Zend\View\Renderer\PhpRenderer');
            $request = new \Zend\Http\PhpEnvironment\Request();

            if($this->form) {
                $this->getForm()->setData($request->getQuery());
            }

            $this->getView()->setVariable('title', $this->getTitle());
            $this->getView()->setVariable('header', $this->getHeader());
            $this->getView()->setVariable('form', $this->getForm());
            $this->getView()->setVariable('filter', $this->filter);
            $this->getView()->setVariable('queryParams', $request->getQuery()->getArrayCopy());
            $this->getView()->setVariable('sortField', $request->getQuery('_sortfield'));
            $this->getView()->setVariable('sortOrder', $request->getQuery('_sortorder') == 'ASC' ? 'DESC' : 'ASC');
            $this->getView()->setVariable('actions', $this->actions);
            $this->getView()->setVariable('columns', $this->columns);
            $this->getView()->setVariable('data', $this->getPaginator());
            $this->getView()->setVariable('url', $this->getUrl());
            $this->getView()->setVariable('getIdMethodName', $this->getGetIdMethodName());
            $this->getView()->setVariable('request', $request);
            $helper = $this->sm->get('viewhelpermanager');
            $this->getView()->setVariable('this', $helper);

            return $renderer->render($this->getView());
        } catch (\Exception $exception) {
            //throw $exception;
            xd($exception->getMessage(), $exception->getCode(), $exception->getFile(), $exception->getLine(), $exception->getTraceAsString());
            return '';
        }
    }

}
