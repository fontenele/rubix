<?php

namespace Main\Controller;

use Rubix\Mvc\Controller;
use Main\Form\PerfilForm;
use Main\Entity\Perfis;
use Zend\Paginator\Paginator;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator as PaginatorAdapter;

class PerfilController extends Controller {

    /**
     * Init
     */
    public function init() {

    }

    public function indexAction() {
        $this->setViewMessages();

        $perfis = $this->getEntityManager()->createQueryBuilder()->select('p')->from('Main\Entity\Perfis', 'p');
        
        $doctrinePaginator = new DoctrinePaginator($perfis);
        $paginatorAdapter = new PaginatorAdapter($doctrinePaginator);

        $paginator = new Paginator($paginatorAdapter);

        $paginator->setCurrentPageNumber($this->request->getQuery('page'));
        $paginator->setItemCountPerPage(5);

        foreach($paginator as $perfil) {
            $acessos = $this->getEntityManager()->createQueryBuilder()
                    ->select('a')
                    ->from('Main\Entity\Acessos', 'a')
                    ->innerJoin('a.intPerfil', 'p')
                    ->where('p.intCod = ' . $perfil->id())
                    ->getQuery()
                    ->getArrayResult();
            $perfil->setColAcessos($acessos);
        }

        $this->view->setVariable('datagrid', $paginator);
        return $this->view;
    }

    public function addAction() {
        $this->setViewMessages();
        $form = new PerfilForm();

        $request = $this->getRequest();

        if ($request->isPost()) {
            $perfil = new Perfis();

            $form->setInputFilter($perfil->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $perfil->exchangeArray($form->getData());

                $this->getEntityManager()->persist($perfil);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addSuccessMessage(array('message' => 'Registro salvo com sucesso!'));
                return $this->redirect()->toUrl(APPLICATION_URL . 'main/perfil');
            } else {
                xd($form->getMessages());
            }
        }

        $this->view->setVariable('form', $form);
        return $this->view;
    }

    public function editAction() {
        $this->setViewMessages();
        $id = $this->getParam('id') ? (int) $this->getParam('id') : null;

        if ($id == null) {
            $this->flashMessenger()->addErrorMessage(array('message' => 'Parâmetro não informado.'));
            return $this->redirect()->toUrl(APPLICATION_URL . 'main/perfil');
        }

        $post = $this->getEntityManager()->find('\Main\Entity\Perfis', $id);
        $request = $this->getRequest();

        $form = new PerfilForm();
        $form->bind($post);

        if ($request->isPost()) {

            $form->setInputFilter($post->getInputFilter());
            $form->setData($request->getPost());

            if ($form->isValid()) {
                $this->getEntityManager()->persist($post);
                $this->getEntityManager()->flush();

                $this->flashMessenger()->addSuccessMessage(array('message' => 'Registro salvo com sucesso!'));
                return $this->redirect()->toUrl(APPLICATION_URL . 'main/perfil');
            } else {
                xd($form->getMessages());
            }
        }

        $this->view->setVariable('form', $form);
        $this->view->setVariable('id', $id);
        return $this->view;
    }

    public function removeAction() {
        $id = $this->getParam('id') ? (int) $this->getParam('id') : null;

        if ($id == null) {
            $this->flashMessenger()->addErrorMessage(array('message' => 'Parâmetro não informado.'));
            return $this->redirect()->toUrl(APPLICATION_URL . 'main/perfil');
        }

        $post = $this->getEntityManager()->find('\Main\Entity\Perfis', $id);
        $this->getEntityManager()->remove($post);
        $this->getEntityManager()->flush();

        $this->flashMessenger()->addSuccessMessage(array('message' => 'Registro removido com sucesso!'));
        return $this->redirect()->toUrl(APPLICATION_URL . 'main/perfil');
    }

    public function resourcesAction() {
        $this->setViewMessages();
        $id = $this->getParam('id') ? (int) $this->getParam('id') : null;

        if ($id == null) {
            $this->flashMessenger()->addErrorMessage(array('message' => 'Parâmetro não informado.'));
            return $this->redirect()->toUrl(APPLICATION_URL . 'main/perfil');
        }

        $acessos = array();
        $acessosBanco = $this->getEntityManager()->getRepository('\Main\Entity\Acessos')->findBy(array('intPerfil' => $id));

        foreach($acessosBanco as $acesso) {
            $acessos[] = $acesso->getStrNomeAcesso();
        }

        $perfil = $this->getEntityManager()->find('\Main\Entity\Perfis', $id);

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();

            $removidos = $this->getEntityManager()->createQueryBuilder()->delete('Main\Entity\Acessos', 'p')->where('p.intPerfil = ' . $id);
            $removidos->getQuery()->execute();

            $this->getEntityManager()->beginTransaction();
            $erro = false;

            // Salvar acessos
            foreach ($post as $resource => $status) {
                $acesso = new \Main\Entity\Acessos();
                $acesso->setIntPerfil($perfil);
                $acesso->setStrNomeAcesso($resource);

                $this->getEntityManager()->persist($acesso);
                $this->getEntityManager()->flush();

                if(!$acesso->getIntCod()) {
                    $this->getEntityManager()->rollback();
                    $erro = true;
                    break;
                }
            }

            if($erro) {
                $this->flashMessenger()->addErrorMessage(array('message' => 'Falha ao salvar acessos.'));
                return $this->redirect()->toUrl(APPLICATION_URL . 'main/perfil/resources/' . $id);
            }else{
                $this->getEntityManager()->commit();
                $this->flashMessenger()->addSuccessMessage(array('message' => 'Acessos salvos com sucesso!'));
                return $this->redirect()->toUrl(APPLICATION_URL . 'main/perfil/resources/' . $id);
            }
        }

        $allResources = $this->readResources();
        $resources = array_shift($allResources);
        $comments = array_shift($allResources);

        $this->view->setVariable('acessos', $acessos);
        $this->view->setVariable('resources', $resources);
        $this->view->setVariable('comments', $comments);
        $this->view->setVariable('perfil', $perfil);

        return $this->view;
    }

    protected function readResources() {
        $classForbidden = array(
            'Zend\Mvc\Controller\AbstractActionController',
        );
        $actionsForbidden = array(
            'init',
            'setViewMessages',
            'afterExecuteAction',
            'notFoundAction',
            'getMethodFromAction',
        );

        $modulesDir = APPLICATION_PATH . 'module/';
        $cfgControllers = include APPLICATION_PATH . 'config/autoload/controllers.php';
        $controllers = $comments = array();

        foreach ($cfgControllers['invokables'] as $alias => $controller) {
            $classe = new \ReflectionClass($controller);

            if (!$classe) {
                continue;
            }

            foreach ($classe->getMethods(\ReflectionMethod::IS_PUBLIC) as $metodo) {
                if (preg_match('/Action$/', $metodo->getName(), $res) && !in_array($metodo->getName(), $actionsForbidden) && !in_array($metodo->class, $classForbidden)) {
                    // Parse resource name
                    $filter = new \Zend\Filter\Word\CamelCaseToDash();
                    $methodName = strtolower($filter->filter(substr($metodo->name, 0, -6)));

                    $route = explode('\\', $controller);
                    $module = strtolower($route[0]);

                    $resource = "{$module}|{$alias}|{$methodName}";
                    $controllers[] = $resource;

                    // Parse comments
                    $_comments = trim(str_replace(array('/', '*', '  '), '', $metodo->getDocComment()));
                    $_comments = explode("\n", $_comments);

                    if (trim($_comments[0])) {
                        $comments[$resource] = trim($_comments[0]);
                    }
                }
            }
        }

        return array($controllers, $comments);
    }

}
