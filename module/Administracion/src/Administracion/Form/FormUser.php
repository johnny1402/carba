<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Administracion\Form;

use Zend\Captcha\AdapterInterface as CaptchaAdapter;
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Captcha;
use Zend\Form\Factory;

class FormUser extends Form {

    public function __construct($name = null, $options = array()) {
        parent::__construct($name, $options);
        $factory = new Factory();

        $csrf = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
            'options' => array(
            )
        ));

        $chr_usuario = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'chr_usuario',
            'options' => array(
                'label' => 'Usuario',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            )/* ,
                  'attributes' => array(
                  'readonly' => 'readonly',
                  ), */
        ));
        $chr_password = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'chr_password',
            'options' => array(
                'label' => 'Password',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            )/* ,
                  'attributes' => array(
                  'readonly' => 'readonly',
                  ), */
        ));
        
        $bool_active = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'bool_active',
            'options' => array(
                'label' => 'Activo',
                'attributes' => array(
                    'class' => 'control-label'
                )
            ),
            'attributes' => array(
                'placeholder' => 'Activo',
            ),
        ));        
        
        $chr_nombre = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'chr_nombre',
            'options' => array(
                'label' => 'Nombre',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            ),
            'attributes' => array(
                'placeholder' => 'Nombre',
            ),
        ));
        
        $chr_apellido_paterno = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'chr_apellido_paterno',
            'options' => array(
                'label' => 'Apellido paterno',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            ),
            'attributes' => array(
                'placeholder' => 'Apellido paterno',
            ),
        ));        
        $chr_apellido_materno = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'chr_apellido_materno',
            'options' => array(
                'label' => 'Apellido materno',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            ),
            'attributes' => array(
                'placeholder' => 'Apellido materno',
            ),
        ));        
        $date_fecha_nacimiento = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'date_fecha_nacimiento',
            'options' => array(
                'label' => 'Fecha nacimiento',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            ),
            'attributes' => array(
                'placeholder' => 'yyyy/mm/dd',
            ),
        ));        
        $chr_dni = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'chr_dni',
            'options' => array(
                'label' => 'DNI',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            ),
            'attributes' => array(
                'placeholder' => 'Dni',
            ),
        ));
        $chr_telefono = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'chr_telefono',
            'options' => array(
                'label' => 'Telefono',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            ),
            'attributes' => array(
                'placeholder' => 'Telefono',
            ),
        ));
        $chr_domicilio = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'chr_domicilio',
            'options' => array(
                'label' => 'Dirección',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            ),
            'attributes' => array(
                'placeholder' => 'Dirección',
            ),
        ));
        $chr_email = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'chr_email',
            'options' => array(
                'label' => 'Correo',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            ),
            'attributes' => array(
                'placeholder' => 'Correo',
            ),
        ));

        $submit = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Submit',
            'name' => 'btnSubmit',
            'attributes' => array(
                'class' => 'btn btn-large btn-primary',
                'value' => 'Guardar'
            ),
        ));
        $id = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Hidden',
            'name' => 'id'
        ));

        $this->add($chr_nombre);
        $this->add($chr_apellido_paterno);
        $this->add($chr_apellido_materno);
        $this->add($date_fecha_nacimiento);
        $this->add($bool_active);
        $this->add($chr_dni);
        $this->add($chr_telefono);
        $this->add($chr_domicilio);
        $this->add($chr_email);
        $this->add($csrf);
        $this->add($id);
        $this->add($submit);
    }

}