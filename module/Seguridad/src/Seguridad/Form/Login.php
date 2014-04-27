<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Seguridad\Form;

use Zend\Captcha\AdapterInterface as CaptchaAdapter;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Captcha;
use Zend\Form\Factory;

class Login extends Form {

    public function __construct($name = null, $options = array()) {
        parent::__construct($name, $options);
        $factory = new Factory();
        $email = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Email',
            'name' => 'txtEmail',
            'options' => array(
                'label' => 'Correo',
            ),
            'attributes' => array(
                'class' => 'input-block-level',
                'placeholder' => 'Contraseña',
            ),
        ));

        $user = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'txtUser',
            'options' => array(
                'label' => 'Usuario',
            ),
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Usuario',
            ),
        ));
        $password = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Password',
            'name' => 'txtPassword',
            'options' => array(
                'label' => 'Contraseña',
            ),
            'attributes' => array(
                'class' => 'form-control',
                'placeholder' => 'Contraseña',
            ),
        ));
        $submit = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Submit',
            'name' => 'btnSubmit',
            'attributes' => array(
                'class' => 'btn btn-lg btn-primary btn-block',
                'placeholder' => 'Contraseña',
                'value' => 'Enviar'
            ),
        ));

        $this->add($password);
        $this->add($email);
        $this->add($user);
        $this->add($submit);

        /*$this->add(array(
            'name' => 'send',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Enviar',
                'class' => 'btn btn-large btn-primary'
            ),
        ));*/


        /*$name = new Element('name');
        $name->setLabel('Tu nombre');
        $name->setAttributes(array(
            'type' => 'text'
        ));*/
    }

}