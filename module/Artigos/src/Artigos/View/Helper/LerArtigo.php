<?php

namespace Artigos\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceManager;

class LerArtigo extends AbstractHelper {

    const LIMITE_MAX_ARTIGO = 200;

    /**
     * Service Locator
     * @var ServiceManager
     */
    protected $serviceLocator;

    /*public function __construct() {

    }*/

    public function __invoke($id, $returnHtmlBlock = true) {
        $artigo = $this->getServiceLocator()->get('artigos')->get($id);
        $return = null;

        if($returnHtmlBlock) {
            $return = $this->generateHtmlBlock($artigo);
        }

        return $return;
    }

    protected function generateHtmlBlock($row) {
        $conteudo = strlen(trim($row->conteudo)) > self::LIMITE_MAX_ARTIGO ? substr(trim($row->conteudo), 0, self::LIMITE_MAX_ARTIGO) . '...' : $row->conteudo;
        $html = <<<HTML
                <div class="thumbnail well">
                    <div class="caption">
                        <h3 class="fs-red">{$row->chamada}</h3>
                        <div>{$conteudo}</div>
                        <div class="div-artigo-leia-mais">
                            <a class="bt-artigo-leia-mais btn btn-danger pull-right" href="./artigos/artigo/read/{$row->cod}">Leia mais &raquo;</a>
                        </div>
                        <span class="clearfix"></span>
                    </div>
                </div>
HTML;
         return $html;
    }

    /**
     * Setter for $serviceLocator
     * @param ServiceManager $serviceLocator
     */
    public function setServiceLocator(ServiceManager $serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

}
