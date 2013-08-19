<?php

return array(
    'router' => array(
        'routes' => array(
            'gerencial' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/gerencial[/:controller][/:action][/:id]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id' => '[0-9]+'
                    ),
                    'defaults' => array(
                        'controller' => 'gerencial',
                        'action' => 'index'
                    )
                )
            ),
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'gerencial' => __DIR__ . '/../view'
        )
    ),
);