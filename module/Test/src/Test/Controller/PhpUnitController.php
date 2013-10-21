<?php

namespace Test\Controller;

use Rubix\Mvc\Controller;

class PhpUnitController extends Controller {

    const PHPUNIT_PATH = '/usr/bin/phpunit';

    public function init() {

    }

    public function indexAction() {
        $module = $this->getParam('id');

        $cmd = 'cd ' . APPLICATION_PATH . "module/{$module}/test/;";
        $cmd.= self::PHPUNIT_PATH;

        if ($module) {
            $cmd.= " --testsuite {$module}";
        }

        $result = trim(shell_exec($cmd));
        preg_match_all('/Generating code coverage report in HTML format ... done/', $result, $done);

        if (count($done[0])) {
            preg_match_all('/No tests executed/', $result, $noExecutes);
            preg_match_all('/OK /', $result, $ok);
            preg_match_all('/FAILURES!/', $result, $failures);
            preg_match_all('/Errors: /', $result, $erros);

            if (count($ok[0])) {
                preg_match_all('(OK (?:.)(?P<tests>.*\d) (?:.*), (?P<asserts>.*\d))', $result, $totals);
            } elseif (count($noExecutes[0])) {
                $totals = array(
                    'tests' => array(0),
                    'asserts' => array(0),
                );
            } elseif (count($erros[0])) {
                preg_match_all('(Tests: (?P<tests>.*\d), Assertions: (?P<asserts>.*\d), Failures: (?P<failures>.*\d), Errors: (?P<errors>.*\d))', $result, $totals);
            } elseif (count($failures[0])) {
                preg_match_all('(Tests: (?P<tests>.*\d), Assertions: (?P<asserts>.*\d), Failures: (?P<failures>.*\d))', $result, $totals);
            }

            preg_match_all('(Time(?:.) (?P<time>.*\w) (?P<unitTime>.*\w), Memory(?:.) (?P<memory>.*\w))', $result, $spent);
            $this->view->setVariable('status', count($failures[0]) ? 'FALHA' : 'SUCESSO');
            $this->view->setVariable('tests', $totals['tests'][0]);
            $this->view->setVariable('asserts', $totals['asserts'][0]);
            $this->view->setVariable('failures', count($failures[0]) || count($erros[0]) ? $totals['failures'][0] : 0);
            $this->view->setVariable('errors', count($erros[0]) ? $totals['errors'][0] : 0);
            $this->view->setVariable('time', $spent['time'][0] . $spent['unitTime'][0]);
            $this->view->setVariable('memory', $spent['memory'][0]);
        }

        $this->view->setVariable('cmdResult', $result);

        $this->addCss('modules/main/home/index.css');
        return $this->view;
    }

}