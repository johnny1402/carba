<?php

namespace Administracion\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;

class Grupo extends TableGateway {

    public function __construct(Adapter $adapter = NULL, $databaseSchema = NULL, ResultSet $selectResultPrototype = NULL) {
        parent::__construct('tb_seg_grupo', $adapter, $databaseSchema, $selectResultPrototype);
    }
    
    /**
     * Método para listar todos los grupos
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @return array
     */
    public function getGrupo() {
        $select = $this->getSql()->select()->order('int_order ASC');
        $resultSet = $this->selectWith($select)->toArray();
        return $resultSet;
    }    

    /**
     * Método para imprimir variables
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param string $var
     */
    private function vd($var) {
        echo "<pre>";
        print_r($var);
        echo "</pre>";
    }

}