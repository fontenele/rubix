<?php

namespace FS\View;

class DatagridFilter {

    /**
     * Search filters
     * @var SearchFilter
     */
    public $filter;

    /**
     * Datagrid
     * @var DataGrid
     */
    public $datagrid;

    /**
     * Service
     * @var \FS\Model\Service
     */
    public $service;

    /**
     * Name of callback function
     * @var string
     */
    public $callback;

    /**
     * Args of callback function
     * @var array
     */
    public $args;

    public function __construct(SearchFilter $filter, DataGrid $datagrid, \FS\Model\Service $service, $callback, $args = array()) {
        $this->filter = $filter;
        $this->datagrid = $datagrid;
        $this->service = $service;
        $this->callback = $callback;
        $this->args = $args;

        $this->configure();
    }

    protected function configure() {
        $where = $this->getSqlWhereFilters();
        $data = $this->getData($where);
        $this->setDatagridData($data);
    }

    /**
     * Get where from post
     * @return array
     */
    protected function getSqlWhereFilters() {
        $where = array();
        foreach ($this->filter->getItems() as $item) {
            if (trim($this->filter->request->getPost($item->getAttribute('name')))) {
                $where[$item->getAttribute('name')] = $this->filter->request->getPost($item->getAttribute('name'));
            }
        }
        return $where;
    }

    protected function getData($where = array()) {
        $method = $this->callback;
        $args = $this->args + $where;

        $data = $this->service->$method($args);
        return $data;
    }

    protected function setDatagridData($data) {
        $this->datagrid->setData($data);
    }

}
