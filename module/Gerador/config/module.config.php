<?php

return array(
    'router' => array(
        'routes' => array(
            'gerador' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/gerador[/:controller][/:action]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ),
                    'defaults' => array(
                        'controller' => 'gerador',
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
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'gerador' => __DIR__ . '/../view'
        )
    ),
);