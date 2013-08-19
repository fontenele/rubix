<?php

namespace FS\Entity;

class Entity {

    protected $schema;
    protected $table;
    protected $sequence;
    protected $model;
    protected $service;
    protected $view;
    protected $module;
    protected $name;

    public function __construct($module, $entity) {
        $this->module = $module;
        $this->name = $entity;
        $reader = new \Zend\Config\Reader\Xml();
        $config = $reader->fromFile(APPLICATION_DIR . "module/{$this->module}/xml/{$this->name}.xml");

        $this->schema = $config['schema'];
        $this->table = $config['tabela'];
        $this->sequence = $config['sequence'];
        $this->model = $config['model'];

        $this->service = $config['service'];
        $this->view = $config['view'];
    }

    public function getView() {
        return $this->view;
    }

    public function getService() {
        return $this->service;
    }

    public function getSchema() {
        return $this->schema;
    }

    public function getTable() {
        return $this->table;
    }

    public function getSequence() {
        return $this->sequence;
    }

    public function getModel() {
        return $this->model;
    }

    public function getModule() {
        return $this->module;
    }

    public function getName() {
        return $this->name;
    }

}
