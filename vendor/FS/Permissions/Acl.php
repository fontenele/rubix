<?php

namespace FS\Permissions;

use Zend\Permissions\Acl\Acl as BaseAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

class Acl extends BaseAcl {

    const ACL_COD_PERFIL_CONVIDADO = 22;

    protected $codPerfil;
    protected $acessos;

    public function setPerfil($codPerfil) {
        $this->codPerfil = $codPerfil;
        $this->addRole(new Role($codPerfil));
    }
    
    public function getPerfil() {
        return $this->codPerfil;
    }

    public function setAcessos($acessos) {
        $this->acessos = $acessos;
        $this->processAcessos();
    }

    protected function processAcessos() {
        foreach ($this->acessos as $_controller => $_actions) {
            $this->addResource(new Resource($_controller));
            $this->allow($this->codPerfil, $_controller, $_actions);
        }
    }

}
