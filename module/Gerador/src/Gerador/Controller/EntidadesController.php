<?php

namespace Gerador\Controller;

//use Zend\View\Model\ViewModel;
use FS\Controller\Controller;

/**
 * Gerador de Entidades do Sistema (CRUD)
 *
 * @package Gerencial
 * @subpackage Controller
 * @version 1.0
 * @author Guilherme Fontenele
 * @copyright FonteSolutions
 *
 * @property Gerencial\Service\AcessosService $acessosService
 */
class EntidadesController extends Controller {

    public function init() {
        $this->addBreadcrumb('Entidades', '/gerador/entidades');
    }

    /**
     * Tela principal de entidades do gerador
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction() {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $metadata = new \Zend\Db\Metadata\Metadata($adapter);
        $map = $classes = array();

        $schemas = $metadata->getSchemas();

        foreach ($schemas as $_schema) {
            if (!isset($map[$_schema])) {
                $map[$_schema] = array();
                $classes[$_schema] = array();
            }

            $tables = $metadata->getTables($_schema);

            foreach ($tables as $_table) {
                $filter = new \Zend\Filter\Word\UnderscoreToCamelCase();
                $nmEntidade = ucfirst($filter->filter($_table->getName()));

                $nmController = ucfirst($_schema) . '\\Controller\\' . $nmEntidade;
                $nmForm = ucfirst($_schema) . '\\Form\\' . $nmEntidade;
                $nmService = ucfirst($_schema) . '\\Service\\' . $nmEntidade;
                $nmModel = ucfirst($_schema) . '\\Model\\' . $nmEntidade;

                $pathController = ucfirst($_schema) . '/src/' . ucfirst($_schema) . '/Controller/' . $nmEntidade . 'Controller.php';
                $pathForm = ucfirst($_schema) . '/src/' . ucfirst($_schema) . '/Form/' . $nmEntidade . 'Form.php';
                $pathService = ucfirst($_schema) . '/src/' . ucfirst($_schema) . '/Service/' . $nmEntidade . 'Service.php';
                $pathModel = ucfirst($_schema) . '/src/' . ucfirst($_schema) . '/Model/' . $nmEntidade . '.php';

                if (!isset($map[$_schema][$_table->getName()])) {
                    $map[$_schema][$_table->getName()] = array();
                    $classes[$_schema][$_table->getName()] = array();
                }

                if (file_exists(APPLICATION_DIR . "/module/{$pathController}")) {
                    $classes[$_schema][$_table->getName()]['controller'][] = $nmController;
                }
                if (file_exists(APPLICATION_DIR . "/module/{$pathForm}")) {
                    $classes[$_schema][$_table->getName()]['form'][] = $nmForm;
                }
                if (file_exists(APPLICATION_DIR . "/module/{$pathService}")) {
                    $classes[$_schema][$_table->getName()]['service'][] = $nmService;
                    $service = $nmService . 'Service';
                    $service = new $service();
                    $_map = $service->_map;
                    $mapFiltered = array();
                    foreach ($_map as $_attributo => $_coluna) {
                        if (substr($_coluna, 0, 3) != 'aux') {
                            $mapFiltered[$_attributo] = $_coluna;
                        }
                    }
                    $classes[$_schema][$_table->getName()]['service'][] = $mapFiltered;
                }
                if (file_exists(APPLICATION_DIR . "/module/{$pathModel}")) {
                    $classes[$_schema][$_table->getName()]['model'][] = $nmModel;
                }

                $columns = $_table->getColumns();

                foreach ($columns as $_column) {
                    $map[$_schema][$_table->getName()][] = $_column->getName();
                }
            }
        }

        $this->view->setVariable('map', $map);
        $this->view->setVariable('classes', $classes);

        return $this->view;
    }

    public function addAction() {
        $filter = new \Zend\Filter\Word\UnderscoreToCamelCase();
        $schemaName = $this->params()->fromRoute('schema');
        $tableName = $this->params()->fromRoute('table');

        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $metadata = new \Zend\Db\Metadata\Metadata($adapter);
        $map = $classes = array();

        $colunas = $this->getService('entidades')->getColunas($schemaName, $tableName);
        $this->view->colunas = $colunas;

        $camellizer = new \Zend\Filter\Word\UnderscoreToCamelCase();
        $this->view->nmModulo = $camellizer->filter($schemaName);
        $this->view->nmEntidade = $camellizer->filter($tableName);


        //xd($schemaName, $tableName,$colunas);

//        $table = $metadata->getTable($tableName, $schemaName);
//
//        $nmEntidade = ucfirst($filter->filter($table->getName()));
//
//        $nmController = ucfirst($schemaName) . '\\Controller\\' . $nmEntidade;
//        $nmForm = ucfirst($schemaName) . '\\Form\\' . $nmEntidade;
//        $nmService = ucfirst($schemaName) . '\\Service\\' . $nmEntidade;
//        $nmModel = ucfirst($schemaName) . '\\Model\\' . $nmEntidade;
//
//        $pathController = ucfirst($schemaName) . '/src/' . ucfirst($schemaName) . '/Controller/' . $nmEntidade . 'Controller.php';
//        $pathForm = ucfirst($schemaName) . '/src/' . ucfirst($schemaName) . '/Form/' . $nmEntidade . 'Form.php';
//        $pathService = ucfirst($schemaName) . '/src/' . ucfirst($schemaName) . '/Service/' . $nmEntidade . 'Service.php';
//        $pathModel = ucfirst($schemaName) . '/src/' . ucfirst($schemaName) . '/Model/' . $nmEntidade . '.php';

        //xd($nmController,$pathController,$nmForm,$pathForm, $nmService, $nmModel);

        //xd($schema, $table);
        return $this->view;
    }

}
