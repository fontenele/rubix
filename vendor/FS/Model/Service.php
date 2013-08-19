<?php

namespace FS\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\AdapterAwareInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\HydratingResultSet;
use Zend\Db\Sql\TableIdentifier;
use Zend\Db\Sql\Select;

abstract class Service extends AbstractTableGateway implements AdapterAwareInterface {

    /**
     *
     * @var AbstractTableGateway
     */
    protected $tableGateway;

    /**
     *
     * @var string
     */
    protected $schema;

    /**
     *
     * @var type
     */
    protected $tableName;

    /**
     *
     * @var array
     */
    protected $primaryKey = array();

    /**
     *
     * @var array
     */
    protected $descriptionKey = array();

    /**
     *
     * @var string
     */
    protected $modelName;

    /**
     *
     * @var string
     */
    protected $sequence;

    /**
     *
     * @var array
     */
    public $_map = array();

    /**
     *
     * @var array
     */
    public $_joins = array();

    /**
     * Configurações da Entidade
     * @var \FS\Entity\Entity
     */
    protected $entity;

    /**
     *
     */
    public function __construct() {
        $this->init();
    }

    /**
     *
     */
    abstract public function init();

    /**
     *
     */
    public function configureEntity() {
        $this->modelName = $this->entity->getModel();
        $this->schema = $this->entity->getSchema();
        $this->tableName = $this->entity->getTable();
        $this->sequence = $this->entity->getSequence();

        $service = $this->entity->getService();
        foreach ($service['items'] as $_attr => $_item) {

            $name = $_item['name'];
            if (isset($_item['join'])) {

                $_arrJoin = array();

                $name = 'aux_' . $name;
                $joinTable = array($_item['join']['schema'], $_item['join']['table']);

                if (isset($_item['alias'])) {
                    $joinTable = array($_item['alias'] => $joinTable);
                }

                $_arrJoin[] = $joinTable;
                $_arrJoin[] = $_item['join']['on'];
                $_arrJoin[] = $_item['join']['where'];

                if (isset($_item['type'])) {
                    $_arrJoin[] = $_item['type'];
                }

                $this->_joins[] = $_arrJoin;
            }

            if (isset($_item['primaryKey'])) {
                $this->primaryKey[$_item['primaryKey']] = $name;
            }

            if (isset($_item['descriptionKey'])) {
                $this->descriptionKey[$_item['descriptionKey']] = $name;
            }

            $this->_map[$_attr] = $name;
        }

        ksort($this->primaryKey);
        ksort($this->descriptionKey);
    }

    /**
     *
     * @param type $cod
     * @param type $where
     * @return \FS\Model\modelName
     * @throws \Exception
     */
    public function get($cod = null, $where = null) {
        $_where = array();
        $rowset = null;

        if ($cod) {
            if (!is_array($cod)) {
                $cod = array($cod);
            }

            $primaryKey = $this->primaryKey;

            foreach ($primaryKey as $_pk) {
                $_where["{$this->tableName}.{$_pk}"] = array_shift($cod);
            }
        } elseif ($where && is_array($where)) {
            foreach ($where as $_attr => $_val) {
                $_where[$_attr] = $_val;
            }
        } else {
            throw new \Exception("Nenhum parâmetro foi informardo.");
        }

        if (count($this->_joins)) {
            $rowset = $this->select(
                    function(Select $select) use ($_where) {
                        $columns = array_values($this->getMap());
                        $select->columns($columns);
                        foreach ($this->_joins as $_join) {
                            $_table = array_shift($_join);
                            if (is_array($_table)) {
                                $_tableTmp = $_table;
                                $booTemAlias = false;
                                $_schema = $_tbJoin = $strAlias = '';

                                foreach ($_table as $_alias => $_tmpTable) {
                                    if (is_array($_tmpTable)) { // caso seja com alias
                                        $booTemAlias = true;
                                        $_schema = array_shift($_tmpTable);
                                        $_tbJoin = array_shift($_tmpTable);
                                        $strAlias = $_alias;
                                    } else { // caso seja sem alias
                                        switch ($_alias) {
                                            case 0:
                                                $_schema = $_tmpTable;
                                                break;
                                            case 1:
                                                $_tbJoin = $_tmpTable;
                                                break;
                                        }
                                    }
                                }

                                $_tableTmp = new TableIdentifier($_tbJoin, $_schema);
                                if ($booTemAlias) {
                                    $_tableTmp = array($strAlias => $_tableTmp);
                                }
                            }

                            $_table = $_tableTmp;

                            $_on = array_shift($_join);
                            $_columns = array_shift($_join);
                            $_type = array_shift($_join);
                            $select->join($_table, $_on, $_columns, $_type);
                        }
                        $select->where($_where);
                    });
        } else {
            $rowset = $this->select($_where);
        }

        $row = $rowset->current();

        if (!$row) {
            throw new \Exception("Não foi possível encontrar o registro.");
        }

        $objModel = new $this->modelName();
        $objModel->exchangeArray($row);

        return $objModel;
    }

    /**
     *
     * @param object $object
     * @return array
     */
    public function prepareForSave($data, $map = null) {
        $return = $_data = array();

        if (!$map) {
            $map = $this->getMap();
        } else {
            $map = array_flip($map);
        }

        $pk = $this->primaryKey;

        if (is_object($data)) {
            $_data = $data->getArrayCopy();
        }

        foreach ($_data as $_index => $_value) {
            if (isset($map[$_index])) {
                $return[$map[$_index]] = $_value;
            }
        }

        if ($pk) {
            foreach ($pk as $_pk) {
                if (key_exists($_pk, $return)) {
                    unset($return[$_pk]);
                }
            }
        }
        
        return $return;
    }

    /**
     *
     * @param type $cod
     * @param type $where
     * @return boolean
     */
    public function beforeRemove($cod = null, $where = null) {
        return true;
    }

    /**
     *
     * @param type $cod
     * @param type $where
     * @return type
     * @throws \Exception
     */
    public function remove($cod = null, $where = null) {

        $willRemove = $this->beforeRemove($cod, $where);

        if (!$willRemove) {
            throw new \Exception("Não foi possível remover o item.");
        }

        if ($cod) {
            $primaryKey = $this->primaryKey;
            $_where = array();
            if (is_array($cod)) {
                foreach ($primaryKey as $_pk) {
                    $_where["{$this->tableName}.{$_pk}"] = array_shift($cod);
                }
            } else {
                $cod = (int) $cod;
                $primaryKey = array_shift($primaryKey);
                $_where["{$this->tableName}.{$primaryKey}"] = $cod;
            }

            return $this->delete($_where);
        } elseif ($where && is_array($where)) {
            return $this->delete($where);
        } else {
            throw new \Exception("Nenhum parâmetro foi informardo.");
        }
    }

    /**
     *
     * @param type $data
     * @return type
     * @throws \Exception
     */
    public function save($data) {
        $map = $this->getMap();
        $cod = array();

        if (is_object($data)) {
            $map = array_flip($map);
            $_data = $this->prepareForSave($data, $map);
            $pk = $this->primaryKey;

            foreach ($pk as $_pk) {
                if (isset($data->$map[$_pk])) {
                    $cod[$_pk] = $data->$map[$_pk];
                }
            }
        } else {
            throw new \Exception('Não foi possível encontrar o ' . is_object($data) ? get_class($data) : get_class($this));
        }

        if (count($cod) == 0) {
            if (count($pk) == 1) {
                $cod = $this->getSequenceNextVal();
                $_data[$this->primaryKey[1]] = $cod;
                $data->$map[$this->primaryKey[1]] = $cod;
                return $this->insert($_data);
            } else {
                xd('implementar');/** @todo */
            }
        } else {
            if ($this->get($cod)) {
                return $this->update($_data, $cod);
            } else {
                throw new \Exception('Não foi possível encontrar o ' . is_object($data) ? get_class($data) : get_class($this) . ' = ' . $cod);
            }
        }
    }

    /**
     *
     * @param type $set
     * @param type $debug
     * @return type
     */
    public function insert($set, $debug = false) {

        if ($debug) {
            $insert = $this->sql->insert();
            $insert->values($set);
            xd($set, $insert->getSqlString());
        }

        return parent::insert($set);
    }

    /**
     *
     * @param Closure $where
     * @param type $debug
     * @return type
     */
    public function select($where = null, $debug = false) {

        if ($debug) {
            if (!$this->isInitialized) {
                $this->initialize();
            }

            $select = $this->sql->select();

            if ($where instanceof \Closure) {
                $where($select);
            } elseif ($where !== null) {
                $select->where($where);
            }
            xd($select->getSqlString());
        }

        return parent::select($where);
    }

    /**
     *
     * @param Adapter $adapter
     */
    public function setDbAdapter(Adapter $adapter) {

        $this->table = new TableIdentifier($this->tableName, $this->schema);
        $this->adapter = $adapter;
        $this->resultSetPrototype = new HydratingResultSet();

        $this->initialize();
    }

    /**
     *
     * @param type $where
     * @return type
     */
    public function fetchAll($where = null) {

        if (count($this->_joins)) {
            return $this->select(
                            function(Select $select) use ($where) {
                                $columns = array_values($this->getMap());
                                $select->columns($columns);
                                foreach ($this->_joins as $_join) {
                                    $_table = array_shift($_join);
                                    if (is_array($_table)) {
                                        $_tableTmp = $_table;
                                        $booTemAlias = false;
                                        $_schema = $_tbJoin = $strAlias = '';

                                        foreach ($_table as $_alias => $_tmpTable) {
                                            if (is_array($_tmpTable)) { // caso seja com alias
                                                $booTemAlias = true;
                                                $_schema = array_shift($_tmpTable);
                                                $_tbJoin = array_shift($_tmpTable);
                                                $strAlias = $_alias;
                                            } else { // caso seja sem alias
                                                switch ($_alias) {
                                                    case 0:
                                                        $_schema = $_tmpTable;
                                                        break;
                                                    case 1:
                                                        $_tbJoin = $_tmpTable;
                                                        break;
                                                }
                                            }
                                        }

                                        $_tableTmp = new TableIdentifier($_tbJoin, $_schema);
                                        if ($booTemAlias) {
                                            $_tableTmp = array($strAlias => $_tableTmp);
                                        }
                                    }

                                    $_table = $_tableTmp;

                                    $_on = array_shift($_join);
                                    $_columns = array_shift($_join);
                                    $_type = array_shift($_join);
                                    $select->join($_table, $_on, $_columns, $_type);
                                }

                                $select->where($where);
                            }
                    )->toArray();
        } else {
            return $this->select($where)->toArray();
        }
    }

    /**
     *
     * @param type $indexField
     * @param type $valueField
     * @param type $where
     * @return type
     */
    public function getAttributeArray($indexField, $valueField, $where = array()) {

        $rowset = $this->select(
                function(Select $select) use ($indexField, $valueField, $where) {
                    $select->columns(array($indexField, $valueField));
                    if (count($this->_joins)) {
                        foreach ($this->_joins as $_join) {
                            $_table = array_shift($_join);
                            if (is_array($_table)) {
                                $_schema = array_shift($_table);
                                $_tbJoin = array_shift($_table);
                                $_table = new TableIdentifier($_tbJoin, $_schema);
                            }

                            $_on = array_shift($_join);
                            $_columns = array_shift($_join);
                            $_type = array_shift($_join);

                            $select->join($_table, $_on, $_columns, $_type);
                        }
                    }

                    $select->where($where);
                }
        );

        $rows = $rowset->toArray();
        $return = array();

        foreach ($rows as $_row) {
            $return[$_row[$indexField]] = $_row[$valueField];
        }

        return $return;
    }

    /**
     *
     * @param type $seq
     * @return type
     */
    public function getSequenceNextVal($seq = null, $index = null) {

        if ($seq) {
            $_seq = $seq;
        } else {
            $seq = $this->sequence;
            if ($index && is_array($this->primaryKey)) {
                $seq = $this->primaryKey[$index];
            }
            $_seq = $this->schema ? "{$this->schema}.{$seq}" : $seq;
        }

        $sql = <<<SQL
            SELECT NEXTVAL('{$_seq}');
SQL;

        $stm = $this->getAdapter()->query($sql);

        $return = array();
        foreach ($stm->execute() as $_value) {
            $return = $_value;
        }

        return $return['nextval'];
    }

    /**
     *
     * @param type $name
     * @return \FS\Model\className
     */
    public function getService($name) {
        $className = ucfirst($name) . 'Service';
        $obj = new $className();
        $obj->setDbAdapter($this->getAdapter());
        return $obj;
    }

    /**
     *
     * @param type $removeAux
     * @return type
     */
    public function getMap($removeAux = true) {
        if ($removeAux) {
            $_map = array();
            $map = array_flip($this->_map);

            foreach ($map as $_attrDb => $_attrModel) {
                if (substr($_attrDb, 0, 3) != 'aux') {
                    $_map[$_attrModel] = $_attrDb;
                }
            }

            return $_map;
        } else {
            return $this->_map;
        }
    }

    /** @todo implementar total( ) // COUNT */
}
