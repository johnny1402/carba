<?php

namespace Administracion\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class Usuariogrupo extends TableGateway {

    public function __construct(Adapter $adapter = NULL, $databaseSchema = NULL, ResultSet $selectResultPrototype = NULL) {
        parent::__construct('tb_seg_usuario_grupo', $adapter, $databaseSchema, $selectResultPrototype);
    }

    /**
     * listamos los grupos al que pertenece el usuario
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $user_id
     * @return array
     */
    public function getUsuariogrupo($user_id) {
        $returnValue = array();
        $rowset = $this->select(array('int_usuario_id' => $user_id));
        $returnValue = $rowset->toArray();
        return $returnValue;
    }
    
    public function getUserByIdGrupo($grupo_id){
        $returnValue = array();
        $rowset = $this->select(array('int_grupo_id' => $grupo_id));
        $returnValue = $rowset->toArray();
        return $returnValue;       
    }

}