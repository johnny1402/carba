<?php

namespace Administracion\Form;

// add these import statements
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Form\Element\Csrf as CsrfElement;
use Zend\Validator\Csrf as CsrfValidator;

class FormUserValidate implements InputFilterAwareInterface {

    public $chr_nombre;
    public $chr_apellido_paterno;
    public $chr_apellido_materno;
    public $chr_email;
    public $chr_telefono;
    public $chr_dni;
    public $csrf;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->chr_nombre = (isset($data['chr_nombre'])) ? $data['chr_nombre'] : null;
        $this->chr_apellido_paterno = (isset($data['chr_apellido_paterno'])) ? $data['chr_apellido_paterno'] : null;
        $this->chr_apellido_materno = (isset($data['chr_apellido_materno'])) ? $data['chr_apellido_materno'] : null;
        $this->chr_dni = (isset($data['chr_dni'])) ? $data['chr_dni'] : null;
        $this->chr_email = (isset($data['chr_email'])) ? $data['chr_email'] : null;
        $this->chr_telefono = (isset($data['chr_telefono'])) ? $data['chr_telefono'] : null;
        $this->csrf = (isset($data['csrf'])) ? $data['csrf'] : null;
    }

    public function setInputFilter(InputFilterInterface $inputFilter) {
        throw new Exception("Not used");
    }

    public function getInputFilter() {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                        'name' => 'chr_nombre',
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
                                    'max' => 60,
                                    'messages' => array(
                                        \Zend\Validator\StringLength::TOO_SHORT => 'Nombre del usuario no permitido',
                                        \Zend\Validator\StringLength::TOO_LONG => 'Tamaño del nombre del usuario exagerado'
                                    )
                                )
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Nombre del usuario no puede ser vacio'
                                    )
                                )
                            )/*,
                            array(
                                'name' => 'Regex',
                                'options' => array(
                                    //'pattern' => '/^[a-zA-Z_][a-zA-Z_0-9]*$/',
                                    //'pattern' => '/^[A-Za-záéíóúñ]{2,}([A-Za-záéíóúñ]{2,})+$/',
                                    'pattern' => '/^[a-z0-9_-]{3,25}$/',
                                    'messages' => array(
                                        \Zend\Validator\Regex::INVALID => 'Nombre del menú no valido1',
                                        \Zend\Validator\Regex::NOT_MATCH => 'Nombre del submenú no valido'
                                    )
                                )
                            )*/
                        )
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'chr_apellido_paterno',
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
                                    'max' => 60,
                                    'messages' => array(
                                        \Zend\Validator\StringLength::TOO_SHORT => 'apellido no permitido',
                                        \Zend\Validator\StringLength::TOO_LONG => 'Tamaño del apellido del usuario exagerado'
                                    )
                                )
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Apellido del usuario no puede ser vacio'
                                    )
                                )
                            )/*,
                            array(
                                'name' => 'Regex',
                                'options' => array(
                                    //'pattern' => '/^[a-zA-Z_][a-zA-Z_0-9]*$/',
                                    //'pattern' => '/^[A-Za-záéíóúñ]{2,}([A-Za-záéíóúñ]{2,})+$/',
                                    'pattern' => '/^[a-z0-9_-]{3,25}$/',
                                    'messages' => array(
                                        \Zend\Validator\Regex::INVALID => 'Nombre del menú no valido1',
                                        \Zend\Validator\Regex::NOT_MATCH => 'Nombre del submenú no valido'
                                    )
                                )
                            )*/
                        )
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'chr_telefono',
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
                                    'min' => 6,
                                    'max' => 20,
                                    'messages' => array(
                                        \Zend\Validator\StringLength::TOO_SHORT => 'Telefono incorrecta',
                                        \Zend\Validator\StringLength::TOO_LONG => 'Telefono incorrecta'
                                    )
                                )
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Ingrese su telefono'
                                    )
                                )
                            ),
                            array(
                                'name' => 'Regex',
                                'options' => array(
                                    //'pattern' => '/^[a-z0-9 &-_\.,@]{3,25}$/i',
                                    //'pattern' => '/^[a-zA-Z_][a-zA-Z_0-9]*$/',
                                    'pattern' => '/^[0-9]*$/',
                                    'messages' => array(
                                        \Zend\Validator\Regex::INVALID => 'Telefono no válido',
                                        \Zend\Validator\Regex::NOT_MATCH => 'Telefono no válido'
                                    )
                                )
                            )
                        )
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'chr_dni',
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
                                    'min' => 8,
                                    'max' => 8,
                                    'messages' => array(
                                        \Zend\Validator\StringLength::TOO_SHORT => 'DNI incorrecta',
                                        \Zend\Validator\StringLength::TOO_LONG => 'DNI incorrecta'
                                    )
                                )
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Ingrese su DNI'
                                    )
                                )
                            ),
                            array(
                                'name' => 'Regex',
                                'options' => array(
                                    //'pattern' => '/^[a-z0-9 &-_\.,@]{3,25}$/i',
                                    //'pattern' => '/^[a-zA-Z_][a-zA-Z_0-9]*$/',
                                    'pattern' => '/^[0-9]*$/',
                                    'messages' => array(
                                        \Zend\Validator\Regex::INVALID => 'DNI no válido',
                                        \Zend\Validator\Regex::NOT_MATCH => 'DNI no válido'
                                    )
                                )
                            )
                        )
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'chr_email',
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
                                    'min' => 6,
                                    'max' => 100,
                                    'messages' => array(
                                        \Zend\Validator\StringLength::TOO_SHORT => 'Correo incorrecta',
                                        \Zend\Validator\StringLength::TOO_LONG => 'Correo incorrecta'
                                    )
                                )
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Ingrese su correo'
                                    )
                                )
                            ),
                            array(
                                'name' => 'Regex',
                                'options' => array(
                                    //'pattern' => '/^[a-z0-9 &-_\.,@]{3,25}$/i',
                                    //'pattern' => '/^[a-zA-Z_][a-zA-Z_0-9]*$/',
                                    'pattern' => '/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/',
                                    'messages' => array(
                                        \Zend\Validator\Regex::INVALID => 'Correo no válido',
                                        \Zend\Validator\Regex::NOT_MATCH => 'Correo no válido'
                                    )
                                )
                            )
                        )
            )));
            $inputFilter->add($factory->createInput(array(
                        'name' => 'csrf',
                        'required' => TRUE,
                        'validators' => array(new \Zend\Validator\Csrf())
            )));

            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }

}