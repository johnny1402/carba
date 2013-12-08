<?php

namespace Administracion\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class Gruposubmenu extends TableGateway{
    
    public function __construct(Adapter $adapter = NULL, $databaseSchema= NULL, ResultSet $selectResultPrototype=NULL) {
        parent::__construct('tb_seg_grupo_submenu', $adapter, $databaseSchema, $selectResultPrototype);
    }
    
    public function getGrupoSubmenu($arrayGrupo){
        $returnValue = array();
        $where = new \Zend\Db\Sql\Where();
        $where->in('int_id_grupo', $arrayGrupo);
        $rowset = $this->select($where);
        $returnValue = $rowset->toArray();
        return $returnValue;
    }

    
}