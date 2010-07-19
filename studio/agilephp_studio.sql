/*
SQLyog Community v8.3 
MySQL - 5.1.37-1ubuntu5.1 : Database - agilephp_studio
*********************************************************************
*/


/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`agilephp_studio` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `agilephp_studio`;

/*Table structure for table `configs` */

DROP TABLE IF EXISTS `configs`;

CREATE TABLE `configs` (
  `name` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*Data for the table `configs` */

insert  into `configs`(`name`,`value`) values ('workspace','/home/jhahn/Apps/eclipse-galileo/workspace');
insert  into `configs`(`name`,`value`) values ('appstore_endpoint','http://appstore.makeabyte.com:8080/appstore/api?wsdl');
insert  into `configs`(`name`,`value`) values ('appstore_platformId','2');
insert  into `configs`(`name`,`value`) values ('appstore_username','');
insert  into `configs`(`name`,`value`) values ('appstore_password','');
insert  into `configs`(`name`,`value`) values ('appstore_apikey','');
insert  into `configs`(`name`,`value`) values ('pear_bin','/usr/bin/pear');
insert  into `configs`(`name`,`value`) values ('pecl_bin','/usr/bin/pecl');

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `name` varchar(25) NOT NULL,
  `description` text,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `roles` */

insert  into `roles`(`name`,`description`) values ('admin','This is an administrator account'),('test','This is a test account');

/*Table structure for table `server` */

DROP TABLE IF EXISTS `server`;

CREATE TABLE `server` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `type` int(255) DEFAULT NULL,
  `ip` varchar(15) DEFAULT NULL,
  `hostname` varchar(255) DEFAULT NULL,
  `profile` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_server` (`type`),
  CONSTRAINT `FK_server` FOREIGN KEY (`type`) REFERENCES `server_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `server` */

insert  into `server`(`id`,`type`,`ip`,`hostname`,`profile`) values (1,3,'192.168.1.10','mssql','Production'),(2,3,'localhost','mysql.localhost.localdomain','Development');

/*Table structure for table `server_type` */

DROP TABLE IF EXISTS `server_type`;

CREATE TABLE `server_type` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `type` varchar(100) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `vendor` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `server_type` */

insert  into `server_type`(`id`,`type`,`name`,`vendor`) values (1,'SQL','MSSQL 2000','MSSQL'),(2,'SQL','MSSQL 2005','MSSQL'),(3,'SQL','MSSQL 2008','MSSQL'),(4,'SQL','MySQL 4.0','MySQL'),(5,'SQL','MySQL 5.1','MySQL');

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(21) NOT NULL DEFAULT '',
  `data` text,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `sessions` */

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `username` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `last_login` datetime DEFAULT NULL,
  `roleId` varchar(25) DEFAULT NULL,
  `enabled` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`username`),
  KEY `FK_UserRoles` (`roleId`),
  CONSTRAINT `FK_UserRoles` FOREIGN KEY (`roleId`) REFERENCES `roles` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `users` */

/* Passwords are set to "test" !!! */
insert  into `users`(`username`,`password`,`email`,`created`,`last_login`,`roleId`,`enabled`) values ('admin','9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08','root@localhost','2009-09-06 15:27:44','1969-12-31 19:00:00','admin','1'),('test','9f86d081884c7d659a2feaa0c55ad015a3bf4f1b2b0b822cd15d6c15b0f00a08','test','2010-01-22 19:01:00','1969-12-31 19:00:00','test',NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
