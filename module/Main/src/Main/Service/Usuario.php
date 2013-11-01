<?php

namespace Main\Service;

use Rubix\Mvc\Service;

class Usuario extends Service {

    public function init() {

    }
    
    public function fAutenticarUsuario($user, $pass, $ip, $sessionID) {
        $result = $this->getModel('Main\Model\Usuario')->fAutenticarUsuario($user, $pass, $ip, $sessionID);
        unset($result['desSenha']);
        return $result;
    }

    public function fEncerrarSessaoUsuario($sessionID) {
        return $this->getModel('Main\Model\Usuario')->fEncerrarSessaoUsuario($sessionID);
    }

    public function fConsultarDadosConta($prkCliente, $desMatricula) {
        $stm = $this->getModel('Main\Model\Usuario')->fConsultarDadosConta($prkCliente, $desMatricula);
        $result = array();
        foreach($stm as $_result) {
            $result[$_result['DES_CAMPO']] = $_result['DES_VALOR'];
        }
        return $result;
    }

}