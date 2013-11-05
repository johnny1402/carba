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
use Administracion\Model\Entity\Submenu;
use Administracion\Model\Entity\Menu;
use Administracion\Model\Entity\Modulo;
use Zend\Json\Json;
use Administracion\Form\FormMenu;
use Administracion\Form\FormMenuValidate;

/**
 * Clase para trabajar las varibles de configuración del sistema
 * @version 0.1
 * @author Johnny Huamani <johnny1402@gmail.com>
 * @package Administracion
 */
class MenusController extends AbstractActionController {

    /**
     * atributo que sirve para el acceso al motor de datos
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @var object 
     */
    public $dbAdapter;

    /**
     * atributo que sirve para identificar la pertenencia del controlador
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @var string 
     */
    private $name_module = 'administracion';

    /**
     * Identificador del controlador
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @var int 
     */
    private $id;

    /**
     * usuario en linea
     * @var object 
     */
    private $user;

    /**
     * variables de configuración
     * @var object 
     */
    private $config;

    /**
     * lista de modulos, menus y submenus
     * @var array 
     */
    private $modulos;

    /**
     * lista de modulos
     * @var array 
     */
    private $lista_modulos;

    /**
     * la URL actual
     * @var string 
     */
    private $base;

    /**
     * Método para iniciar valores e verificar si el usuario está en session
     * @author Johnny Huamani <johnny1402@gmail.com>
     */
    public function __construct() {
        $session = new Container('seguridad');
        $this->user = $session->user;
    }

    /**
     * Método para iniciar valores predeterminados
     * @author Johnny Huamani <johnny1402@gmail.com>
     */
    public function iniciar() {
        $session = new Container('seguridad');
        if (!$session->offsetExists('user')) {
            //$this->redirect()->toRoute('seguridad');
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/seguridad');
        }
        //$this->user = $session->user; 

        $this->modulos = $this->getModulo($this->user->id);
        $config = new Config($this->dbAdapter);
        $this->config = $config->getConfig();
        $this->lista_modulos = $this->getPackage();
        $uri = $this->getRequest()->getUri();
        //seteamos el valor del identificador del submenu
        $this->setValueId($uri->getPath());
        $this->base = sprintf('%s://%s', $uri->getScheme(), $uri->getHost());
    }

    /**
     * Método de la vista inicial de la lista de modulos del sistema
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @return \Zend\View\Model\ViewModel
     */
    public function indexAction() {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $this->iniciar();
        $this->layout('layout/administracion');
        //obtenemos  la lista de menús
        $lista_menus = $this->getListMenu();
        //seteamos el submenú
        $session = new Container('seguridad');
        $session->submenu_id = $this->id;
        return new ViewModel(array(
            "objUser" => $this->user,
            "config" => $this->config,
            "package" => $this->name_module,
            "modulo" => $this->modulos,
            "url" => $this->base,
            "id" => $this->id,
            "lista_menu" => $lista_menus
        ));
    }

    /**
     * Método para obtener la lista de menus
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @return array
     */
    private function getListMenu() {
        $objModelMenu = new Menu($this->dbAdapter);
        $arrayValue = $objModelMenu->getMenu();
        $objModelModulo = new Modulo($this->dbAdapter);
        if (count($arrayValue) > 0) {
            foreach ($arrayValue as $index => $arrayMenu) {
                $arrayModulo = $objModelModulo->getModuleById($arrayMenu['int_modulo_id']);
                $arrayMenu['chr_nombre_modulo'] = $arrayModulo['chr_nombre_publico'];
                $arrayValue[$index] = $arrayMenu;
            }
        }
        return $arrayValue;
    }

    public function getModulo($user_id) {
        $objController = new \Administracion\Controller\IndexController();
        $objController->setDbAdapter($this->dbAdapter);
        $modulo = $objController->getMenu($user_id, $this->name_module);
        return $modulo;
    }

    /**
     * setear un valor de identificador de, ID del submenu
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $id
     */
    public function setId($id) {
        $this->id = $id;
        $session = new Container('seguridad');
        $session->submenu_id = $id;        
    }

    /**
     * buscamos el path_uri en la BD y asignamos el id del submenu al controlador
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param string $path_uri
     */
    public function setValueId($path_uri) {
        $objSubmenu = new Submenu($this->dbAdapter);
        $submenu = $objSubmenu->findSubmenuByUrl($path_uri);
        if ($submenu) {
            $this->setId($submenu->id);
        } else {
            $session = new Container('seguridad');
            $this->vd($session);
            $this->setId($session->submenu_id);
        }
    }

    /**
     * Meétodo para eliminar menu, esta función será llamada desde ajax
     * @author Johnny Huamani <johnny1402@gmail.com>
     */
    public function deleteAction() {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        if ($this->getRequest()->isXmlHttpRequest()) {
            $post = $this->getRequest()->getPost();
            $isDeleted = TRUE;
            //verificamos si se puede eliminar y procedemos a eliminar
            $this->deleteMenu($post['menu_id']);
            $data = array('result' => $isDeleted);
            return $this->getResponse()->setContent(Json::encode($data));
        }
    }

    /**
     * Método para eliminar el menu directamente en la Base de datos
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $module_id
     */
    private function deleteMenu($menu_id) {
        $objModelModule = new Menu($this->dbAdapter);
        $objModelModule->deleleMenu($menu_id);
    }

    /**
     * Método para vargar la vista de edición de menus
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $module_id
     */
    public function editarAction() {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $message = "";
        $menu_id = (int) $this->params()->fromRoute('id', 0);
        $objModelMenu = new Menu($this->dbAdapter);
        $arrayMenu = $objModelMenu->getMenuById($menu_id);
        $this->iniciar();
        $form = new FormMenu('formMenu');
        $this->title = 'Editar menú';
        //verificamos si hay un request
        $objRequest = $this->getRequest();
        if ($objRequest->isPost()) {
            $objFormModuleValidate = new FormMenuValidate();
            $form->setValidationGroup(array('chr_nombre', 'int_order', 'csrf', 'int_modulo_id'));
            $form->setInputFilter($objFormModuleValidate->getInputFilter());
            $form->setData($objRequest->getPost());
            if ($form->isValid()) {
                //ahora despues de validar los datos del formulario iniciamos la actualización
                $this->saveMenu($objRequest->getPost());
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/administracion/menus');
                //return $this->forward()->dispatch('Administracion\Controller\Index', array('action'=>'index'));
            } else {
                $message = "Ocurrio algún error";
            }
        }

        $this->layout('layout/administracion');
        return new ViewModel(array(
            "objUser" => $this->user,
            "config" => $this->config,
            "package" => $this->name_module,
            "modulo" => $this->modulos,
            "url" => $this->base,
            "id" => $this->id,
            "form" => $form,
            "title" => $this->title,
            "objMenu" => $arrayMenu,
            "message" => $message,
            "lista_modulos" => $this->lista_modulos
        ));
    }

    /**
     * Método para recibir los datos del formulario y enviarlo almodelo para guardarlo
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param object $objPost
     */
    private function saveMenu($objPost) {
        $objModelModule = new Menu($this->dbAdapter);
        $objModelModule->saveMenu($objPost);
    }

    /**
     * Método para vargar la vista de creación de modulos
     * @author Johnny Huamani <johnny1402@gmail.com>
     */
    public function nuevoAction() {
        return $this->forward()->dispatch('Administracion\Controller\Menus', array('action' => 'editar'));
    }

    /**
     * obtenemos la lista de modulos del sistema
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @return array
     */
    private function getPackage() {
        $objModulo = new Modulo($this->dbAdapter);
        $lista_modulos = $objModulo->getPackage();

        $returnValue = array();
        if (count($lista_modulos) > 0) {
            foreach ($lista_modulos as $index => $modulo) {
                $returnValue[$modulo['id']] = $modulo['chr_nombre_publico'];
            }
        }
        //var_dump($returnValue);
        return $returnValue;
    }

    private function vd($var) {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

}