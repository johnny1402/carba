<?php

namespace Administracion\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;

class Config extends TableGateway{
    
    public function __construct(Adapter $adapter = NULL, $databaseSchema= NULL, ResultSet $selectResultPrototype=NULL) {
        parent::__construct('tb_seg_config', $adapter, $databaseSchema, $selectResultPrototype);
    }
    
    public function getConfig(){
        $rowset = $this->select();
        $row = $rowset->toArray();
        $arrayConfig= array();
        if(is_array($row)){
            if(count($row)>0){
                foreach ($row as $index=>$array){
                    $arrayConfig[$array['chr_variable']]=$array['chr_value'];
                }
            }
        }
        return $arrayConfig;
    }
    
}