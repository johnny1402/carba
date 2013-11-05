<?php

namespace Seguridad\Model;

// add these import statements
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Loginvalidate implements InputFilterAwareInterface {

    public $user;
    public $password;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->user = (isset($data['txtUser'])) ? $data['txtUser'] : null;
        $this->password = (isset($data['txtPassword'])) ? $data['txtPassword'] : null;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                        'name' => 'txtUser',
                        'required' => TRUE,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'StringTrim')
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => 1,
                                    'max' => 20,
                                    'messages' => array(
                                        \Zend\Validator\StringLength::TOO_SHORT => 'usuario incorrecto',
                                        \Zend\Validator\StringLength::TOO_LONG => 'Usuario incorrecto'
                                    )
                                )
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Ingrese un usuario'
                                    )
                                )
                            ),
                            array(
                                'name' => 'Regex',
                                'options' => array(
                                    //'pattern' => '/^[a-z0-9 &-_\.,@]{3,25}$/i',
                                    'pattern' => '/^[a-zA-Z_][a-zA-Z_0-9]*$/',
                                    'messages' => array(
                                        \Zend\Validator\Regex::INVALID => 'Usuario no valido',
                                        \Zend\Validator\Regex::NOT_MATCH => 'Usuario no valido'
                                    )
                                )
                            )
                        )
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'txtPassword',
                        'required' => TRUE,
                        'filters' => array(
                            array('name' => 'StripTags'),
                            array('name' => 'stringTrim')
                        ),
                        'validators' => array(
                            array(
                                'name' => 'StringLength',
                                'options' => array(
                                    'encoding' => 'UTF-8',
                                    'min' => 1,
                                    'max' => 8,
                                    'messages' => array(
                                        \Zend\Validator\StringLength::TOO_SHORT => 'Contraseña incorrecta',
                                        \Zend\Validator\StringLength::TOO_LONG => 'Contraseña incorrecta'
                                    )
                                )
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Ingrese su contraseña'
                                    )
                                )
                            ),
                            array(
                                'name' => 'Regex',
                                'options' => array(
                                    //'pattern' => '/^[a-z0-9 &-_\.,@]{3,25}$/i',
                                    'pattern' => '/^[a-zA-Z_][a-zA-Z_0-9]*$/',
                                    'messages' => array(
                                        \Zend\Validator\Regex::INVALID => 'Usuario no valido',
                                        \Zend\Validator\Regex::NOT_MATCH => 'Contraseña incorrecta'
                                    )
                                )
                            )
                        )
            )));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

}