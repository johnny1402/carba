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
use Administracion\Model\Entity\Menu;
use Administracion\Model\Entity\Submenu;
use Administracion\Model\Entity\Modulo;
use Zend\Json\Json;
use Administracion\Form\FormSubmenu;
use Administracion\Form\FormSubmenuValidate;

/**
 * Clase para trabajar las varibles de configuración del sistema
 * @version 0.1
 * @author Johnny Huamani <johnny1402@gmail.com>
 * @package Administracion
 */
class SubmenusController extends AbstractActionController {

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
     * la URL actual
     * @var string 
     */
    private $base;

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
            die();
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

    public function indexAction() {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $this->iniciar();
        $objModelSubmenu = new Submenu($this->dbAdapter);
        $lista_submenu = $objModelSubmenu->getSubmenu();
        $this->layout('layout/administracion');
        return new ViewModel(array(
            "objUser" => $this->user,
            "config" => $this->config,
            "package" => $this->name_module,
            "modulo" => $this->modulos,
            "url" => $this->base,
            "id" => $this->id,
            "lista_submenu" => $lista_submenu
        ));
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
            $this->setId($session->submenu_id);
        }
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
            $this->deleteSubmenu($post['submenu_id']);
            $data = array('result' => $isDeleted);
            return $this->getResponse()->setContent(Json::encode($data));
        }
    }

    /**
     * Método para eliminar el submenu directamente en la Base de datos
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $submenu_id
     */
    private function deleteSubmenu($submenu_id) {
        $objModelSubmenu = new Submenu($this->dbAdapter);
        $objModelSubmenu->deleteSubmenu($submenu_id);
    }

    /**
     * Método para vargar la vista de edición de menus
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $module_id
     */
    public function editarAction() {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $this->iniciar();
        $message = "";
        $submenu_id = (int) $this->params()->fromRoute('id', 0);
        $objModelSubmenu = new Submenu($this->dbAdapter);
        $objModelMenu = new Menu($this->dbAdapter);
        if ($submenu_id > 0) {
            $arraySubmenu = $objModelSubmenu->getSubmenuById($submenu_id);
            $arrayMenu = $objModelMenu->getMenuById($arraySubmenu['int_menu_id']);

            //asignar el id del modulo al array submenu
            $arraySubmenu['int_modulo_id'] = $arrayMenu['int_modulo_id'];

            //listamos los menus del modulo
            $lista_menus = $objModelMenu->getMenuByIdModulo($arrayMenu['int_modulo_id']);
            $lista_menus = $this->formatoListaDropdown($lista_menus);
        } else {
            $arraySubmenu = $objModelSubmenu->getSubmenuById($submenu_id);
            foreach ($this->lista_modulos as $idModulo => $nameModulo) {
                $lista_menus = $objModelMenu->getMenuByIdModulo($idModulo);
                $lista_menus = $this->formatoListaDropdown($lista_menus);
            }
        }


        $form = new FormSubmenu('formSubmenu');
        $this->title = 'Editar submenú';
        //verificamos si hay un request
        $objRequest = $this->getRequest();
        if ($objRequest->isPost()) {
            $objFormModuleValidate = new FormSubmenuValidate();
            $form->setValidationGroup(array('chr_nombre', 'int_order', 'csrf', 'int_modulo_id', 'int_menu_id'));
            $form->setInputFilter($objFormModuleValidate->getInputFilter());
            $form->setData($objRequest->getPost());
            if ($form->isValid()) {
                //ahora despues de validar los datos del formulario iniciamos la actualización
                $this->saveSubmenu($objRequest->getPost());
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/administracion/submenus');
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
            "objSubmenu" => $arraySubmenu,
            "message" => $message,
            "lista_modulos" => $this->lista_modulos,
            "lista_menus" => $lista_menus
        ));
    }

    /**
     * Método para vargar la vista de creación de modulos
     * @author Johnny Huamani <johnny1402@gmail.com>
     */
    public function nuevoAction() {
        $session = new Container('seguridad');
        if (!$session->offsetExists('user')) {
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/seguridad');
        }        
        return $this->forward()->dispatch('Administracion\Controller\Submenus', array('action' => 'editar'));
    }

    /**
     * Método para recibir los datos del formulario y enviarlo almodelo para guardarlo
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param object $objPost
     */
    private function saveSubmenu($objPost) {
        $objModelModule = new Submenu($this->dbAdapter);
        $objModelModule->saveSubmenu($objPost);
    }

    /**
     * Método para formatear la lista a modo compatible para un dropdown
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param array $lista_menus
     * @return array
     */
    private function formatoListaDropdown($lista_menus) {
        $returnValue = array();
        if (count($lista_menus) > 0) {
            foreach ($lista_menus as $index => $array) {
                $returnValue[$array['id']] = $array['chr_nombre'];
            }
        }
        return $returnValue;
    }

    /**
     * Meotodo para listar los menus x módulo
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @return json
     */
    public function listamenuAction() {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        if ($this->getRequest()->isXmlHttpRequest()) {
            $objModelMenu = new Menu($this->dbAdapter);
            $post = $this->getRequest()->getPost();
            //listamos los menus del modulo
            $lista_menus = $objModelMenu->listarMenuByIdModulo($post['id_modulo']);
            $lista_menus = $this->formatoListaDropdown($lista_menus);
            $data = array(
                'result' => true,
                'data' => $lista_menus
            );
            return $this->getResponse()->setContent(Json::encode($data));
        }
    }

    private function vd($var) {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

}