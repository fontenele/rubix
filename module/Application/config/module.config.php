<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'home',
                        'action' => 'index',
                    ),
                ),
            ),
            'quem-somos' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/quem-somos',
                    'defaults' => array(
                        'controller' => 'home',
                        'action' => 'quemSomos',
                    ),
                ),
            ),
            'servicos' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/servicos',
                    'defaults' => array(
                        'controller' => 'home',
                        'action' => 'servicos',
                    ),
                ),
            ),
            'clientes' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/clientes',
                    'defaults' => array(
                        'controller' => 'home',
                        'action' => 'clientes',
                    ),
                ),
            ),
            'apps-ios' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/apps-ios',
                    'defaults' => array(
                        'controller' => 'home',
                        'action' => 'appsIos',
                    ),
                ),
            ),
            'sistemas-gestao' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/sistemas-gestao',
                    'defaults' => array(
                        'controller' => 'home',
                        'action' => 'sistemasGestao',
                    ),
                ),
            ),
            'sites-empresariais' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/sites-empresariais',
                    'defaults' => array(
                        'controller' => 'home',
                        'action' => 'sitesEmpresariais',
                    ),
                ),
            ),
            'advergames' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/advergames',
                    'defaults' => array(
                        'controller' => 'home',
                        'action' => 'advergames',
                    ),
                ),
            ),
            'suporte' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/suporte',
                    'defaults' => array(
                        'controller' => 'home',
                        'action' => 'suporte',
                    ),
                ),
            ),
            'contato' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/contato',
                    'defaults' => array(
                        'controller' => 'home',
                        'action' => 'contato',
                    ),
                ),
            ),
            'try-login' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/try-login',
                    'defaults' => array(
                        'controller' => 'home',
                        'action' => 'tryLogin',
                    ),
                ),
            ),
            'try-logout' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/try-logout',
                    'defaults' => array(
                        'controller' => 'home',
                        'action' => 'tryLogout',
                    ),
                ),
            ),
            'galeria-fotos' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/galeria-fotos[/:action][/:id]',
                    'constraints' => array(
                        'controller' => '[/:controller]',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'galeriaFotos',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Wildcard',
                        'options' => array(
                        ),
                    ),
                ),
            ),
            'categorias-imagens' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/categorias-imagens[/:action][/:id]',
                    'constraints' => array(
                        'controller' => '[/:controller]',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'categoriasImagens',
                        'action' => 'index'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Wildcard',
                        'options' => array(
                        ),
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
        ),
        'invokables' => array(
            'Imagens' => 'Application\Service\ImagensService',
            'CategoriasImagens' => 'Application\Service\CategoriasImagensService',
        ),
    ),
    'translator' => array(
        //'locale' => 'en_US',
        'locale' => 'pt_BR',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            //'Application\Controller\Index' => 'Application\Controller\IndexController',
            'home' => 'Application\Controller\IndexController',
            'galeriaFotos' => 'Application\Controller\GaleriaFotosController',
            'categoriasImagens' => 'Application\Controller\CategoriasImagensController',
        ),
        'factories' => array(
            //'Application\Controller\Index' => 'Application\Controller\IndexController',
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'layout' => 'layout/layout',
    ),
    'view_helpers' => array(
        'invokables' => array(
            'datetime' => 'FS\View\Helper\DateFormat',
            //'lerArtigo' => 'Artigos\View\Helper\LerArtigo',
        ),
        'factories' => array(
            'lerArtigo' => function ($helperPluginManager) {
                $serviceLocator = $helperPluginManager->getServiceLocator();
                $viewHelper = new \Artigos\View\Helper\LerArtigo();
                $viewHelper->setServiceLocator($serviceLocator);
                return $viewHelper;
            }
        )
    ),
);
