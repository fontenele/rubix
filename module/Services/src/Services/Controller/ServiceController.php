<?php

namespace Services\Controller;

use Rubix\Mvc\ControllerService as SoapService;

class ServiceController extends SoapService {

    public function init() {
        $this->setUri(APPLICATION_URL . 'services/service/rubix');
        $this->setWsdlUri(APPLICATION_URL . 'services/service/rubix?wsdl');

        $this->setServiceClass('\Services\Service\OlaMundo');
        $this->setServiceName('RubixService');
    }

    public function rubixAction() {
        $this->handle();
    }

}