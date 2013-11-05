<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Administracion\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Administracion\Model\Entity\Config;
use Administracion\Model\Entity\Usuariogrupo;
use Administracion\Model\Entity\Gruposubmenu;
use Administracion\Model\Entity\Menu;
use Administracion\Model\Entity\Submenu;
use Administracion\Model\Entity\Modulo;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;

class IndexController extends AbstractActionController {

    public $dbAdapter;

    /**
     * atributo que sirve para identificar la pertenencia del controlador
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @var string 
     */
    private $name_module = 'administracion';

    /**
     * atributo para indicar que submenu se está ejecutando
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @var int 
     */
    private $submenu_active = 0;

    public function indexAction() {
        $uri = $this->getRequest()->getUri();
        $base = sprintf('%s://%s', $uri->getScheme(), $uri->getHost());
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $session = new Container('seguridad');
        if (!$session->offsetExists('user')) {
            $this->redirect()->toRoute('seguridad');
        }
        $this->layout('layout/administracion');
        //listamos el objeto configuración
        $config = new Config($this->dbAdapter);
        //$modulos = $this->getModulo($session->user->id);
        $modulos = $this->getMenu($session->user->id);
        return new ViewModel(array(
            "objUser" => $session->user,
            "config" => $config->getConfig(),
            "package" => $this->name_module,
            "modulo" => $modulos,
            "url" => $base
        ));
    }

    /**
     * listaremos los modulos que tiene acceso este usuario
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $user_id
     * @return array
     */
    public function getModulo($user_id) {
        $returnValue = array();
        //listamos los grupos al que pertenece el usuario
        $tabla_usuario_grupo = new Usuariogrupo($this->dbAdapter);
        $usuario_grupos = $tabla_usuario_grupo->getUsuariogrupo($user_id);
        //verificamos que tenga grupos 
        if (count($usuario_grupos) > 0) {
            $array_grupo_id = array();
            foreach ($usuario_grupos as $index => $arrayGrupo) {
                array_push($array_grupo_id, $arrayGrupo['int_grupo_id']);
            }
            if (count($array_grupo_id) > 0) {
                //listamos los submenus que tenga asignados
                $tabla_grupo_submenu = new Gruposubmenu($this->dbAdapter);
                $grupo_submenu = $tabla_grupo_submenu->getGrupoSubmenu($array_grupo_id);
                //verificamos si tiene submenus para listar los menus
                if (count($grupo_submenu) > 0) {
                    $array_submenu_id = array();
                    foreach ($grupo_submenu as $index => $arrayGrupo) {
                        array_push($array_submenu_id, $arrayGrupo['int_submenu_id']);
                    }
                    if (count($array_submenu_id) > 0) {
                        //listamos los menus que tenga asignado el usuario
                        $objMenu = new Menu($this->dbAdapter);
                        $menus = $objMenu->getMenuByIdSubmenu($array_submenu_id);
                        //validamos que tenga menus para listar los modulos
                        if (count($menus) > 0) {
                            $array_modulo_id = array();
                            foreach ($menus as $indice => $menu) {
                                array_push($array_modulo_id, $menu['int_modulo_id']);
                            }
                            //verificamos si existen menus
                            if (count($array_modulo_id) > 0) {
                                $objModulo = new Modulo($this->dbAdapter);
                                $returnValue = $objModulo->getModulo($array_modulo_id);
                            }
                        }
                    }
                }
            }
        }
        return $returnValue;
    }

    /**
     * obtenemos el listado del menu superior del sistema añadiendo la pripiedad de activo
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $user_id
     * @return array
     */
    public function getMenu($user_id, $name_module = '') {
        $returnValue = array();
        if (strlen(trim($name_module)) == 0) {
            $name_module = $this->name_module;
        }
        $arrayModulos = $this->getModulo($user_id);
        if (count($arrayModulos) > 0) {
            foreach ($arrayModulos as $index => $modulo) {
                if ($modulo['chr_nombre'] == $this->name_module) {
                    $modulo['menu'] = $this->getMenuByIdModuloIdUser($modulo['id'], $user_id);
                    if ($modulo['chr_nombre'] == $name_module) {
                        $modulo['active'] = TRUE;
                    } else {
                        $modulo['active'] = FALSE;
                    }
                    $arrayModulos[$index] = $modulo;
                }
            }
            $returnValue = $arrayModulos;
        }
        return $returnValue;
    }

    /**
     * Método para listar los menus y submenus  de cada modulo que ingresa como parametro
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $modulo_id
     * @param int $user_id
     * @return array
     */
    private function getMenuByIdModuloIdUser($modulo_id, $user_id) {
        $returnValue = array();
        $objMenu = new Menu($this->dbAdapter);
        $arrayMenus = $objMenu->getMenuByIdModuloByUserId($modulo_id, $user_id);
        if (count($arrayMenus) > 0) {
            foreach ($arrayMenus as $index => $menu) {
                $objSubmenu = new Submenu($this->dbAdapter);
                $menu['submenu'] = $objSubmenu->getSubmenuByIdMenuByUser($menu['id'], $user_id);
                $arrayMenus[$index] = $menu;
            }
            $returnValue = $arrayMenus;
        }
        return $returnValue;
    }

    public function setDbAdapter($adapter) {
        $this->dbAdapter = $adapter;
    }

    /**
     * Metodo para registrar el nombre del modulo que se listará
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param string $nameModule
     */
    public function setNameModule($nameModule) {
        $this->name_module = $nameModule;
    }

    /**
     * método para setear el valor del submenu activo
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $submenu
     */
    public function setSubmenuActive($submenu) {
        $this->submenu_active = $submenu;
    }

    /**
     * Método para imprimir variables
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param string $var
     */
    private function vd($var) {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

}
