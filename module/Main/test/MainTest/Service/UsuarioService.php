<?php

namespace MainTest\Service;

use Rubix\Mvc\ControllerTest;

class UsuarioService extends ControllerTest {

    public function fAutenticarUsuario() {
        //$abc = $this->getServiceLocator()->get('usuario')->fAutenticarUsuario(1,2,3,4);
        $this->assertResponseStatusCode(501);
    }

}
