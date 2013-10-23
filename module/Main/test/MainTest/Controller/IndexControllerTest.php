<?php

namespace MainTest\Controller;

use Rubix\Mvc\ControllerTest;

class IndexControllerTest extends ControllerTest {

    public function testIndexAction() {
        $this->dispatch('/main/home/index');

        $this->assertResponseStatusCode(302);
        
        $this->assertModuleName('Main');
        $this->assertControllerName('home');
        $this->assertControllerClass('IndexController');
    }

    public function testLoginAction() {
        $b = $this->dispatch('/main/home/login');
        $this->assertEquals(null, $b);

        $this->assertResponseStatusCode(200);
        
        $this->assertModuleName('Main');
        $this->assertControllerName('home');
        $this->assertControllerClass('IndexController');
    }

    public function testTryLoginAction() {
        $this->dispatch('/main/home/try-login');
        
        $this->assertResponseStatusCode(302);
        $this->assertModuleName('Main');
        $this->assertControllerName('home');
        $this->assertControllerClass('IndexController');

        $this->reset();

        $post = array('usuario' => 'fontenele', 'senha' => '12345');
        $this->dispatch('/main/home/try-login', 'POST', $post);
        
        $this->dispatch('/main/home/index');
        
        $this->reset();
        
        $post = array('usuario' => 'fontenele', 'senha' => '11111');
        $this->dispatch('/main/home/try-login', 'POST', $post);

        $this->assertResponseStatusCode(302);
        $this->assertModuleName('Main');
        $this->assertControllerName('home');
        $this->assertControllerClass('IndexController');

        $this->dispatch('/main/home/index');
        $this->dispatch('/main/home/do-logout');
        
        $this->reset();
    }

}
