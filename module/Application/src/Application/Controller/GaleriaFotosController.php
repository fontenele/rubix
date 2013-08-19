<?php

namespace Application\Controller;

use FS\Controller\Controller;

/**
 * Galeria de Imagens do Sistema
 *
 * @package Gerencial
 * @subpackage Controller
 * @version 1.0
 * @author Guilherme Fontenele
 * @copyright FonteSolutions
 */
class GaleriaFotosController extends Controller {

    protected $dirUpload;
    protected $dirUploadWeb;

    public function init() {
        $this->entity = new \FS\Entity\Entity('Application', 'imagens');
        $this->dirUpload = APPLICATION_DIR . 'public/upload/galeria-fotos/';
        $this->dirUploadWeb = APPLICATION_URL . 'upload/galeria-fotos/';
    }

    public function indexAction() {
        $layout = $this->setLayoutBlank();
        $this->view->setTemplate('application/galeria-fotos/index.phtml');

        $categorias = $this->getService('categoriasImagens')->fetchAll();

        $treeCategorias = new \FS\View\Tree('tree-categorias-imagens');
        $treeCategorias->setData($categorias);

        $form = new \Application\Form\ImagensForm('frm-imagens', $this->getServiceLocator(), $this->entity);
        $form->get('submit')->setValue($this->translate('Adicionar Imagem'));

        $this->view->categorias = $treeCategorias;
        $this->view->form = $form;

        return $this->view;
    }

    public function carregarFotosAction() {
        $categoria = (int) $this->getParam('categoria', 'int_categoria');
        $thumbs = (bool) $this->getParam('thumbs');

        if ($categoria) {
            $arCategorias = array();
            foreach ($this->getService('imagens')->fetchAll('int_categoria = ' . $categoria) as $image) {
                $image['url'] = $this->dirUploadWeb . $image['str_nome_original'] . ($thumbs ? '_tbn' : '');
                $arCategorias[] = $image;
            }
            echo \Zend\Json\Json::encode($arCategorias);
        }

        die;
    }

    public function carregarFotoAction() {
        $cod = (int) $this->getParam('id', 'int_cod');

        if ($cod) {
            $image = $this->getService('imagens')->get($cod)->getArrayCopy();
            $image['url'] = $this->dirUploadWeb . $image['nomeOriginal'];
            $size = getimagesize($this->dirUpload . $image['nomeOriginal']);
            $image['resolucao'] = "{$size[0]}x{$size[1]}";
            echo \Zend\Json\Json::encode($image);
        }

        die;
    }

    public function salvarFotoAction() {
        $img = $this->request->getFiles('galeria-fotos-img');
        if ($img) {
            switch ($img['error']) {
                case UPLOAD_ERR_EXTENSION:
                case UPLOAD_ERR_CANT_WRITE:
                case UPLOAD_ERR_NO_TMP_DIR:
                case UPLOAD_ERR_NO_FILE:
                case UPLOAD_ERR_PARTIAL:
                case UPLOAD_ERR_FORM_SIZE:
                case UPLOAD_ERR_INI_SIZE:
                    break;
                case UPLOAD_ERR_OK:
                    $idCategoria = $this->request->getPost('int_categoria');

                    $imagens = new \Application\Model\Imagens();

                    $form = new \Application\Form\ImagensForm('frm-imagens', $this->getServiceLocator(), $this->entity);
                    $form->setInputFilter($imagens->getInputFilter());

                    $objUsuario = $this->getAuthStorage()->read();
                    if ($objUsuario) {
                        $objUsuario = unserialize($objUsuario);
                    }

                    $nomeOriginal = md5(date("Y-m-d H:i:s", time()) . '-' . $img["name"] . '-' . $idCategoria);

                    $data = $this->request->getPost();
                    $data->set('int_usuario', $objUsuario->cod);
                    $data->set('int_categoria', $idCategoria);
                    $data->set('int_tamanho', $img['size']);
                    $data->set('str_nome_original', $nomeOriginal);

                    $form->setData($data);

                    if ($form->isValid()) {
                        $imagens->exchangeArray($form->getData());
                        $this->getService('imagens')->save($imagens);
                    }

                    move_uploaded_file($img['tmp_name'], $this->dirUpload . $nomeOriginal);

                    $resizeImage = new \FS\Image\Resize($this->dirUpload . $nomeOriginal);
                    $resizeImage->resizeImage(64, 64, 'crop');
                    $resizeImage->saveImage($this->dirUpload . $nomeOriginal . '_tbn');

                    echo \Zend\Json\Json::encode(array(
                        'status' => 'success',
                        'message' => 'Imagem cadastrada com sucesso.',
                        'file' => $this->dirUploadWeb . $nomeOriginal
                    ));

                    break;
            }
        }
        die;
    }

}
