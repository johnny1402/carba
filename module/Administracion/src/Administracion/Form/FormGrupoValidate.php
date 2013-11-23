<?php

namespace Administracion\Form;

// add these import statements
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use Zend\Form\Element\Csrf as CsrfElement;
use Zend\Validator\Csrf as CsrfValidator;

class FormGrupoValidate implements InputFilterAwareInterface {

    public $chr_nombre;
    public $chr_nombre_publico;
    public $int_order;
    public $csrf;
    protected $inputFilter;

    public function exchangeArray($data) {
        $this->chr_nombre = (isset($data['chr_nombre'])) ? $data['chr_nombre'] : null;
        $this->chr_nombre_publico = (isset($data['chr_nombre_publico'])) ? $data['chr_nombre_publico'] : null;
        $this->int_order = (isset($data['int_order'])) ? $data['int_order'] : null;
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
                        'name' => 'chr_nombre_publico',
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
                                        \Zend\Validator\StringLength::TOO_SHORT => 'Nombre del menú no permitido',
                                        \Zend\Validator\StringLength::TOO_LONG => 'Tamaño del nombre del menú exagerado'
                                    )
                                )
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Nombre del menú no puede ser vacio'
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
                        'name' => 'int_order',
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
                                        \Zend\Validator\StringLength::TOO_SHORT => 'Orden incorrecta',
                                        \Zend\Validator\StringLength::TOO_LONG => 'Orden incorrecta'
                                    )
                                )
                            ),
                            array(
                                'name' => 'NotEmpty',
                                'options' => array(
                                    'messages' => array(
                                        \Zend\Validator\NotEmpty::IS_EMPTY => 'Ingrese el orden'
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
                                        \Zend\Validator\Regex::INVALID => 'Orden no válido',
                                        \Zend\Validator\Regex::NOT_MATCH => 'Orden no válido'
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