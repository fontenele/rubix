<?php

namespace Rubix\Session;

use Zend\Authentication\Storage\Session;

class Storage extends Session {

    public function __construct($namespace = null, $member = null, SessionManager $manager = null, $time = 1209600) {
        parent::__construct($namespace, $member, $manager);
        if(!isset($this->session->{$this->namespace})) {
            $this->session->{$this->namespace} = new \stdClass();
        }
        $this->session->getManager()->rememberMe($time);
    }

    public function add($var, $value) {
        $this->session->{$this->namespace}->{$var} = $value;
    }

    public function get($var) {
        return $this->has($var) ? $this->session->{$this->namespace}->{$var} : null;
    }

    public function has($var) {
        return (bool) isset($this->session->{$this->namespace}->{$var});
    }

    public function clear($var = null) {
        if($var) {
            unset($this->session->{$this->namespace}->{$var});
        }else{
            unset($this->session->{$this->namespace});
        }
    }
}