/*
SQLyog Ultimate v10.42 
MySQL - 5.5.34-0ubuntu0.13.10.1 : Database - escuela
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`escuela` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `escuela`;

/*Table structure for table `tb_cur_curso` */

DROP TABLE IF EXISTS `tb_cur_curso`;

CREATE TABLE `tb_cur_curso` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `int_instructor_id` int(11) NOT NULL,
  `int_categoria_id` int(11) NOT NULL,
  `chr_nombre` varchar(80) DEFAULT NULL,
  `bool_active` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tb_cur_curso_tb_cur_instructor1_idx` (`int_instructor_id`),
  KEY `fk_tb_cur_curso_tb_cur_categoria1_idx` (`int_categoria_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tb_cur_curso` */

/*Table structure for table `tb_cur_instructor` */

DROP TABLE IF EXISTS `tb_cur_instructor`;

CREATE TABLE `tb_cur_instructor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `int_usuario_id` int(11) NOT NULL,
  `int_tipo_instructor_id` int(11) NOT NULL,
  `bool_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_tb_cur_instructor_tb_seg_usuario1_idx` (`int_usuario_id`),
  KEY `fk_tb_cur_instructor_tb_cur_tipo_instructor1_idx` (`int_tipo_instructor_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tb_cur_instructor` */

/*Table structure for table `tb_cur_tipo_instructor` */

DROP TABLE IF EXISTS `tb_cur_tipo_instructor`;

CREATE TABLE `tb_cur_tipo_instructor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chr_nombre` varchar(45) DEFAULT NULL,
  `bool_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tb_cur_tipo_instructor` */

/*Table structure for table `tb_seg_config` */

DROP TABLE IF EXISTS `tb_seg_config`;

CREATE TABLE `tb_seg_config` (
  `chr_variable` varchar(45) NOT NULL,
  `chr_value` varchar(250) DEFAULT NULL,
  `bool_active` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tb_seg_config` */

insert  into `tb_seg_config`(`chr_variable`,`chr_value`,`bool_active`) values ('siteNameSmall','SAEM',1),('siteNameLarge','Sistema de Administración de Escuela de Manejo',1);

/*Table structure for table `tb_seg_grupo` */

DROP TABLE IF EXISTS `tb_seg_grupo`;

CREATE TABLE `tb_seg_grupo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chr_nombre` varchar(45) DEFAULT NULL,
  `chr_nombre_publico` varchar(100) DEFAULT NULL,
  `bool_active` varchar(45) DEFAULT NULL,
  `int_order` tinyint(4) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  `id_user_creacion` int(11) DEFAULT NULL,
  `id_user_actualizacion` int(11) DEFAULT NULL,
  `is_deleted` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `tb_seg_grupo` */

insert  into `tb_seg_grupo`(`id`,`chr_nombre`,`chr_nombre_publico`,`bool_active`,`int_order`,`fecha_creacion`,`fecha_actualizacion`,`id_user_creacion`,`id_user_actualizacion`,`is_deleted`) values (1,'admin','administrador','1',NULL,NULL,NULL,NULL,NULL,0);

/*Table structure for table `tb_seg_grupo_submenu` */

DROP TABLE IF EXISTS `tb_seg_grupo_submenu`;

CREATE TABLE `tb_seg_grupo_submenu` (
  `int_id_grupo` int(11) NOT NULL,
  `int_submenu_id` int(11) NOT NULL,
  PRIMARY KEY (`int_id_grupo`,`int_submenu_id`),
  KEY `fk_tb_tipo_usuario_has_tb_seg_submenu_tb_seg_submenu1_idx` (`int_submenu_id`),
  KEY `fk_tb_tipo_usuario_has_tb_seg_submenu_tb_tipo_usuario1_idx` (`int_id_grupo`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tb_seg_grupo_submenu` */

insert  into `tb_seg_grupo_submenu`(`int_id_grupo`,`int_submenu_id`) values (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,9),(1,10);

/*Table structure for table `tb_seg_menu` */

DROP TABLE IF EXISTS `tb_seg_menu`;

CREATE TABLE `tb_seg_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `int_modulo_id` int(11) NOT NULL,
  `chr_nombre` varchar(100) DEFAULT NULL,
  `bool_active` tinyint(4) DEFAULT '1',
  `int_order` tinyint(4) DEFAULT NULL,
  `id_user_actualizacion` int(11) DEFAULT NULL,
  `id_user_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tb_seg_menu_tb_seg_modulo1_idx` (`int_modulo_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Data for the table `tb_seg_menu` */

insert  into `tb_seg_menu`(`id`,`int_modulo_id`,`chr_nombre`,`bool_active`,`int_order`,`id_user_actualizacion`,`id_user_creacion`,`fecha_creacion`,`fecha_actualizacion`) values (1,2,'configuración',1,1,NULL,NULL,NULL,NULL),(2,3,'Registros',1,2,1,NULL,NULL,'2013-09-22 14:35:26'),(3,2,'Módulos',1,2,NULL,NULL,NULL,NULL),(7,2,'tesssss',1,3,NULL,1,'2013-09-22 14:50:06',NULL),(8,2,'Grupos',1,3,NULL,1,'2013-09-22 22:50:16',NULL);

/*Table structure for table `tb_seg_modulo` */

DROP TABLE IF EXISTS `tb_seg_modulo`;

CREATE TABLE `tb_seg_modulo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chr_nombre` varchar(100) DEFAULT NULL,
  `bool_active` tinyint(4) DEFAULT NULL,
  `int_order` tinyint(4) DEFAULT NULL,
  `chr_nombre_publico` varchar(100) DEFAULT NULL,
  `is_deleted` tinyint(4) DEFAULT '1',
  `id_user_actualizacion` int(11) DEFAULT NULL,
  `id_user_creacion` int(11) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

/*Data for the table `tb_seg_modulo` */

insert  into `tb_seg_modulo`(`id`,`chr_nombre`,`bool_active`,`int_order`,`chr_nombre_publico`,`is_deleted`,`id_user_actualizacion`,`id_user_creacion`,`fecha_creacion`,`fecha_actualizacion`) values (1,'seguridad',0,1,'Seguridad',0,NULL,NULL,NULL,NULL),(2,'administracion',1,2,'Administración',0,NULL,NULL,NULL,NULL),(3,'tramite',1,3,'Trámites',1,1,NULL,NULL,'2013-09-21 13:25:18'),(14,'testing',1,5,'Testing',1,1,1,'2013-09-21 19:33:34','2013-09-21 19:34:01');

/*Table structure for table `tb_seg_submenu` */

DROP TABLE IF EXISTS `tb_seg_submenu`;

CREATE TABLE `tb_seg_submenu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `int_menu_id` int(11) NOT NULL,
  `chr_nombre` varchar(100) DEFAULT NULL,
  `bool_active` tinyint(4) DEFAULT '1',
  `int_order` tinyint(4) DEFAULT NULL,
  `chr_url` varchar(250) DEFAULT '#',
  `id_user_creacion` int(11) DEFAULT NULL,
  `id_user_actualizacion` int(11) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT NULL,
  `fecha_actualizacion` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tb_seg_submenu_tb_seg_menu1_idx` (`int_menu_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

/*Data for the table `tb_seg_submenu` */

insert  into `tb_seg_submenu`(`id`,`int_menu_id`,`chr_nombre`,`bool_active`,`int_order`,`chr_url`,`id_user_creacion`,`id_user_actualizacion`,`fecha_registro`,`fecha_actualizacion`) values (1,1,'Editar configuración',1,1,'/administracion/configuracion',NULL,1,NULL,'2013-10-20 11:08:27'),(2,2,'Nuevo trámite',1,1,'/tramite/nuevo',NULL,NULL,NULL,NULL),(3,3,'Módulos',1,1,'/administracion/modulos',NULL,1,NULL,'2013-09-22 22:41:59'),(4,3,'Menús',1,3,'/administracion/menus',NULL,NULL,NULL,NULL),(5,3,'Submenu',1,5,'/administracion/submenus',NULL,NULL,NULL,NULL),(6,3,'Crear módulo',1,2,'/administracion/modulos/nuevo',NULL,NULL,NULL,NULL),(7,3,'Crear menú',1,4,'/administracion/menus/nuevo',NULL,NULL,NULL,NULL),(9,3,'Crear submenu',1,6,'/administracion/submenus/nuevo',NULL,1,NULL,'2013-10-08 18:21:55'),(10,8,'Grupos',1,1,'/administracion/grupo',NULL,1,NULL,'2013-10-20 12:09:32'),(11,1,'eeee',1,2,'#',1,NULL,'2013-10-20 11:27:38',NULL);

/*Table structure for table `tb_seg_usuario` */

DROP TABLE IF EXISTS `tb_seg_usuario`;

CREATE TABLE `tb_seg_usuario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chr_usuario` varchar(45) DEFAULT NULL,
  `chr_password` varchar(45) DEFAULT NULL,
  `bool_active` varchar(45) DEFAULT NULL,
  `chr_nombre` varchar(150) DEFAULT NULL,
  `chr_apellido_paterno` varchar(100) DEFAULT NULL,
  `chr_apellido_materno` varchar(100) DEFAULT NULL,
  `date_fecha_nacimiento` datetime DEFAULT NULL,
  `chr_dni` varchar(8) DEFAULT NULL,
  `chr_telefono` varchar(20) DEFAULT NULL,
  `chr_domicilio` varchar(250) DEFAULT NULL,
  `date_fecha_registro` datetime DEFAULT NULL,
  `date_fecha_actualizacion` datetime DEFAULT NULL,
  `int_usuario_actualizacion` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `tb_seg_usuario` */

insert  into `tb_seg_usuario`(`id`,`chr_usuario`,`chr_password`,`bool_active`,`chr_nombre`,`chr_apellido_paterno`,`chr_apellido_materno`,`date_fecha_nacimiento`,`chr_dni`,`chr_telefono`,`chr_domicilio`,`date_fecha_registro`,`date_fecha_actualizacion`,`int_usuario_actualizacion`) values (1,'admin','21232f297a57a5a743894a0e4a801fc3','1','Johnny','Huamani','Palomino','2013-08-18 14:57:25','41762758','987517190','--','2013-08-18 14:57:43','2013-08-18 14:57:47',1),(2,'johnny','f4eb27cea7255cea4d1ffabf593372e8','1','Edmin','aaaa','jjjjj','2013-08-22 19:53:34','41253698','987517190','--','2013-08-22 19:53:51','2013-08-22 19:53:54',1);

/*Table structure for table `tb_seg_usuario_grupo` */

DROP TABLE IF EXISTS `tb_seg_usuario_grupo`;

CREATE TABLE `tb_seg_usuario_grupo` (
  `int_usuario_id` int(11) NOT NULL,
  `int_grupo_id` int(11) NOT NULL,
  PRIMARY KEY (`int_usuario_id`,`int_grupo_id`),
  KEY `fk_tb_seg_usuario_has_tb_seg_grupo_tb_seg_grupo1_idx` (`int_grupo_id`),
  KEY `fk_tb_seg_usuario_has_tb_seg_grupo_tb_seg_usuario1_idx` (`int_usuario_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tb_seg_usuario_grupo` */

insert  into `tb_seg_usuario_grupo`(`int_usuario_id`,`int_grupo_id`) values (1,1);

/*Table structure for table `tb_tmt_categoria` */

DROP TABLE IF EXISTS `tb_tmt_categoria`;

CREATE TABLE `tb_tmt_categoria` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `int_tramite_id` int(11) NOT NULL,
  `chr_nombre` varchar(100) DEFAULT NULL,
  `bool_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_tb_cur_categoria_tb_cur_tramite1_idx` (`int_tramite_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tb_tmt_categoria` */

/*Table structure for table `tb_tmt_estado` */

DROP TABLE IF EXISTS `tb_tmt_estado`;

CREATE TABLE `tb_tmt_estado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chr_estado` varchar(45) DEFAULT NULL,
  `bool_active` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tb_tmt_estado` */

/*Table structure for table `tb_tmt_ficha` */

DROP TABLE IF EXISTS `tb_tmt_ficha`;

CREATE TABLE `tb_tmt_ficha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chr_codigo` varchar(45) DEFAULT NULL,
  `int_usuario_id` int(11) NOT NULL,
  `tb_tmt_estado_id` int(11) NOT NULL,
  `date_fecha_inscripcion` datetime DEFAULT NULL,
  `date_fecha_inicio_curso` datetime DEFAULT NULL,
  `date_fecha_fin_curso` datetime DEFAULT NULL,
  `txt_observacion` varchar(45) DEFAULT NULL,
  `int_nota` tinyint(4) DEFAULT NULL,
  `bool_huella` tinyint(4) DEFAULT '0',
  `bool_firma` tinyint(4) DEFAULT '0',
  `tb_tmt_categoria_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_tb_tmt_ficha_tb_tmt_estado1_idx` (`tb_tmt_estado_id`),
  KEY `fk_tb_tmt_ficha_tb_seg_usuario1_idx` (`int_usuario_id`),
  KEY `fk_tb_tmt_ficha_tb_tmt_categoria1_idx` (`tb_tmt_categoria_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tb_tmt_ficha` */

/*Table structure for table `tb_tmt_tramite` */

DROP TABLE IF EXISTS `tb_tmt_tramite`;

CREATE TABLE `tb_tmt_tramite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `chr_nombre` varchar(150) DEFAULT NULL,
  `int_edad` int(11) DEFAULT NULL,
  `int_hora_tecnica` int(11) DEFAULT NULL,
  `int_hora_esapit` int(11) DEFAULT NULL,
  `int_hora_practica_manejo` int(11) DEFAULT NULL,
  `int_duracion_minima` int(11) DEFAULT NULL,
  `int_duracion_maxima` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tb_tmt_tramite` */

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
