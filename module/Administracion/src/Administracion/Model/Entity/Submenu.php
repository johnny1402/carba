<?php

namespace Administracion\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Session\Container;

class Submenu extends TableGateway {

    /**
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @var object 
     */
    public $dbAdapter;

    public function __construct(Adapter $adapter = NULL, $databaseSchema = NULL, ResultSet $selectResultPrototype = NULL) {
        $this->dbAdapter = $adapter;
        parent::__construct('tb_seg_submenu', $adapter, $databaseSchema, $selectResultPrototype);
    }

    public function getSubmenuByIdMenu($menu_id) {
        $where = new \Zend\Db\Sql\Where();
        $where->in('int_menu_id', array($menu_id));
        $where->in('bool_active', array(1));
        $select = $this->getSql()->select()->where($where)->order('int_order ASC');
        $resultSet = $this->selectWith($select)->toArray();
        return $resultSet;
    }

    /**
     * Método para obtener los submenus que tiene acceso el usuario
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $menu_id
     * @param int $user_id
     * @return array
     */
    public function getSubmenuByIdMenuByUser($menu_id, $user_id) {
        //listamos los submenus que corresponden al menu
        $lista_submenu = $this->getSubmenuByIdMenu($menu_id);
        if (count($lista_submenu) > 0) {
            foreach ($lista_submenu as $indice => $arraySubmenu) {
                //verificamos que este submenu tenga permisos de acceso para este usuario
                if (!$this->accessUserSubmenu($arraySubmenu, $user_id)) {
                    unset($lista_submenu[$indice]);
                }
            }
        }
        return $lista_submenu;
    }

    /**
     * Método para verificar que el submenu tenga acceso por el usuario
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param array $arraySubmenu
     * @param int $user_id
     * @return boolean
     */
    private function accessUserSubmenu($arraySubmenu, $user_id) {
        $returnValue = FALSE;
        //listamos los grupos al que pertenece el usuario
        $grupos = $this->getGruposByUserId($user_id);
        //verificamos si existen grupos
        if (count($grupos) > 0) {
            $array_id_grupos = array();
            foreach ($grupos as $index => $arrayGrupo) {
                array_push($array_id_grupos, $arrayGrupo['int_grupo_id']);
            }
            //validamos que el arra de ID de grupos tenga contenido
            if (count($array_id_grupos) > 0) {
                $objGrupoSubmenu = new \Administracion\Model\Entity\Gruposubmenu($this->dbAdapter);
                $grupoSubmenu = $objGrupoSubmenu->getSubmenuByUserIdByGrupos($arraySubmenu['id'], $array_id_grupos);
                if (count($grupoSubmenu) > 0) {
                    $returnValue = TRUE;
                }
            }
        }
        return $returnValue;
    }

    public function getGruposByUserId($user_id) {
        $objMenu = new \Administracion\Model\Entity\Menu($this->dbAdapter);
        return $objMenu->getGruposByUserId($user_id);
    }

    /**
     * obtener el submenu que tenga asignado esta url
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param string $path_uri
     * @return array
     */
    public function findSubmenuByUrl($path_uri) {
        $where = new \Zend\Db\Sql\Where();
        $where->like('chr_url', $path_uri);
        $where->in('bool_active', array(1));
        $rowset = $this->select($where);
        return $rowset->current();
    }

    /**
     * Método para listar todos los menus
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @return array
     */
    public function getSubmenu() {
        $where = array(); // If have any criteria
        $result = $this->select(function (Select $select) use ($where) {
                    $select->join('tb_seg_menu', 'tb_seg_menu.id = tb_seg_submenu.int_menu_id', array('chr_nombre_menu' => 'chr_nombre', 'int_modulo_id'));
                    $select->join('tb_seg_modulo', 'tb_seg_menu.int_modulo_id = tb_seg_modulo.id', array('chr_nombre_modulo' => 'chr_nombre'));
                    $select->order('tb_seg_submenu.int_menu_id DESC');
                    //echo $select->getSqlString(); // see the sql query
                });
        return $result;
    }

    /**
     * Método para eliminar el submenú directamente en la Base de datos
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param type $submenu_id
     * @return boolean
     */
    public function deleteSubmenu($submenu_id) {
        $where = new \Zend\Db\Sql\Where();
        $where->equalTo('id', $submenu_id);
        $this->delete($where);
    }

    /**
     * Método para obtener el menu por su ID
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @return array
     */
    public function getSubmenuById($submenu_id) {
        if ($submenu_id == 0) {
            $objSubmenu = new \stdClass();
            $objSubmenu->id = 0;
            $objSubmenu->chr_nombre = '';
            $objSubmenu->bool_active = '1';
            $objSubmenu->int_order = '';
            $objSubmenu->int_menu_id = 1;
            $objSubmenu->int_modulo_id = 1;
            $objSubmenu->chr_url = '';
            $row = $objSubmenu;
        } else {
            $where = new \Zend\Db\Sql\Where();
            $where->equalTo('id', $submenu_id);
            $rowset = $this->select($where);
            $row = $rowset->current();
        }
        return $row;
    }

    /**
     * Método para registrar y/o actualizar un submenu
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param array $form
     */
    public function saveSubmenu($form) {
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
                'int_menu_id' => $form['int_menu_id'],
                'bool_active' => $form['bool_active'],
                'chr_url' => $form['chr_url'],
                'int_order' => $form['int_order'],
                'fecha_registro' => date("Y-m-d H:i:s"),
                'id_user_creacion' => $form['id_user_creacion']
            );
        } else {
            $data = array(
                'bool_active' => $form['bool_active'],
                'int_order' => $form['int_order'],
                'chr_url' => $form['chr_url'],
                'int_menu_id' => $form['int_menu_id'],
                'chr_nombre' => $form['chr_nombre'],
                'id_user_actualizacion' => $session->user->id,
                'fecha_actualizacion' => date("Y-m-d H:i:s")
            );
        }
        $id = (int) $form['id'];
        if ($id == 0) {
            $returnValue = $this->insert($data);
            
        } else {
            if ($this->getSubmenuById($id)) {
                $this->update($data, array('id' => $id));
            } else {
                throw new \Exception('El menú no existe');
            }
        }
    }

}