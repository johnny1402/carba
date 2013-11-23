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
use Administracion\Model\Entity\Grupo;
use Zend\Json\Json;
use Administracion\Form\FormGrupo;
use Administracion\Form\FormGrupoValidate;


/**
 * Clase para administrar los grupos
 * @version 0.1
 * @author Johnny Huamani <johnny1402@gmail.com>
 * @package Administracion
 */
class GrupoController extends AbstractActionController {

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
        $objModelGrupo = new Grupo($this->dbAdapter);
        $arrayValue = $objModelGrupo->getGrupo();
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
            $isDeleted = FALSE;
            if ($this->isRemovable($post['grupo_id'])) {
                $isDeleted = TRUE;
            }            
            //verificamos si se puede eliminar y procedemos a eliminar
            if($isDeleted){
                $this->deleteGrupo($post['grupo_id']);
            }
            $data = array('result' => $isDeleted);
            return $this->getResponse()->setContent(Json::encode($data));
        }
    }

    /**
     * Método para eliminar el grupo directamente en la Base de datos
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $grupo_id
     */
    private function deleteGrupo($grupo_id) {
        $objModelGrupo = new Grupo($this->dbAdapter);
        $objModelGrupo->deleteGrupo($grupo_id);
    }

    /**
     * Método para vargar la vista de edición de menus
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $module_id
     */
    public function editarAction() {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $message = "";
        $grupo_id = (int) $this->params()->fromRoute('id', 0);
        $objModelGrupo = new Grupo($this->dbAdapter);
        $arrayGrupo = $objModelGrupo->getGrupoById($grupo_id);
        
        $this->iniciar();
        
        $form = new FormGrupo('formGrupo');
        $this->title = 'Editar grupo';
        //verificamos si hay un request
        $objRequest = $this->getRequest();
        if ($objRequest->isPost()) {
            $objFormModuleValidate = new FormGrupoValidate();
            $form->setValidationGroup(array('chr_nombre_publico', 'int_order', 'csrf'));
            $form->setInputFilter($objFormModuleValidate->getInputFilter());
            $form->setData($objRequest->getPost());
            if ($form->isValid()) {
                //ahora despues de validar los datos del formulario iniciamos la actualización
                $this->saveGrupo($objRequest->getPost());
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/administracion/grupo');
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
            "objGrupo" => $arrayGrupo,
            "message" => $message,
            "lista_modulos" => $this->lista_modulos
        ));
    }

    /**
     * Método para recibir los datos del formulario y enviarlo almodelo para guardarlo
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param object $objPost
     */
    private function saveGrupo($objPost) {
        $objModelGrupo = new Grupo($this->dbAdapter);
        $objModelGrupo->saveGrupo($objPost);
    }

    /**
     * Método para vargar la vista de creación de modulos
     * @author Johnny Huamani <johnny1402@gmail.com>
     */
    public function nuevoAction() {
        return $this->forward()->dispatch('Administracion\Controller\Grupo', array('action' => 'editar'));
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
     * Método para verificar si este grupo se puede eliminar
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param type $module_id
     * @return boolean
     */
    private function isRemovable($grupo_id) {
        $returValue = FALSE;
        $objModelGrupo = new Grupo($this->dbAdapter);
        $returValue = $objModelGrupo->isRemovable($grupo_id);
        return $returValue;
    }    

    private function vd($var) {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

}