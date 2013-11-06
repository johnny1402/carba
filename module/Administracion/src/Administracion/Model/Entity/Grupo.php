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
     * Método para verificar si este grupo se puede eliminar
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param type $grupo_id
     * @return boolean
     */
    public function isRemovable($grupo_id) {
        $rowset = $this->select(array('id' => $grupo_id, 'is_deleted' => 1));
        $resultSet = $rowset->current();
        return $resultSet;
    }
    
    /**
     * Método para eliminar el grupo directamente en la Base de datos
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param type $grupo_id
     * @return boolean
     */
    public function deleteGrupo($grupo_id) {
        $where = new \Zend\Db\Sql\Where();
        $where->equalTo('id', $grupo_id);
        $this->delete($where);
    }
    
    /**
     * Método para obtener el grupo por su ID
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @return array
     */
    public function getGrupoById($grupo_id) {
        if ($grupo_id == 0) {
            $objGrupo = new \stdClass();
            $objGrupo->id=0;
            $objGrupo->chr_nombre='';
            $objGrupo->chr_nombre_publico='';
            $objGrupo->bool_active='1';
            $objGrupo->int_order='';
            $objGrupo->fecha_creacion=date("Y-m-d Y:i:s");
            //$objGrupo->id_user_creacion
            //$objGrupo->is_deleted            
            $row = $objGrupo;
        } else {
            $where = new \Zend\Db\Sql\Where();
            $where->equalTo('id', $grupo_id);
            $rowset = $this->select($where);
            $row = $rowset->current();
        }
        return $row;
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