<?php

namespace Rubix\Mvc;

use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Db\Adapter\Adapter;

abstract class Model extends \Zend\Stdlib\ArrayObject implements InputFilterAwareInterface {

    /**
     * DB Owner
     * @var string
     */
    public $owner;

    /**
     *
     * @var string
     */
    public $autocommit;

    /**
     *
     * @var boolean
     */
    public $transaction;

    /**
     *
     * @var type
     */
    public $adapter;

    /**
     *
     * @param \Zend\InputFilter\InputFilterInterface $inputFilter
     * @throws \Exception
     */
    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new \Exception("Not used");
    }

    /**
     *
     * @return \Rubix\Mvc\InputFilter
     */
    public function getInputFilter() {
        $inputFilter = new InputFilter();
        return $inputFilter;
    }

    /**
     *
     * @param Adapter $adapter
     */
    public function setDbAdapter(Adapter $adapter) {

        $this->adapter = $adapter;
        /* $this->table = new TableIdentifier($this->tableName, $this->schema);
          $this->resultSetPrototype = new HydratingResultSet();

          $this->initialize(); */
    }

    /**
     *
     * @return \Zend\Db\Adapter\Adapter
     */
    public function getAdapter() {
        return $this->adapter;
    }

    public function qi($name) {
        return $this->getAdapter()->getPlatform()->quoteIdentifier($name);
    }

    public function fp($name) {
        return $this->getAdapter()->getDriver()->formatParameterName($name);
    }

    public function getParamType($value) {
        if (is_string($value)) {
            return \PDO::PARAM_STR;
        }
        if (is_float($value) || is_int($value)) {
            return \PDO::PARAM_INT;
        }
        if (is_null($value)) {
            return \PDO::PARAM_NULL;
        }
        if (is_bool($value)) {
            return \PDO::PARAM_BOOL;
        }
        if (is_resource($value)) {
            return \PDO::PARAM_LOB;
        }
        if (is_null($value) || !$value) {
            return \PDO::PARAM_NULL;
        }
    }

    public function beginTransaction() {
        $this->autocommit = false;
        if (false === $this->transaction) {
            $this->transaction = true;
            return $this->getAdapter()->getDriver()->getConnection()->beginTransaction();
        }
    }

    public function commit() {
        if (true === $this->transaction) {
            $this->transaction = false;
            return $this->getAdapter()->getDriver()->getConnection()->commit();
        }
    }

    public function query($dml, $parameters = array(), $execute = true, $debug = false) {
        try {
            $dml = preg_replace('/\t+|\s+|\n|\r\n/', ' ', $dml);

            if (!$this->autocommit && preg_match('/(BEGIN|INSERT\s+INTO|UPDATE|DELETE\s+FROM)\s+([a-z0-1.]+)\s+(.*)/smui', $dml, $parts)) {
                $this->beginTransaction();
            }

            $statement = $this->getAdapter()->getDriver()->getConnection()->getResource()->prepare($dml, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
            $statement->setFetchMode(\PDO::FETCH_CLASS, 'Object');

            if (true === $execute) {

                $index = 1;
                foreach ($parameters as $_param => $_value) {
                    $statement->bindParam(is_int($_param) ? $index++ : $_param, $parameters[$_param], $this->getParamType($_value));
                }

                if (false == $statement->execute()) {
                    list($state, $code, $message) = $statement->errorInfo();

                    // @todo
                    //Logger::write(sprintf('%s: %s => %s', $state, $code, $message), 'connection.log');

                    throw new \PDOException($message, $code);
                }
            }

            if (true === $debug) {
                // @todo
                //$this->_trace(func_get_arg(0), $parameters);
                xd('implementar');
            }

            return $statement;
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function callProcedure($dml, $input = array(), $output = array(), $debug = false) {
        try {
            $stm = $this->prepareSql($dml);

            $index = 1;

            foreach ($input as $_param => $_value) {
                $stm->bindParam(is_int($_param) ? $index++ : $_param, $input[$_param], $this->getParamType($_value));
            }

            foreach ($output as $_param => $_value) {
                $stm->bindParam(is_int($_param) ? $index++ : $_param, $output[$_param], $this->getParamType($_value) | \PDO::PARAM_INPUT_OUTPUT, 4096);
            }

            $boSuccess = $stm->execute();

            if ($boSuccess) {
                $attributes = array_merge($input, $output);
                foreach ($attributes as $_attr => $_value) {
                    $this->offsetSet($_attr, $_value);
                }
            } else {
                list($state, $code, $message) = $stm->errorInfo();

                // @todo
                //Logger::write(sprintf('%s: %s => %s', $state, $code, $message), 'connection.log');

                throw new \PDOException($message, $code);
            }

            if (true === $debug) {
                // @todo implementar debug
                //$this->_connection->debug(trim(func_get_arg(0)), $attributes);
                xd('implementar');
            } else {
                $stm->closeCursor();
                $this->commit();
            }

            return $this->getArrayCopy();
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function findBySql($dml, $input = array(), $debug = false) {
        if (preg_match('/(INSERT\s+INTO|UPDATE|DELETE\s+FROM)\s+([a-z0-1.]+).*/smui', $dml, $parts)) {
            throw new Exception(sprintf('You can not use find* methods to run `%s` commands', $parts[1]));
        }

        return $this->query($dml, $input, true, $debug);
    }

    public function prepareSql($dml) {
        try {
            $stm = $this->query($dml, null, false);
            return $stm;
        } catch (Exception $e) {
            throw $e;
        }
    }

}
