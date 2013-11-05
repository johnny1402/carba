<?php

namespace Administracion\Model\Entity;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Select;
use Zend\Session\Container;

class Menu extends TableGateway {

    protected $dbAdapter;

    public function __construct(Adapter $adapter = NULL, $databaseSchema = NULL, ResultSet $selectResultPrototype = NULL) {
        $this->dbAdapter = $adapter;
        parent::__construct('tb_seg_menu', $adapter, $databaseSchema, $selectResultPrototype);
    }

    public function getMenuByIdSubmenu($array_submenu_id) {
        $where = new \Zend\Db\Sql\Where();
        $where->in('id', $array_submenu_id);
        $where->in('bool_active', array(1));
        $rowset = $this->select($where);
        $row = $rowset->toArray();
        return $row;
    }

    /**
     * Metodo para listar los menus de un modulo
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $modulo_id
     * @return array
     */
    public function getMenuByIdModulo($modulo_id) {
        $where = new \Zend\Db\Sql\Where();
        $where->in('int_modulo_id', array($modulo_id));
        $where->in('bool_active', array(1));
        $select = $this->getSql()->select()->where($where)->order('int_order ASC');
        $resultSet = $this->selectWith($select)->toArray();
        return $resultSet;
    }
    /**
     * Metodo para listar los menus de un modulo
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $modulo_id
     * @return array
     */
    public function listarMenuByIdModulo($modulo_id) {
        $where = new \Zend\Db\Sql\Where();
        $where->in('int_modulo_id', array($modulo_id));
        $select = $this->getSql()->select()->where($where)->order('int_order ASC');
        $resultSet = $this->selectWith($select)->toArray();
        return $resultSet;
    }

    /**
     * Método para obtener la lista de menú con referencia al usuario en linea
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $modulo_id
     * @param int $user_id
     * @return array
     */
    public function getMenuByIdModuloByUserId($modulo_id, $user_id) {
        $lista_menus = $this->getMenuByIdModulo($modulo_id);
        //verificamos que tenga contenido
        if (count($lista_menus) > 0) {
            foreach ($lista_menus as $index => $Menu) {
                //verificamos si este menu tiene permiso de acceso para este usuario
                if (!$this->accessUserMenu($Menu, $user_id)) {
                    unset($lista_menus[$index]);
                }
            }
        }
        return $lista_menus;
    }

    /**
     * Método para verificar si este menu tiene accesos por el usuario
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param array $Menu
     * @param int $user_id
     * @return boolean
     */
    public function accessUserMenu($Menu, $user_id) {
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
                //obtenemos los submenus del menú
                $objSubmenu = new \Administracion\Model\Entity\Submenu($this->dbAdapter);
                $submenus = $objSubmenu->getSubmenuByIdMenu($Menu['id']);
                if (count($submenus) > 0) {
                    $array_submenu_id = array();
                    foreach ($submenus as $indice => $arraySubmenu) {
                        array_push($array_submenu_id, $arraySubmenu['id']);
                    }
                    //verificamos la cantidad de submenus que aya por cada menu
                    if (count($array_submenu_id) > 0) {
                        //verificamos si este menu tiene submenus referenciados por el grupo
                        $objGrupoSubmenu = new \Administracion\Model\Entity\Gruposubmenu($this->dbAdapter);
                        $grupoSubmenu = $objGrupoSubmenu->getGrupoSubmenu($arrayGrupo);
                        if (count($grupoSubmenu) > 0) {
                            $returnValue = TRUE;
                        }
                    }
                }
            }
        }
        return $returnValue;
    }

    /**
     * Método para listar los grupo al que pertenece el usuario
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param int $user_id
     * @return array
     */
    public function getGruposByUserId($user_id) {
        $objUsuarioGrupo = new \Administracion\Model\Entity\Usuariogrupo($this->dbAdapter);
        return $objUsuarioGrupo->getUsuariogrupo($user_id);
    }

    /**
     * Método para listar todos los menus
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @return array
     */
    public function getMenu() {
        $select = $this->getSql()->select()->order('int_modulo_id ASC');
        $resultSet = $this->selectWith($select)->toArray();
        return $resultSet;
    }

    /**
     * Método para obtener el menu por su ID
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @return array
     */
    public function getMenuById($menu_id) {
        if ($menu_id == 0) {
            $objModule = new \stdClass();
            $objModule->id = 0;
            $objModule->chr_nombre = '';
            $objModule->bool_active = '1';
            $objModule->int_order = '';
            $objModule->chr_nombre = '';
            $objModule->int_modulo_id = 1;
            $row = $objModule;
        } else {
            $where = new \Zend\Db\Sql\Where();
            $where->equalTo('id', $menu_id);
            $rowset = $this->select($where);
            $row = $rowset->current();
        }
        return $row;
    }

    /**
     * Método para eliminar el menú directamente en la Base de datos
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param type $menu_id
     * @return boolean
     */
    public function deleleMenu($menu_id) {
        $where = new \Zend\Db\Sql\Where();
        $where->equalTo('id', $menu_id);
        $this->delete($where);
    }

    /**
     * Método para registrar y/o actualizar un menu
     * @author Johnny Huamani <johnny1402@gmail.com>
     * @param array $form
     */
    public function saveMenu($form) {
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
                'int_modulo_id' => $form['int_modulo_id'],
                'bool_active' => $form['bool_active'],
                'int_order' => $form['int_order'],
                'fecha_creacion' => date("Y-m-d H:i:s"),
                'id_user_creacion' => $form['id_user_creacion']
            );
        } else {
            $data = array(
                'bool_active' => $form['bool_active'],
                'int_order' => $form['int_order'],
                'int_modulo_id' => $form['int_modulo_id'],
                'chr_nombre' => $form['chr_nombre'],
                'id_user_actualizacion' => $session->user->id,
                'fecha_actualizacion' => date("Y-m-d H:i:s")
            );
        }

        $id = (int) $form['id'];
        if ($id == 0) {
            $this->insert($data);
        } else {
            if ($this->getMenuById($id)) {
                $this->update($data, array('id' => $id));
            } else {
                throw new \Exception('El menú no existe');
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