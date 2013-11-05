<?php

namespace Seguridad\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;

class Usuario extends TableGateway{
    
    public function __construct(Adapter $adapter = NULL, $databaseSchema= NULL, ResultSet $selectResultPrototype=NULL) {
        parent::__construct('tb_seg_usuario', $adapter, $databaseSchema, $selectResultPrototype);
    }
    
    public function getUsuario($arrayForm){
        //$row = $this->where->like('chr_usuario', 'adm%');
        //$rowset = $this->select(array('id'=>'1'));
        $where = new \Zend\Db\Sql\Where();
        $where->like('chr_usuario', $arrayForm['txtUser'])
                ->like('chr_password', md5($arrayForm['txtPassword']));
        $rowset = $this->select($where);
        $row = $rowset->current();
        /*if(!$row){
                throw new \Exception("No se encontró usuarios con el identificador ".$arrayForm['txtUser']);

        }   */     
        return $row;
    }
    
    public function test(){
    $select = $this->getSql()->select()->order('id desc');
    $resultSet = $this->selectWith($select)->toArray();
    return $resultSet;
    }
    
}