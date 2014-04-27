<?php

namespace Administracion\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Session\Container;

class Modulo extends TableGateway {

    public function __construct(Adapter $adapter = NULL, $databaseSchema = NULL, ResultSet $selectResultPrototype = NULL) {
        if ($adapter == NULL) {
            $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter');
        }
        parent::__construct('tb_seg_modulo', $adapter, $databaseSchema, $selectResultPrototype);
    }

    /**
     * Método para obtener la lista de módulos ordenados por su campo int_order
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param array $array_id
     * @return array
     */
    public function getModulo($array_id) {
        $where = new \Zend\Db\Sql\Where();
        $where->in('id', $array_id);
        $where->in('bool_active', array(1));
        $select = $this->getSql()->select()->where($where)->order('int_order ASC');
        //echo $select;
        $resultSet = $this->selectWith($select)->toArray();
        return $resultSet;

        /* $where = new \Zend\Db\Sql\Where();
          $where->in('id', $array_id);
          $rowset =$this->select($where);
          //$rowset = $this->order('int_order ASC');
          $row = $rowset->toArray();
          return $row; */
    }

    /**
     * Método para verificar si este módulo se puede eliminar
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param type $modulo_id
     * @return boolean
     */
    public function isRemovable($modulo_id) {
        $rowset = $this->select(array('id' => $modulo_id, 'is_deleted' => 1));
        $resultSet = $rowset->current();
        return $resultSet;
    }
    
    public function existModuleByName($chrName, $idModule){
        $returnValue = TRUE;
        $rowset = $this->select(array('chr_nombre_publico' => $chrName));
        $resultSet = $rowset->current();
        if(is_bool($resultSet)){
            $returnValue = FALSE;
        }
        return $returnValue;
    }

    /**
     * Obtener el modulo x el ID
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param type $module_id
     * @return array
     */
    public function getModuleById($module_id) {
        if ($module_id == 0) {
            $objModule = new \stdClass();
            $objModule->id = 0;
            $objModule->chr_nombre = '';
            $objModule->bool_active = '1';
            $objModule->int_order = '';
            $objModule->chr_nombre_publico = '';
            $resultSet = $objModule;
        } else {
            $rowset = $this->select(array('id' => $module_id));
            $resultSet = $rowset->current();
        }
        return $resultSet;
    }

    /**
     * Método para eliminar el modulo directamente en la Base de datos
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param type $modulo_id
     * @return boolean
     */
    public function deleleModule($module_id) {
        /* $rowset = $this->select(array('id' => $module_id));
          $row = $rowset->current();
          $row->delete(); */
        $where = new \Zend\Db\Sql\Where();
        $where->equalTo('id', $module_id);
        $this->delete($where);
    }

    /**
     * Método para registrar y/o actualizar un modulo
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param array $form
     */
    public function saveModule($form) {
        $session = new Container('seguridad');
        if (isset($form['bool_active'])) {
            $form['bool_active'] = 1;
        } else {
            $form['bool_active'] = 0;
        }
        if ($form['id'] == 0) {
            $form['chr_nombre'] = $form['chr_nombre_publico'];
            $form['id_user_creacion'] = $session->user->id;
            $form['fecha_creacion'] = date("Y-m-d H:i:s");
            $data = array(
                'chr_nombre' => $form['chr_nombre'],
                'chr_nombre_publico' => $form['chr_nombre_publico'],
                'bool_active' => $form['bool_active'],
                'int_order' => $form['int_order'],
                'fecha_creacion' => date("Y-m-d H:i:s"),
                'id_user_creacion' => $form['id_user_creacion']
            );
        } else {
            $data = array(
                'bool_active' => $form['bool_active'],
                'int_order' => $form['int_order'],
                'chr_nombre_publico' => $form['chr_nombre_publico'],
                'id_user_actualizacion' => $session->user->id,
                'fecha_actualizacion' => date("Y-m-d H:i:s")
            );
        }

        $id = (int) $form['id'];
        if ($id == 0) {
            $this->insert($data);
        } else {
            if ($this->getModuleById($id)) {
                $this->update($data, array('id' => $id));
            } else {
                throw new \Exception('El modulo no existe');
            }
        }
    }

    public function getPackage() {
        $rowset = $this->select(array());
        $row = $rowset->toArray();
        return $row;
    }

}