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
     * Método para obtener el usuario por su ID
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @return array
     */
    public function getUserById($user_id) {
        if ($user_id == 0) {
            $objUser = new \stdClass();
            $objUser->id=0;
            $objUser->chr_usuario='';
            $objUser->chr_password='';
            $objUser->bool_active='1';
            $objUser->chr_nombre='';
            $objUser->chr_apellido_paterno='';
            $objUser->chr_apellido_materno='';
            $objUser->date_fecha_nacimiento='';
            $objUser->chr_dni='';
            $objUser->chr_telefono='';
            $objUser->chr_domicilio='';
            $objUser->date_fecha_registro='';
            $objUser->date_fecha_actualizacion='';
            $objUser->int_usuario_actualizacion='';
            $objUser->chr_email='';
            $objUser->is_deleted=1;
            $row = $objUser;
        } else {
            $where = new \Zend\Db\Sql\Where();
            $where->equalTo('id', $user_id);
            $rowset = $this->select($where);
            $row = $rowset->current();
        }
        return $row;
    }
    
    /**
     * Método para registrar y/o actualizar un usuario
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param array $form
     */
    public function saveUser($form) {
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
                'chr_usuario' => $form['chr_usuario'],
                'chr_password' => md5($form['chr_password']),
                'bool_active' => $form['bool_active'],
                'chr_nombre' => $form['chr_nombre'],
                'chr_apellido_paterno' => $form['chr_apellido_paterno'],
                'chr_apellido_materno' => $form['chr_apellido_materno'],
                'chr_telefono' => $form['chr_telefono'],
                'chr_dni' => $form['chr_dni'],
                'chr_email' => $form['chr_email'],
                'chr_domicilio' => $form['chr_domicilio'],
                'date_fecha_registro' => date("Y-m-d H:i:s"),
                'int_usuario_actualizacion' => $session->user->id,
            );
        } else {
            $data = array(
                'bool_active' => $form['bool_active'],
                'chr_nombre' => $form['chr_nombre'],
                'chr_apellido_paterno' => $form['chr_apellido_paterno'],
                'chr_apellido_materno' => $form['chr_apellido_materno'],
                'chr_telefono' => $form['chr_telefono'],
                'chr_dni' => $form['chr_dni'],
                'chr_email' => $form['chr_email'],
                'chr_domicilio' => $form['chr_domicilio'],
                'int_usuario_actualizacion' => $session->user->id,
                'date_fecha_actualizacion' => date("Y-m-d H:i:s")
            );
        }

        $id = (int) $form['id'];
        if ($id == 0) {
            $this->insert($data);
        } else {
            if ($this->getUserById($id)) {
                $this->update($data, array('id' => $id));
            } else {
                throw new \Exception('El Usuaario no existe');
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
    
    public function findUser($txtSearch){
        if(strlen(trim($txtSearch))>0){
            $select = $this->getSql()->select()->where("chr_nombre LIKE '%".$txtSearch."%'")->order('id ASC');
        }else{
            $select = $this->getSql()->select();
        }
        $resultSet = $this->selectWith($select)->toArray();
        return $resultSet; 
    }


}