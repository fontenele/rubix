<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Db\Sql\TableIdentifier;
use FS\ModuleManager\Module as BaseModule;

class Module extends BaseModule {

    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;

    public function getServiceConfig() {
        return array(
            'initializers' => $this->initializers,
            'invokables' => array(
                'usuarios' => 'Gerencial\Service\UsuariosService'
            ),
            'factories' => array(
                'SessionStorage' => function($sm) {
                    return new \FS\Session\Storage('fontesolutions');
                },
                'AuthStorage' => function($sm) {
                    $auth = new \FS\Auth\AuthStorage('fontesolutions_login');
                    return $auth;
                },
                'AuthService' => function($sm) {
                    return $this->authProcess($sm);
                },
            ),
        );
    }

    protected function authProcess(\Zend\ServiceManager\ServiceManager $sm) {
        $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, "usuarios", 'str_login', 'str_senha', 'MD5(?)');
        $dbTableAuthAdapter->setTableName(new TableIdentifier('usuarios', 'gerencial'));
        $authService = new AuthenticationService();
        $authService->setAdapter($dbTableAuthAdapter);
        $authService->setStorage($sm->get('AuthStorage'));

        if($authService->hasIdentity()) {
            //$objUsuario = unserialize($authService->getIdentity());
            //$objUsuario = $sm->get('usuarios')->getUsuario(null, $authService->getIdentity());
            //$authService->getStorage()->write($objUsuario);
        }

        return $authService;
    }

}
