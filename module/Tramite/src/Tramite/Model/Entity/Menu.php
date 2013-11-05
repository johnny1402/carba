<?php

namespace Administracion\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;

class Menu extends TableGateway{
    
    public function __construct(Adapter $adapter = NULL, $databaseSchema= NULL, ResultSet $selectResultPrototype=NULL) {
        parent::__construct('tb_seg_menu', $adapter, $databaseSchema, $selectResultPrototype);
    }
    
    public function getMenuByIdSubmenu($array_submenu_id){
        $where = new \Zend\Db\Sql\Where();
        $where->in('id', $array_submenu_id);
        $rowset =$this->select($where);
        //$rowset = $this->order('int_order ASC');
        $row = $rowset->toArray();
        return $row;
    }
    
}