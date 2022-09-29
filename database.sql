/*
SQLyog Ultimate v13.1.1 (64 bit)
MySQL - 5.7.19 : Database - api_ci
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`api_ci` /*!40100 DEFAULT CHARACTER SET latin1 */;

USE `api_ci`;

/*Table structure for table `log_activity` */

DROP TABLE IF EXISTS `log_activity`;

CREATE TABLE `log_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(200) DEFAULT NULL,
  `activity_date` timestamp NULL DEFAULT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `ip` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;

/*Data for the table `log_activity` */

insert  into `log_activity`(`id`,`username`,`activity_date`,`activity`,`product_id`,`ip`) values 
(1,'admin','2022-09-28 11:10:36','Berhasil Menambah data produk',8,'127.0.0.1'),
(2,'admin','2022-09-28 11:17:07','Berhasil update data produk',1,'127.0.0.1'),
(3,'admin','2022-09-28 11:17:40','Berhasil menghapus data produk',NULL,'127.0.0.1'),
(4,'admin','2022-09-28 11:18:26','Berhasil menambah stock produk',2,'127.0.0.1'),
(5,'admin','2022-09-28 11:23:14','Gagal menambahkan stock,Data tidak ditemukan',2,'127.0.0.1'),
(6,'admin','2022-09-28 11:23:29','Gagal menambahkan stock,Data tidak ditemukan',2,'127.0.0.1'),
(7,'admin','2022-09-28 11:24:13','Berhasil menambah stock produk',3,'127.0.0.1'),
(8,'admin','2022-09-28 11:27:59','Gagal mengurangi stock,Data tidak ditemukan',2,'127.0.0.1'),
(9,'admin','2022-09-28 11:28:05','Berhasil mengurangi stock produk',3,'127.0.0.1'),
(10,'admin','2022-09-28 11:28:09','Berhasil mengurangi stock produk',3,'127.0.0.1'),
(11,'admin','2022-09-28 11:28:11','Berhasil mengurangi stock produk',3,'127.0.0.1'),
(12,'admin','2022-09-28 11:28:14','Gagal mengurangi stock, stok tersedia 10',3,'127.0.0.1'),
(13,'admin','2022-09-28 11:28:19','Gagal mengurangi stock, stok tersedia 10',3,'127.0.0.1'),
(14,'admin','2022-09-28 11:41:08','Berhasil update data produk',1,'127.0.0.1'),
(15,'admin','2022-09-28 11:41:11','Berhasil update data produk',1,'127.0.0.1'),
(16,'admin','2022-09-28 11:42:03','gagal menambah produk, Nama Produk sudah ada di database',0,'127.0.0.1'),
(17,'admin','2022-09-28 11:42:13','Berhasil update data produk',3,'127.0.0.1'),
(18,'admin','2022-09-28 11:42:29','gagal update produk, Nama Produk sudah ada di database',3,'127.0.0.1'),
(19,'admin','2022-09-28 11:42:32','gagal update produk, Nama Produk sudah ada di database',3,'127.0.0.1'),
(20,'admin','2022-09-28 15:06:26','gagal menambah produk, Nama Produk sudah ada di database',0,'127.0.0.1'),
(21,'admin','2022-09-28 15:07:12','Berhasil Menambah data produk',9,'127.0.0.1'),
(22,'admin','2022-09-28 15:22:34','Gagal menambahkan stock, produk sudah expired',3,'127.0.0.1'),
(23,'admin','2022-09-28 15:23:17','Berhasil menambah stock produk',3,'127.0.0.1'),
(24,'admin','2022-09-28 15:24:40','Berhasil menambah stock produk',3,'127.0.0.1'),
(25,'admin','2022-09-28 15:27:38','Berhasil mengurangi stock produk',3,'127.0.0.1'),
(26,'admin','2022-09-28 15:27:41','Berhasil mengurangi stock produk',3,'127.0.0.1'),
(27,'admin','2022-09-28 15:41:15','Berhasil update data produk',3,'127.0.0.1'),
(28,'admin','2022-09-28 15:41:50','gagal update produk, Nama Produk sudah ada di database',3,'127.0.0.1');

/*Table structure for table `product` */

DROP TABLE IF EXISTS `product`;

CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_name` varchar(255) DEFAULT NULL,
  `product_description` varchar(255) DEFAULT NULL,
  `price` int(11) DEFAULT NULL,
  `stock` int(11) DEFAULT NULL,
  `exp_date` datetime DEFAULT NULL,
  `user_input` varchar(100) DEFAULT NULL,
  `input_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `user_update` varchar(100) DEFAULT NULL,
  `update_date` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

/*Data for the table `product` */

insert  into `product`(`id`,`product_name`,`product_description`,`price`,`stock`,`exp_date`,`user_input`,`input_date`,`user_update`,`update_date`) values 
(3,'Xiaomi Mi TV Stick update xxx','Xiaomi Mi TV Stick description',500000,100,'2022-09-30 22:10:50','arkan',NULL,'admin','2022-09-28 15:41:15'),
(4,'Notebook Asus X200','Notebook Asus Spec',4000000,30,'2022-10-28 22:10:56','arkan',NULL,NULL,NULL),
(5,'Notebook Asus X200','Notebook Asus Spec',4000000,30,'2022-09-23 22:11:02','arkan',NULL,NULL,NULL),
(6,'Xiaomi Mi TV Stick','Xiaomi Mi TV Stick description',500000,100,'2022-09-26 22:11:09','arkan','2022-09-28 07:00:09',NULL,NULL),
(7,'Xiaomi Mi TV Stick','Xiaomi Mi TV Stick description',500000,100,'2023-09-28 22:11:14','admin','2022-09-28 11:04:58',NULL,NULL),
(8,'Xiaomi Mi TV Stick','Xiaomi Mi TV Stick description',500000,100,'2022-09-29 22:11:19','admin','2022-09-28 11:10:36',NULL,NULL),
(9,'Xiaomi Mi TV Stick xxx','Xiaomi Mi TV Stick description',500000,100,'2022-09-28 22:11:25','admin','2022-09-28 15:07:12',NULL,NULL);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) DEFAULT NULL,
  `pass` varchar(255) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

/*Data for the table `users` */

insert  into `users`(`id`,`username`,`pass`,`email`) values 
(1,'admin','$2y$10$CukuHEU/9FMBuf6E5ajLC.FgZyLpKpGAyCetqnqdL18g9bPsnL2ku','email@admin.com'),
(2,'admin','$2y$10$uyZd16Wl688xbWs44VBbLOqdhychi1gl79v2iiVysWjbpr8K.GnUO','email@admin.com'),
(3,'admin','$2y$10$VObSmrMnompUuonblnctl.qFi33mkPKKvOTEAJ72TXpHEsG/XrcSa','email@admin.com');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
