<?php

namespace Administracion\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class Gruposubmenu extends TableGateway {

    public function __construct(Adapter $adapter = NULL, $databaseSchema = NULL, ResultSet $selectResultPrototype = NULL) {
        parent::__construct('tb_seg_grupo_submenu', $adapter, $databaseSchema, $selectResultPrototype);
    }

    public function getGrupoSubmenu($arrayGrupo) {
        $returnValue = array();
        $where = new \Zend\Db\Sql\Where();
        $where->in('int_id_grupo', $arrayGrupo);
        $rowset = $this->select($where);
        $returnValue = $rowset->toArray();
        return $returnValue;
    }

    /**
     * Método para listar los submenus que pertenecen al grupo
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param array $array_id_grupo
     * @param array $array_id_submenu
     */
    public function getGrupoSubmenuByIdGrupoIdSubmenu($array_id_grupo, $array_id_submenu) {
        $returnValue = array();
        $where = new \Zend\Db\Sql\Where();
        $where->in('int_id_grupo', $array_id_grupo);
        $where->in('int_submenu_id', $array_id_submenu);
        $rowset = $this->select($where);
        $returnValue = $rowset->toArray();
        return $returnValue;        
    }
    
    public function getSubmenuByUserIdByGrupos($submenu_id, $array_id_grupos){
        $where = new \Zend\Db\Sql\Where();
        $where->in('int_id_grupo', $array_id_grupos);
        $where->in('int_submenu_id', array($submenu_id));
        $rowset = $this->select($where);
        return $rowset->toArray();
    }
    
    
    public function getGrupoSubmenuByIdGroup($submenu_id, $grupo_id){
        $where = new \Zend\Db\Sql\Where();
        $where->equalTo('int_id_grupo', $grupo_id);
        $where->equalTo('int_submenu_id', $submenu_id);
        $rowset = $this->select($where);
        $row = $rowset->toArray(); 
        return $row;
    }    

}