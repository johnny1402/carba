<?php

namespace Administracion\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Session\Container;

class Usuario extends TableGateway {

    public function __construct(Adapter $adapter = NULL, $databaseSchema = NULL, ResultSet $selectResultPrototype = NULL) {
        parent::__construct('tb_seg_usuario', $adapter, $databaseSchema, $selectResultPrototype);
    }
    
    /**
     * Método para listar todos los usuarios
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @return array
     */
    public function getUsers() {
        $select = $this->getSql()->select()->order('id ASC');
        $resultSet = $this->selectWith($select)->toArray();
        return $resultSet;
    }
    /**
     * Método para verificar si este usuario se puede eliminar
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param type $user_id
     * @return boolean
     */
    public function isRemovable($user_id) {
        $rowset = $this->select(array('id' => $user_id, 'is_deleted' => 1));
        $resultSet = $rowset->current();
        return $resultSet;
    }
    
    /**
     * Método para eliminar el usuario directamente en la Base de datos
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param type $user_id
     * @return boolean
     */
    public function deleteUser($user_id) {
        $where = new \Zend\Db\Sql\Where();
        $where->equalTo('id', $user_id);
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
     * Método para registrar y/o actualizar un menu
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param array $form
     */
    public function saveGrupo($form) {
        $session = new Container('seguridad');
        if (isset($form['bool_active'])) {
            $form['bool_active'] = 1;
        } else {
            $form['bool_active'] = 0;
        }
        if ($form['id'] == 0) {
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
            if ($this->getGrupoById($id)) {
                $this->update($data, array('id' => $id));
            } else {
                throw new \Exception('El Grupo no existe');
            }
        }
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