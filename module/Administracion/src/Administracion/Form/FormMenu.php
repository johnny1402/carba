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

class FormMenu extends Form {

    public function __construct($name = null, $options = array()) {
        parent::__construct($name, $options);
        $factory = new Factory();

        $csrf = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf',
            'options' => array(
            )
        ));

        $int_order = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'int_order',
            'options' => array(
                'label' => 'Orden',
                'label_attributes' => array(
                    'class' => 'control-label'
                )
            ),
            'attributes' => array(
                'placeholder' => 'Orden',
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

        $selectModules = $factory->createElement(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'int_modulo_id',
            'attributes' => array(
                'id' => 'int_modulo_id',
                'options' => array(
                    '1' => 'Hi, Im a test!',
                    '3' => 'Bar',
                ),
            ),
            'options' => array(
                'label' => 'Módulos',
            ),
        ));

        $this->add($chr_nombre);
        $this->add($int_order);
        $this->add($bool_active);
        $this->add($csrf);
        $this->add($id);
        $this->add($selectModules);
        $this->add($submit);
    }

}