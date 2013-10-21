<?php

namespace MainTest\Controller;

use Rubix\Mvc\ControllerTest;
//use Zend\Session\Container;

class IndexControllerTest extends ControllerTest {

    public function testIndexAction() {
        $this->dispatch('/main/home/index');

        $this->assertResponseStatusCode(500);
        
        $this->assertModuleName('Main');
        $this->assertControllerName('home');
        $this->assertControllerClass('IndexController');

        /*$this->reset();

        $this->dispatch('/pagina/nao/existe');

        $this->assertResponseStatusCode(404);
        $this->assertModuleName('Main');
        $this->assertControllerName('home');
        $this->assertControllerClass('IndexController');*/
    }

    /*public function testLoginAction() {
        $b = $this->dispatch('/main/home/login');
        $this->assertEquals(null, $b);

        $this->assertResponseStatusCode(500);
        //$this->assertResponseStatusCode(404);
        $this->assertModuleName('Main');
        $this->assertControllerName('home');
        $this->assertControllerClass('IndexController');
    }*/

    /*public function testTryLoginAction() {
        $this->dispatch('/main/home/try-login');
        
        $this->assertResponseStatusCode(302);
        $this->assertModuleName('Main');
        $this->assertControllerName('home');
        $this->assertControllerClass('IndexController');

        $this->reset();

        $post = array('usuario' => 'fontenele', 'senha' => '12345');
        $this->dispatch('/main/home/try-login', 'POST', $post);

        $this->assertResponseStatusCode(302);
        $this->assertModuleName('Main');
        $this->assertControllerName('home');
        $this->assertControllerClass('IndexController');

        $this->reset();
    }*/

    /*public function testDoLogoutAction() {
        $this->dispatch('/main/home/do-logout');

        $this->assertResponseStatusCode(302);
        $this->assertModuleName('Main');
        $this->assertControllerName('home');
        $this->assertControllerClass('IndexController');
    }*/

}
