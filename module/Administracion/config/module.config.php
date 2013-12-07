<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'administracion' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/administracion',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Administracion\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            //'route'    => '/[:controller[/:action]]',
                            'route'    => '/[:controller[/:action[/:id]]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
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
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Administracion\Controller\Index' => 'Administracion\Controller\IndexController',
            'Administracion\Controller\Configuracion' => 'Administracion\Controller\ConfiguracionController',
            'Administracion\Controller\Modulos' => 'Administracion\Controller\ModulosController',
            'Administracion\Controller\Menus' => 'Administracion\Controller\MenusController',
            'Administracion\Controller\Submenus' => 'Administracion\Controller\SubmenusController',
            'Administracion\Controller\Grupo' => 'Administracion\Controller\GrupoController',
            'Administracion\Controller\Usuario' => 'Administracion\Controller\UsuarioController',
            'Administracion\Controller\Acceso' => 'Administracion\Controller\AccesoController'
        ),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/administracion'           => __DIR__ . '/../view/layout/layout.phtml',
            'administracion/index/index' => __DIR__ . '/../view/administracion/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
           'administracion'=> __DIR__ . '/../view',
           'partial' => __DIR__ . '/../view/partial',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
);
