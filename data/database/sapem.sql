/*Data for the table `tb_seg_config` */

insert  into `tb_seg_config`(`chr_variable`,`chr_value`,`bool_active`) values ('siteNameSmall','SAEM',1),('siteNameLarge','Sistema de Administración de Escuela de Manejo',1);

/*Data for the table `tb_seg_grupo` */

insert  into `tb_seg_grupo`(`id`,`chr_nombre`,`chr_nombre_publico`,`bool_active`,`int_order`,`fecha_creacion`,`fecha_actualizacion`,`id_user_creacion`,`id_user_actualizacion`) values (1,'admin','administrador','1',NULL,NULL,NULL,NULL,NULL);

/*Data for the table `tb_seg_grupo_submenu` */

insert  into `tb_seg_grupo_submenu`(`int_id_grupo`,`int_submenu_id`) values (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,9),(1,10);

/*Data for the table `tb_seg_menu` */

insert  into `tb_seg_menu`(`id`,`int_modulo_id`,`chr_nombre`,`bool_active`,`int_order`,`id_user_actualizacion`,`id_user_creacion`,`fecha_creacion`,`fecha_actualizacion`) values (1,2,'configuración',1,1,NULL,NULL,NULL,NULL),(2,3,'Registros',1,2,1,NULL,NULL,'2013-09-22 14:35:26'),(3,2,'Módulos',1,2,NULL,NULL,NULL,NULL),(7,2,'tesssss',1,3,NULL,1,'2013-09-22 14:50:06',NULL),(8,2,'Grupos',1,3,NULL,1,'2013-09-22 22:50:16',NULL);

/*Data for the table `tb_seg_modulo` */

insert  into `tb_seg_modulo`(`id`,`chr_nombre`,`bool_active`,`int_order`,`chr_nombre_publico`,`is_deleted`,`id_user_actualizacion`,`id_user_creacion`,`fecha_creacion`,`fecha_actualizacion`) values (1,'seguridad',0,1,'Seguridad',0,NULL,NULL,NULL,NULL),(2,'administracion',1,2,'Administración',0,NULL,NULL,NULL,NULL),(3,'tramite',1,3,'Trámites',1,1,NULL,NULL,'2013-09-21 13:25:18'),(14,'testing',1,5,'Testing',1,1,1,'2013-09-21 19:33:34','2013-09-21 19:34:01');

/*Data for the table `tb_seg_submenu` */

insert  into `tb_seg_submenu`(`id`,`int_menu_id`,`chr_nombre`,`bool_active`,`int_order`,`chr_url`,`id_user_creacion`,`id_user_actualizacion`,`fecha_registro`,`fecha_actualizacion`) values (1,1,'Editar configuración',1,1,'/administracion/configuracion',NULL,1,NULL,'2013-10-20 11:08:27'),(2,2,'Nuevo trámite',1,1,'/tramite/nuevo',NULL,NULL,NULL,NULL),(3,3,'Módulos',1,1,'/administracion/modulos',NULL,1,NULL,'2013-09-22 22:41:59'),(4,3,'Menús',1,3,'/administracion/menus',NULL,NULL,NULL,NULL),(5,3,'Submenu',1,5,'/administracion/submenus',NULL,NULL,NULL,NULL),(6,3,'Crear módulo',1,2,'/administracion/modulos/nuevo',NULL,NULL,NULL,NULL),(7,3,'Crear menú',1,4,'/administracion/menus/nuevo',NULL,NULL,NULL,NULL),(9,3,'Crear submenu',1,6,'/administracion/submenus/nuevo',NULL,1,NULL,'2013-10-08 18:21:55'),(10,8,'Grupos',1,1,'/administracion/grupo',NULL,1,NULL,'2013-10-20 12:09:32'),(11,1,'eeee',1,2,'#',1,NULL,'2013-10-20 11:27:38',NULL);

/*Data for the table `tb_seg_usuario` */

insert  into `tb_seg_usuario`(`id`,`chr_usuario`,`chr_password`,`bool_active`,`chr_nombre`,`chr_apellido_paterno`,`chr_apellido_materno`,`date_fecha_nacimiento`,`chr_dni`,`chr_telefono`,`chr_domicilio`,`date_fecha_registro`,`date_fecha_actualizacion`,`int_usuario_actualizacion`) values (1,'admin','21232f297a57a5a743894a0e4a801fc3','1','Johnny','Huamani','Palomino','2013-08-18 14:57:25','41762758','987517190','--','2013-08-18 14:57:43','2013-08-18 14:57:47',1),(2,'johnny','f4eb27cea7255cea4d1ffabf593372e8','1','Edmin','aaaa','jjjjj','2013-08-22 19:53:34','41253698','987517190','--','2013-08-22 19:53:51','2013-08-22 19:53:54',1);

/*Data for the table `tb_seg_usuario_grupo` */

insert  into `tb_seg_usuario_grupo`(`int_usuario_id`,`int_grupo_id`) values (1,1);
