<?php

namespace Gerencial\Controller;

use Zend\View\Model\ViewModel;
use FS\Controller\Controller;
use Gerencial\Form\MenusForm;
use Gerencial\Form\MenusItemsForm;
use Gerencial\Model\MenusItems;

/**
 * Menus do Sistema
 *
 * @package Gerencial
 * @subpackage Controller
 * @version 1.0
 * @author Guilherme Fontenele
 * @copyright FonteSolutions
 *
 * @property Gerencial\Service\MenusItemsService $menusItemsService
 */
class MenusController extends Controller {

    public $menusItemsService;
    public $menusItemsEntity;

    public function init() {

        $menuView = new ViewModel();
        $menuView->setTemplate('gerencial/index/menu_principal.phtml');
        $menuView->controller = $this->params('controller');
        $menuView->action = $this->params('action');
        $menuView->controllerAction = $this->getRequest()->getUri()->getPath();
        $this->view->addChild($menuView, 'menu_principal');

        $this->addExtraJavascript('jquery.jstree.js');

        $this->entity = new \FS\Entity\Entity('Gerencial', 'menus');
        $this->menusItemsEntity = new \FS\Entity\Entity('Gerencial', 'menusItems');
        $this->addBreadcrumb($this->translate('Menus'), '/gerencial/menus');
    }

    public function indexAction() {
        $codMenu = $this->getParam('id', 'int_menu') ? (int) $this->getParam('id', 'int_menu') : null;

        $arrMenusItems = $codMenu ? $this->getMenusItemsService()->fetchAll(array('menus_items.int_menu' => $codMenu)) : array();
        $this->view->menusItems = $this->getMenusItemsService()->prepararMenu($arrMenusItems, true);

        $form = new MenusItemsForm('menus_items', $this->getServiceLocator(), $this->menusItemsEntity);
        $form->setAttribute('action', '/gerencial/menus/edit');
        $form->get('submit')->setValue($this->translate('Alterar'));
        $form->get('int_menu')->setAttribute('value', $codMenu);

        $lstMenusItems = array();
        foreach ($arrMenusItems as $_menuItem) {
            if (!$_menuItem['int_cod_pai']) {
                $lstMenusItems[$_menuItem['int_cod']] = $_menuItem['str_label'];
            }
        }
        $form->get('int_cod_pai')->setOptions(array(
            'value_options' => array('' => 'Não informado') + $lstMenusItems,
        ));

        $this->view->form = $form;

        $form = new MenusForm('menus', $this->getServiceLocator(), $this->entity);
        $form->setAttribute('action', '/gerencial/menus/index');
        $form->get('submit')->setValue($this->translate('Selecionar'));
        $form->setAttribute('class', '');

        $this->view->formMenus = $form;
        $this->view->codMenu = $codMenu;

        return $this->view;
    }

    public function addAction() {

        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = $request->getPost();
            $form = new MenusItemsForm('menus_items', $this->getServiceLocator(), $this->menusItemsEntity);
            $form->get('int_menu')->setAttribute('value', $data->offsetGet('int_menu'));

            $arrMenusItems = $this->getMenusItemsService()->fetchAll(array('menus_items.int_menu' => $data->offsetGet('int_menu')));
            $lstMenusItems = array();

            foreach ($arrMenusItems as $_menuItem) {
                if (!$_menuItem['int_cod_pai']) {
                    $lstMenusItems[$_menuItem['int_cod']] = $_menuItem['str_label'];
                }
            }
            $form->get('int_cod_pai')->setOptions(array(
                'value_options' => array('' => 'Não informado') + $lstMenusItems,
            ));

            $menuItem = new MenusItems();
            $form->setInputFilter($menuItem->getInputFilter());
            $form->setData($data);

            if ($form->isValid()) {
                $menuItem->exchangeArray($form->getData());
                $cod = $this->getMenusItemsService()->save($menuItem);
            }
        }

        echo \Zend\Json\Json::encode($menuItem->getArrayCopy());
        die;
    }

    public function editAction() {

        $request = $this->getRequest();

        if ($request->isPost()) {

            $data = $request->getPost();
            $form = new MenusItemsForm('menus_items', $this->getServiceLocator(), $this->menusItemsEntity);
            $form->get('int_menu')->setAttribute('value', $data->offsetGet('int_menu'));

            $menuItem = new MenusItems();

            $arrMenusItems = $this->getMenusItemsService()->fetchAll(array('menus_items.int_menu' => $data->offsetGet('int_menu')));
            $lstMenusItems = array();
            foreach ($arrMenusItems as $_menuItem) {
                if (!$_menuItem['int_cod_pai']) {
                    $lstMenusItems[$_menuItem['int_cod']] = $_menuItem['str_label'];
                }
            }
            $form->get('int_cod_pai')->setOptions(array(
                'value_options' => array('' => 'Não informado') + $lstMenusItems,
            ));

            $form->setInputFilter($menuItem->getInputFilter());
            $form->setData($data);

            if ($form->isValid()) {
                $menuItem->exchangeArray($form->getData());
                $this->getMenusItemsService()->save($menuItem);
                $this->addSuccessMessage($this->translate('Item de Menu editado com sucesso.'));
            } else {
                $this->addErrorMessage($this->translate('Falha ao salvar Item de Menu.'));
            }
        }

        return $this->redir('gerencial', 'menus', 'index', $data->offsetGet('int_menu') ? array('id' => $data->offsetGet('int_menu')) : null);
    }

    public function deleteAction() {
        $cod = (int) $this->getParam('id', 'int_cod');

        if (!$cod) {
            $this->addErrorMessage($this->translate('Código não informado.'));
            return $this->redir('gerencial', 'menus', 'index');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $cod = (int) $request->getPost('int_cod');
            $codMenu = (int) $request->getPost('int_menu');
            $this->getMenusItemsService()->remove($cod);

            $this->addSuccessMessage($this->translate('Item de menu excluído com sucesso.'));
            return $this->redir('gerencial', 'menus', 'index', $codMenu ? array('id' => $codMenu) : null);
        }


        $layout = $this->layout();
        $layout->setTemplate('gerencial/menus/delete.phtml');
        $layout->menu = $this->getMenusItemsService()->get(array($cod));
    }

    public function carregarMenuItemAction() {
        $cod = (int) $this->getParam('id', 'int_cod');
        if ($cod) {
            echo \Zend\Json\Json::encode($this->getMenusItemsService()->get($cod)->getArrayCopy());
        }
        die;
    }

    public function gerarAction() {
        $menuPrincipal = $this->getMenusItemsService()->prepararMenu($this->getMenusItemsService()->fetchAll(array('menus_items.int_menu' => 1)), true);
        $menuDireita = $this->getMenusItemsService()->prepararMenu($this->getMenusItemsService()->fetchAll(array('menus_items.int_menu' => 2)), true);

        $cfgMenuPrincipal = new \Zend\Config\Config(array(), true);
        $cfgMenuDireita = new \Zend\Config\Config(array(), true);

        foreach ($menuPrincipal as $_pos => $_menu) {
            if (!isset($cfgMenuPrincipal->{'item-' . $_menu['int_cod']}) || !is_array($cfgMenuPrincipal->{'item-' . $_menu['int_cod']})) {
                $cfgMenuPrincipal->{'item-' . $_menu['int_cod']} = array();
            }

            $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->cod = $_menu['int_cod'];
            $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->codPai = $_menu['int_cod_pai'];
            $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->label = $_menu['str_label'];
            $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->target = $_menu['str_target'];
            $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->posicao = $_menu['int_posicao'];
            $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->codMenu = $_menu['int_menu'];
            $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->tipo = $_menu['str_tipo'];

            if (isset($_menu['items']) && count($_menu['items'])) {
                $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->items = array();

                foreach ($_menu['items'] as $_posFilho => $_menuFilho) {
                    if (!isset($cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}) || !is_array($cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']})) {
                        $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']} = array();
                    }

                    $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}->cod = $_menuFilho['int_cod'];
                    $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}->codPai = $_menuFilho['int_cod_pai'];
                    $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}->label = $_menuFilho['str_label'];
                    $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}->target = $_menuFilho['str_target'];
                    $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}->posicao = $_menuFilho['int_posicao'];
                    $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}->codMenu = $_menuFilho['int_menu'];
                    $cfgMenuPrincipal->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}->tipo = $_menuFilho['str_tipo'];
                }
            }
        }

        foreach ($menuDireita as $_pos => $_menu) {
            if (!isset($cfgMenuDireita->{'item-' . $_menu['int_cod']}) || !is_array($cfgMenuDireita->{'item-' . $_menu['int_cod']})) {
                $cfgMenuDireita->{'item-' . $_menu['int_cod']} = array();
            }

            $cfgMenuDireita->{'item-' . $_menu['int_cod']}->cod = $_menu['int_cod'];
            $cfgMenuDireita->{'item-' . $_menu['int_cod']}->codPai = $_menu['int_cod_pai'];
            $cfgMenuDireita->{'item-' . $_menu['int_cod']}->label = $_menu['str_label'];
            $cfgMenuDireita->{'item-' . $_menu['int_cod']}->target = $_menu['str_target'];
            $cfgMenuDireita->{'item-' . $_menu['int_cod']}->posicao = $_menu['int_posicao'];
            $cfgMenuDireita->{'item-' . $_menu['int_cod']}->codMenu = $_menu['int_menu'];
            $cfgMenuDireita->{'item-' . $_menu['int_cod']}->tipo = $_menu['str_tipo'];

            if (isset($_menu['items']) && count($_menu['items'])) {
                $cfgMenuDireita->{'item-' . $_menu['int_cod']}->items = array();

                foreach ($_menu['items'] as $_posFilho => $_menuFilho) {
                    if (!isset($cfgMenuDireita->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}) || !is_array($cfgMenuDireita->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']})) {
                        $cfgMenuDireita->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']} = array();
                    }

                    $cfgMenuDireita->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}->cod = $_menuFilho['int_cod'];
                    $cfgMenuDireita->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}->codPai = $_menuFilho['int_cod_pai'];
                    $cfgMenuDireita->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}->label = $_menuFilho['str_label'];
                    $cfgMenuDireita->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}->target = $_menuFilho['str_target'];
                    $cfgMenuDireita->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}->posicao = $_menuFilho['int_posicao'];
                    $cfgMenuDireita->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}->codMenu = $_menuFilho['int_menu'];
                    $cfgMenuDireita->{'item-' . $_menu['int_cod']}->items->{'item-' . $_menuFilho['int_cod']}->tipo = $_menuFilho['str_tipo'];
                }
            }
        }

        $writer = new \Zend\Config\Writer\Xml();
        $writer->toFile(APPLICATION_DIR . \Gerencial\Service\MenusService::DIR_MENU_PRINCIPAL, $cfgMenuPrincipal);
        $writer->toFile(APPLICATION_DIR . \Gerencial\Service\MenusService::DIR_MENU_DIREITA, $cfgMenuDireita);

        $this->addSuccessMessage($this->translate('Menus gerados com sucesso.'));

        return $this->redir('gerencial', 'menus', 'index');
    }

    /**
     * Recuperar MenusItems Service
     * @return Gerencial\Service\MenusItemsService
     */
    public function getMenusItemsService() {
        if (!$this->menusItemsService) {
            $this->menusItemsService = $this->getTable('menusItems');
        }
        return $this->menusItemsService;
    }

}
