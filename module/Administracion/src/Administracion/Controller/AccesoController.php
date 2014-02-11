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
use Administracion\Model\Entity\Usuario;
use Administracion\Model\Entity\Gruposubmenu;
use Administracion\Model\Entity\Usuariogrupo;
use Zend\Json\Json;
use Administracion\Form\FormUser;
use Administracion\Form\FormUserValidate;


/**
 * Clase para administrar los grupos
 * @version 0.1
 * @author Johnny Huamani <johnny1402@gmail.com>
 * @package Administracion
 */
class AccesoController extends AbstractActionController {

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
        //obtenemos  la lista de usuarios
        $objModelGrupo = new Grupo($this->dbAdapter);
        $listGroup = $objModelGrupo->getGrupo();
        $session = new Container('seguridad');
        $session->submenu_id = $this->id;
        return new ViewModel(array(
            "objUser" => $this->user,
            "config" => $this->config,
            "package" => $this->name_module,
            "modulo" => $this->modulos,
            "url" => $this->base,
            "id" => $this->id,
            "listGroup" => $listGroup
        ));
    }

    /**
     * Método para obtener la lista de usuarios
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @return array
     */
    private function getListUser() {
        $objModelUser = new Usuario($this->dbAdapter);
        $arrayValue = $objModelUser->getUsers();
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
     * Método para eliminar usuario, esta función será llamada desde ajax
     * @author Johnny Huamani <johnny1402@gmail.com>
     */
    public function deleteAction() {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        if ($this->getRequest()->isXmlHttpRequest()) {
            $post = $this->getRequest()->getPost();
            $isDeleted = FALSE;
            if ($this->isRemovable($post['user_id'])) {
                $isDeleted = TRUE;
            }            
            //verificamos si se puede eliminar y procedemos a eliminar
            if($isDeleted){
                $this->deleteUser($post['user_id']);
            }
            $data = array('result' => $isDeleted);
            return $this->getResponse()->setContent(Json::encode($data));
        }
    }

    /**
     * Método para eliminar el usuario directamente en la Base de datos
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $user_id
     */
    private function deleteUser($user_id) {
        $objModelUser = new Usuario($this->dbAdapter);
        $objModelUser->deleteUser($user_id);
    }

    /**
     * Método para vargar la vista de edición de usuarios
     * @author Johnny Huamani <johnny1402@gmail.com>
     */
    public function editarAction() {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $message = "";
        $user_id = (int) $this->params()->fromRoute('id', 0);
        $objModelUser = new Usuario($this->dbAdapter);
        $arrayUser = $objModelUser->getUserById($user_id);
        $this->iniciar();
        
        $form = new FormUser('formUser');
        $this->title = 'Editar usuario';
        //verificamos si hay un request
        $objRequest = $this->getRequest();
        if ($objRequest->isPost()) {
            $objFormModuleValidate = new FormUserValidate();
            if($user_id >0){
                $form->setValidationGroup(array('chr_nombre', 'chr_apellido_paterno','chr_telefono','chr_email','chr_dni', 'csrf'));
            }else{
                $form->setValidationGroup(array('chr_usuario','chr_password','chr_nombre', 'chr_apellido_paterno','chr_telefono','chr_email','chr_dni', 'csrf'));
            }
            $form->setInputFilter($objFormModuleValidate->getInputFilter());
            $form->setData($objRequest->getPost());
            if ($form->isValid()) {
                //ahora despues de validar los datos del formulario iniciamos la actualización
                $this->saveUser($objRequest->getPost());
                return $this->redirect()->toUrl($this->getRequest()->getBaseUrl() . '/administracion/usuario');
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
            "arrayUser" => $arrayUser,
            "message" => $message,
            "lista_modulos" => $this->lista_modulos
        ));
    }

    /**
     * Método para recibir los datos del formulario y enviarlo al modelo para guardarlo
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param object $objPost
     */
    private function saveUser($objPost) {
        $objModelGrupo = new Usuario($this->dbAdapter);
        $objModelGrupo->saveUser($objPost);
    }

    /**
     * Método para vargar la vista de creación de modulos
     * @author Johnny Huamani <johnny1402@gmail.com>
     */
    public function nuevoAction() {
        return $this->forward()->dispatch('Administracion\Controller\Usuario', array('action' => 'editar'));
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
     * Método para verificar si este usuario se puede eliminar
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param type $user_id
     * @return boolean
     */
    private function isRemovable($user_id) {
        $returValue = FALSE;
        $objModelUser = new Usuario($this->dbAdapter);
        $returValue = $objModelUser->isRemovable($user_id);
        return $returValue;
    }
    
    public function searchGroupAjaxAction(){
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
            $post = $this->getRequest()->getPost();
            $objModelGroup = new Grupo($this->dbAdapter);
            $groups = $objModelGroup->findGroup($post['txtSearch']);
            $result = new ViewModel();
            $result->setTerminal(true);
            $result->setVariables(array('listGroup'=>$groups));
            $result->setTemplate('acceso/grupos.phtml');
            return $result;
        }
    }
    /**
     * Método llamado x ajax para actulizar la lista de accesos por grupo
     * @author Johnny Huamani <johnny1402@gmail.com>
     */
    public function actualizarAccesoAction(){
       if ($this->getRequest()->isXmlHttpRequest()) {
            $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
            $post = $this->getRequest()->getPost();
            if($post['data'] == '0'){
                $this->_limpiarAcceso($post['grupo_id']);
            }else{
                $this->_limpiarAcceso($post['grupo_id']);
                $arrayGoupId = explode('-', $post['data']);
                foreach ($arrayGoupId as $index=>$acceso){
                    $data = array("int_id_grupo"=>$post['grupo_id'], "int_submenu_id"=>$acceso);
                    $this->_updateAccessList($data);                    
                }
            }
            $returnValue = array('result' => "1","url"=>$this->getRequest()->getBaseUrl() . '/administracion/acceso');
            return $this->getResponse()->setContent(Json::encode($returnValue));
       } 
    }
    
    private function _updateAccessList($arrayGroup){
        if(is_array($arrayGroup)){
            if(count($arrayGroup)>0){
                $objModelGruposubmenu = new Gruposubmenu($this->dbAdapter);
                $objModelGruposubmenu->updateAccessList($arrayGroup);
            }
        }
    }
    
    private function _limpiarAcceso($grupo_id){
        $objModelGruposubmenu = new Gruposubmenu($this->dbAdapter);
        $objModelGruposubmenu->limpiarAcceso($grupo_id);
    }
    
    /**
     * @author Johnny Huamani <johnny1402@gmail.com>
     * 
     */
    public function grupoAction(){
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $this->iniciar();
        $this->layout('layout/administracion');
        $session = new Container('seguridad');
        $group_id = (int) $this->params()->fromRoute('id', 0);
        $listAccess = $this->_getAccessByIdGroup($group_id);
        $session->submenu_id = $this->id;
        $objModelGrupo = new Grupo($this->dbAdapter);
        $listGroup = $objModelGrupo->getGrupo();
        return new ViewModel(array(
            "objUser" => $this->user,
            "config" => $this->config,
            "package" => $this->name_module,
            "modulo" => $this->modulos,
            "url" => $this->base,
            "id" => $this->id,
            "listGroup" => $listGroup,
            'listAccess'=>$listAccess,
            "grupo_id"=>$group_id
        ));       
    }
    /**
     * 
     * @param int $group_id
     * @return array
     */
    private function _getAccessByIdGroup($group_id){
        //listamos los modulo, menus y submenus totales 
        $objModelGroup = new Modulo($this->dbAdapter);
        $listModule = $objModelGroup->getPackage();
        if(count($listModule)>0){
            foreach ($listModule as $index=>$module){
               $objModelMenu = new Menu($this->dbAdapter);
               $listMenu = $objModelMenu->getMenuByIdModulo($module['id']);
               if(count($listMenu)>0){
                   foreach ($listMenu as $indice=>$menu){
                       $objModelSubmenu = new Submenu($this->dbAdapter);
                       $listSubmenu = $objModelSubmenu->getSubmenuByIdMenu($menu['id']);
                       if(count($listSubmenu)>0){
                           foreach ($listSubmenu as $puntero=>$submenu){
                               $submenu['access']=$this->_access($submenu, $group_id);
                               $listSubmenu[$puntero] = $submenu;
                           }
                       }
                       $menu['submenu'] = $listSubmenu;
                       $listMenu[$indice] = $menu;
                   }
               }
               $module['menu'] = $listMenu;
               $listModule[$index] = $module;
            }
        }
        return $listModule;
    }
    
    private function _access($submenu, $grupo_id){
        $returnValue = 0;
        //obtenemos los Id de los submenus para listar sus accesos
        $objModelGroupSubmenu = new Gruposubmenu($this->dbAdapter);
        $listGroupSubmenu = $objModelGroupSubmenu->getGrupoSubmenuByIdGroup($submenu['id'], $grupo_id);
        if(count($listGroupSubmenu)>0){
            $returnValue=1;
        }
        return $returnValue;
    }
    /**
     * lista de usuarios x grupo
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @return html
     */
    public function usuariosAction(){
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $this->iniciar();
        $this->layout('layout/administracion');
        $grupo_id = (int) $this->params()->fromRoute('id', 0);
        $objModelUsuario = new Usuariogrupo($this->dbAdapter);
        $arrayGrupoUsuario = $objModelUsuario->getUserByIdGrupo($grupo_id);
        $usuarioId = array();
        $arrayUser = array();
        if(count($arrayGrupoUsuario)>0){
            foreach ($arrayGrupoUsuario as $puntero=>$grupoUsuario){
                array_push($usuarioId, $grupoUsuario['int_usuario_id']);
            }
        $objModelUsuario = new Usuario($this->dbAdapter);
        $arrayUser = $objModelUsuario->getUserList($usuarioId);
            
        }
        return new ViewModel(array(
            "objUser" => $this->user,
            "config" => $this->config,
            "package" => $this->name_module,
            "modulo" => $this->modulos,
            "url" => $this->base,
            "id" => $this->id,
            "arrayUser" => $arrayUser,
            //"message" => $message,
            "lista_modulos" => $this->lista_modulos
        ));
    
    }

    private function vd($var) {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

}