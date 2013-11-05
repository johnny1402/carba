<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Seguridad\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Seguridad\Form\Login;
use Seguridad\Model\Loginvalidate;
use Seguridad\Model\Entity\Usuario;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Session\Container;
use Zend\Session\SessionManager;

class IndexController extends AbstractActionController {

    public $dbAdapter;

    public function indexAction() {
        $this->dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        //$this->test();
        //validamos si existe una session
        $this->validateAction();
        $form = new Login('form');
        $title = 'Iniciar sesión';
        //verificamos si hay un request
        $objRequest = $this->getRequest();
        if ($objRequest->isPost()) {
            $objLoginValidate = new Loginvalidate();
            $form->setValidationGroup('txtUser', 'txtPassword');
            $form->setInputFilter($objLoginValidate->getInputFilter());
            $form->setData($objRequest->getPost());
            if ($form->isValid()) {
                $objUsuario = new Usuario($this->dbAdapter);
                $objUser = $objUsuario->getUsuario($form->getData());
                if ($objUser) {
                    $session = new Container('seguridad');
                    $session->user = $objUser;
                    $this->validateAction();
                } else {
                    return new ViewModel(array("mensaje" => "El usuario y/o password son incorrectos", "title" => $title, "form" => $form, 'url' => $this->getRequest()->getBaseUrl()));
                }
            }
        }
        return new ViewModel(array("title" => $title, "form" => $form, 'url' => $this->getRequest()->getBaseUrl()));
        //return new ViewModel();
    }

    public function validateAction() {
        //$data = $this->getRequest()->getPost();
        $session = new Container('seguridad');
        //verificamos si existe la session del usuario
        if ($session->offsetExists('user')) {
            //redireccionamos al modulo que corresponda
            $this->redirect()->toRoute('administracion');
        }
        //return new ViewModel();
    }
    
    public function verify_sessionAction(){
        $session = new Container('seguridad');
        if (!$session->offsetExists('user')) {
            $this->redirect()->toRoute('seguridad');
        }
    }

    public function logoutAction() {
        $session = new Container('seguridad');
        if ($session->offsetExists('user')) {
            $session->getManager()->getStorage()->clear('seguridad');
        }
        $this->redirect()->toRoute('seguridad');
    }
    
    private function test(){
       $objUsuario = new Usuario($this->dbAdapter);
       $objUser = $objUsuario->test();
       $this->vd($objUser);
    }

    private function vd($var) {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
        /* $session = new Container('foo');

          // these are all equivalent means to the same end
          $session['bar'] = 'foobar';

          $session->bar = 'foobar';

          $session->offsetSet('bar', 'foobar');
          Getters

          $bar = $session['bar'];

          $bar = $session->bar;

          $bar = $session->offsetGet('bar');
          isset()

          $test = isset($session['bar']);

          $test = isset($session->bar);

          $test = $session->offsetExists('bar');
          unset()

          unset($session['bar']);

          unset($session->bar);

          $session->offsetUnset('bar'); */
    }

}
