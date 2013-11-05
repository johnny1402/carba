<?php

namespace Administracion\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class Package extends TableGateway{
    
    public function __construct(Adapter $adapter = NULL, $databaseSchema= NULL, ResultSet $selectResultPrototype=NULL) {
        if($adapter == NULL){
            $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        }
        parent::__construct('tb_seg_modulo', $adapter, $databaseSchema, $selectResultPrototype);
    }
    
    public function getModulo($array_id){
        $where = new \Zend\Db\Sql\Where();
        $where->in('id', $array_id);
        $rowset =$this->select($where);
        //$rowset = $this->order('int_order ASC');
        $row = $rowset->toArray();
        return $row;
    }
}