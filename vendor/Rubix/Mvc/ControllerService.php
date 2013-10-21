<?php

namespace Rubix\Mvc;

ini_set("soap.wsdl_cache_enabled", 0);

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Rubix\Soap\AutoDiscover;
use Rubix\Http\Browser;

abstract class ControllerService extends AbstractActionController {

    /**
     * @var string
     */
    protected $serviceName;

    /**
     * @var string
     */
    protected $serviceClass;

    /**
     * @var string
     */
    protected $_URI;

    /**
     * @var string
     */
    protected $_WSDL_URI;

    /**
     * @var Acl
     */
    protected static $acl;

    /**
     *  Método de inicialização
     */
    abstract public function init();

    /**
     * Construtor
     */
    public function __construct() {

    }

    /**
     *
     */
    final public function configure() {
        $this->init();
    }

    /**
     *
     */
    private function configureAcl() {
        if (!self::$acl) {

        }
    }

    /**
     * Execute the request
     *
     * @param  MvcEvent $e
     * @return mixed
     * @throws Exception\DomainException
     */
    protected function attachDefaultListeners() {
        parent::attachDefaultListeners();

        $events = $this->getEventManager();
        $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'afterExecuteAction'));
    }

    /**
     *
     */
    public function afterExecuteAction() {

    }

    /**
     *
     * @return type
     */
    protected function isAllowed() {
        /* if (APPLICATION_LOCKED) {
          $action = "{$this->params('action')}Action";
          return self::$acl->hasResource(get_class($this)) ? self::$acl->isAllowed(self::$acl->getPerfil(), get_class($this), $action) : false;
          } else {
          return true;
          } */
        return true;
    }

    /**
     *
     */
    public function verifyIsAllowed() {
        if (!$this->isAllowed()) {
            // message to die
            die();
        }
    }

    /**
     *
     * @param type $strController
     * @return type
     */
    private function explodeController($strController) {
        return explode('\\', $strController);
    }

    /**
     *
     * @param type $strController
     * @return type
     */
    public function getModuleName($strController = null) {
        if (!$strController) {
            $strController = get_class($this);
        }

        $arrController = $this->explodeController($strController);

        if (count($arrController) == 3) {
            return array_shift($arrController);
        }
    }

    /**
     *
     * @param type $model
     * @return
     */
    public function getService($service) {
        return $this->getServiceLocator()->get($service);
    }

    /**
     *
     * @param type $parameter
     * @param type $postParameter
     * @return type
     */
    public function getParam($parameter, $postParameter = null) {
        if ($postParameter) {
            $postParameter = $this->getRequest()->getPost()->get($postParameter);
        }
        return $this->params()->fromRoute($parameter, $postParameter);
    }

    public function getIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //check ip from share internet
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //to check ip is pass from proxy
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }

    /**
     * Generate AutoDiscover Service
     * @param string $serviceClass
     * @param string $serviceName
     * @return \Zend\Soap\AutoDiscover
     */
    protected function generate($serviceClass, $serviceName = null) {
        $autodiscover = new AutoDiscover();
        $autodiscover->setClass($serviceClass)
                ->setWsdlClass('\Rubix\Soap\Wsdl')
                ->setUri($this->_URI)
                ->setOperationBodyStyle(array('use' => 'literal'));

        if ($serviceName) {
            $autodiscover->setServiceName($serviceName);
        }

        return $autodiscover;
    }

    /**
     * Handle SOAP/WSDL/View
     */
    public function handle() {
        if (isset($_GET['wsdl'])) {
            $this->handleWSDL();
        } else {
            $browser = Browser::getBrowser();
            $key = array_keys($browser);

            if ($key && is_array($key) && in_array($key[0], Browser::$known)) {
                $this->viewWSDL();
            } else {
                $this->handleSOAP();
            }
        }

        $view = new ViewModel();
        $view->setTerminal(true);
        exit();
    }

    /**
     * Handle WSDL
     */
    public function handleWSDL() {
        $autodiscover = $this->generate($this->serviceClass, $this->serviceName);
        $wsdl = $autodiscover->generate();
        header("Content-type: text/xml");
        echo $wsdl->toXML();
    }

    public function handleSOAP() {
        $soap = new \Zend\Soap\Server($this->_WSDL_URI);
        $soap->setClass($this->serviceClass);
        $soap->handle();
    }

    public function viewWsdl() {
        $autodiscover = $this->generate($this->serviceClass, $this->serviceName);
        $wsdl = $autodiscover->generate();

        $reader = new \Zend\Config\Reader\Xml();
        $_arrWsdl = $reader->fromString($wsdl->toXML());
        $arrWsdl = array();

        foreach ($_arrWsdl['wsdl:portType']['wsdl:operation'] as $arrMetodo) {
            $name = $arrMetodo['name'];
            $arrWsdl[$name]['doc'] = $arrMetodo['wsdl:documentation'];
            $arrWsdl[$name]['input'] = array();
            $arrWsdl[$name]['output'] = array();
        }

        foreach ($_arrWsdl['wsdl:message'] as $arrMessage) {
            $params = array();

            switch (true) {
                // Input
                case substr($arrMessage['name'], -2) == 'In':
                    $name = substr($arrMessage['name'], 0, -2);

                    if (isset($arrMessage['wsdl:part'])) {

                        if (isset($arrMessage['wsdl:part']['name'])) {
                            $params[] = array(
                                'name' => $arrMessage['wsdl:part']['name'],
                                'type' => $arrMessage['wsdl:part']['type']
                            );
                        } else {
                            foreach ($arrMessage['wsdl:part'] as $messagePart) {
                                $params[] = $messagePart;
                            }
                        }
                    }

                    $arrWsdl[$name]['input'] = $params;
                    break;

                // Output
                case substr($arrMessage['name'], -3) == 'Out':
                    $name = substr($arrMessage['name'], 0, -3);

                    if (isset($arrMessage['wsdl:part'])) {

                        if (isset($arrMessage['wsdl:part']['name'])) {
                            $params[] = array(
                                'name' => $arrMessage['wsdl:part']['name'],
                                'type' => $arrMessage['wsdl:part']['type']
                            );
                        } else {
                            foreach ($arrMessage['wsdl:part'] as $messagePart) {
                                $params[] = $messagePart;
                            }
                        }
                    }

                    $arrWsdl[$name]['output'] = $params;
                    break;
            }
        }

        $view = new ViewModel();
        $view->setTemplate('soap/wsdl.phtml');
        $view->setVariable('wsdl', $arrWsdl);
        $view->setVariable('serviceName', $this->serviceName);

        $renderer = $this->getServiceLocator()->get('Zend\View\Renderer\PhpRenderer');
        echo $renderer->render($view);
    }

    /**
     * @param string $uri
     */
    public function setUri($uri) {
        $this->_URI = $uri;
    }

    /**
     * @param string $wsdlUri
     */
    public function setWsdlUri($wsdlUri) {
        $this->_WSDL_URI = $wsdlUri;
    }

    /**
     * @param string $serviceName
     */
    public function setServiceName($serviceName) {
        $this->serviceName = $serviceName;
    }

    /**
     * @param string $serviceClass
     */
    public function setServiceClass($serviceClass) {
        $this->serviceClass = $serviceClass;
    }

}