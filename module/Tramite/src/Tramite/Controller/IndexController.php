<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Tramite\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container;
use Zend\Session\SessionManager;
use Administracion\Model\Entity\Config;

//use Administracion\Controller\IndexController;

class IndexController extends AbstractActionController {

    public $dbAdapter;

    /**
     * atributo que sirve para identificar la pertenencia del controlador
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @var string 
     */
    private $name_module = 'tramite';

    public function indexAction() {
        //$objSeguridad = new \Seguridad\Controller\IndexController();
        //$objSeguridad->verify_sessionAction();
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        $session = new Container('seguridad');
        if (!$session->offsetExists('user')) {
            $this->redirect()->toRoute('seguridad');
        }
        $config = new Config($this->dbAdapter);
        $this->layout('layout/administracion');
        $modulos = $this->getModulo($session->user->id);
        return new ViewModel(array(
            "objUser" => $session->user,
            "config" => $config->getConfig(),
            "package" => $this->name_module,
            "modulo" => $modulos
        ));
    }

    public function getModulo($user_id) {
        $objController = new \Administracion\Controller\IndexController();
        $objController->setDbAdapter($this->dbAdapter);
        $objController->setNameModule($this->name_module);
        $modulo = $objController->getMenu($user_id, $this->name_module);
        return $modulo;
    }

    private function vd($var) {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

}
