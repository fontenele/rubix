<?php

namespace Services\Service;

class OlaMundo {

    /**
     * Método teste Welcame
     * @return string retorno
     */
    public function welcame() {
        $opa = "Ola ddmundod!";
        return $opa;
    }
    
    /**
     * ABC,ABC,toda criança...
     * @param integer
     * @return string mensagem
     */
    public function abc($opa) {
        return "Ola ssmundo! {$opa}!!!";
    }
    
    /**
     * 
     * @param integer $id
     * @param string $nome
     * @param string $email
     * @return array usuario
     */
    public function getUsuario($id, $nome, $email) {
        $cc = new Teste();
        $cc->abc = 'Fontenele';
        
        $d = array();
        $d[] = array(1 => "aaaaaa");
        $d[] = array(2 => "bbb");
        $d[] = array(3 => "ccccccccccc");
        
        return array(
            'a' => '1',
            'bb' => '2',
            'c' => array(
                'a1' => 'opaa',
                'a2' => $cc,
                'a3' => $d
            )
        );
    }

}

class Teste {
    public $name = 'teste123';
    public $abc;
}
