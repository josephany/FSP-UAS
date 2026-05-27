-- MySQL dump 10.13  Distrib 5.6.23, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: fullstack
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `akun`
--

DROP TABLE IF EXISTS `akun`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `akun` (
  `username` varchar(20) NOT NULL,
  `password` varchar(100) DEFAULT NULL,
  `nrp_mahasiswa` char(9) DEFAULT NULL,
  `npk_dosen` char(6) DEFAULT NULL,
  `isadmin` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`username`),
  KEY `fk_akun_mahasiswa_idx` (`nrp_mahasiswa`),
  KEY `fk_akun_dosen1_idx` (`npk_dosen`),
  CONSTRAINT `fk_akun_dosen1` FOREIGN KEY (`npk_dosen`) REFERENCES `dosen` (`npk`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_akun_mahasiswa` FOREIGN KEY (`nrp_mahasiswa`) REFERENCES `mahasiswa` (`nrp`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `akun`
--

LOCK TABLES `akun` WRITE;
/*!40000 ALTER TABLE `akun` DISABLE KEYS */;
INSERT INTO `akun` VALUES ('admin','$2y$10$gcj6qSITF1uFEfHS9ux7Xukjve/fE3frJsAHJMSM3nSgDk.vy6EJ6',NULL,NULL,1),('Agnesha','$2y$10$OEYDXShoj5XEpqDAqjk/yOLjsM5IAXV04IzKJpkbSDjKhR7LDMMna','160423099',NULL,0),('Alvin','$2y$10$G9Vvx5QMTyc6VPF5Ph0yfuClQgExPtPss5OkBsQY4rgjoBQVNZ23S','160423053',NULL,0),('Andre','$2y$10$86F2Xs0nR7PL0bonnQGll.QMnAgYUmAAi1DMZv915YJ1z/axsDiUG',NULL,'208020',0),('Bobby','$2y$10$.eGEB0udujFbi5qYl66M6.Iw2lvZvKRk8mLKh0YksTMqDjWmvhB6u','160423185',NULL,0),('Ellysa','$2y$10$yZkeBmspMRikSz1tzWox9Oya/u3acQjwj6Rnq5RILFHsb4a2p9FIG',NULL,'203014',0),('Endah','$2y$10$cS7XTgXySJxI3bkoPqrMDeNKoTFJu/ObPTnXhTOJxzkF1TSMpv/Zi',NULL,'201007',0),('Enrico','$2y$10$7d7Xevg7204Sy2pzEYFSGu.vMHTViVURAnCaWFHkeNEVrcW0W9Bru','160723026',NULL,0),('Evan','$2y$10$I9BRSou8/m2coH/TXDQNq.Pvgcv20B/zGLLA3M7dVZUuaJ2vZPwzK','160423033',NULL,0),('Felix','$2y$10$ILx586KqarFO12Cmr/Tw/uvit3zCNJkVy7KJmJ1WOZGNsVGqwq0/e',NULL,'217023',0),('Fitri','$2y$10$Owsf10An5SV9e6eT/wAhmuZ/olS0Q8DUXOMCMy1zsa8kQfwr.UDce',NULL,'199020',0),('Grefarrel','$2y$10$BZ0oJJxsh2a9RofKy8ZzNeM2ad9zSCKYxeY3/A/9TXNleL4hwa4vm','160723027',NULL,0),('Hendra','$2y$10$SMCu38Epr/pFxuq9eN8XG.2Q4uha8piC4s4GEGlAwvK4ipX1ksS6W',NULL,'210034',0),('Heru','$2y$10$I6iO6vhp1ivzGXIFotGCAu/jLVmyvqwLyl1PVFyMYTaehJ1UE4n.e',NULL,'192014',0),('Jordan','$2y$10$j1P//6AreHdD8sMXx/T1DOpVWqHHt3O67IowvDmQbxF3FEwOD/sTW','160723023',NULL,0),('Maya','$2y$10$qRN8oCytvaWER4qb4B4C8ubGCp0Fs/RG8ZfR0JSm9qx.L9G9m0bSa',NULL,'215027',0),('Nicole','$2y$10$GNNU7rn1YPbynq.H0YO8oeSBHicWLz3WZICZoPwlHVRrdtXvzevEm','160423157',NULL,0),('Njoto','$2y$10$/pzl6OMKA2tHOJgN8O2YPO9Gvtq1mDnUYCk7I1flc0HMqgu1rcR5y',NULL,'201026',0),('Rexell','$2y$10$wiAxiSI9eO.bxSs.A.GQNeYU6mMq4ctx7RmQvU7/Ejuqa3iSch.tq','160423035',NULL,0),('Robert','$2y$10$a9fTeHodeVLAUGeGuq.DtuXWgpl5MPfNMD3jgffvo9W507OIfNgjO','160423188',NULL,0),('Susana','$2y$10$U0l8k2TremZWydzWhIsSLeefrw8Cx7w3MG1WkuhmYaok/SVkSfd6u',NULL,'197030',0),('Tyrza','$2y$10$qQqypBndGoJ127CYCRCbAu7QbbkbLcphz6vTxzIUa.nwgyXZQ8dqu',NULL,'210134',0),('Yujiro','$2y$10$0UiJ5K6OAyjKH/r/pPncE.iZAdWbPCSjYydRXCjUDBgQLWehxHEU6','160423171',NULL,0);
/*!40000 ALTER TABLE `akun` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `chat`
--

DROP TABLE IF EXISTS `chat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `chat` (
  `idchat` int(11) NOT NULL AUTO_INCREMENT,
  `idthread` int(11) NOT NULL,
  `username_pembuat` varchar(20) NOT NULL,
  `isi` text DEFAULT NULL,
  `tanggal_pembuatan` datetime DEFAULT NULL,
  PRIMARY KEY (`idchat`),
  KEY `fk_chat_thread1_idx` (`idthread`),
  KEY `fk_chat_akun1_idx` (`username_pembuat`),
  CONSTRAINT `fk_chat_akun1` FOREIGN KEY (`username_pembuat`) REFERENCES `akun` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_chat_thread1` FOREIGN KEY (`idthread`) REFERENCES `thread` (`idthread`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `chat`
--

LOCK TABLES `chat` WRITE;
/*!40000 ALTER TABLE `chat` DISABLE KEYS */;
/*!40000 ALTER TABLE `chat` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `dosen`
--

DROP TABLE IF EXISTS `dosen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `dosen` (
  `npk` char(6) NOT NULL,
  `nama` varchar(45) DEFAULT NULL,
  `foto_extension` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`npk`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `dosen`
--

LOCK TABLES `dosen` WRITE;
/*!40000 ALTER TABLE `dosen` DISABLE KEYS */;
INSERT INTO `dosen` VALUES ('192014','Heru Arwoko, M.T.','jpg'),('197030','Dr. Susana Limanto, M.Si.','jpg'),('199020','Fitri Dwi Kartikasari, M.Si.','jpg'),('201007','Endah Asmawati, M.Si.','jpg'),('201026','Njoto Benarkah, M.Sc.','jpg'),('203014','Dr. Ellysa Tjandra','jpg'),('208020','Dr. Andre','jpg'),('210034','Dr. Hendra Dinata','jpg'),('210134','Tyrza Adelia, M.Inf.Tech.','jpg'),('215027','Maya Hilda Lestari Louk, M.Sc.','jpg'),('217023','Felix Handani, M.Kom.','jpg');
/*!40000 ALTER TABLE `dosen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event` (
  `idevent` int(11) NOT NULL AUTO_INCREMENT,
  `idgrup` int(11) NOT NULL,
  `judul` varchar(45) DEFAULT NULL,
  `judul-slug` varchar(45) DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `jenis` enum('Privat','Publik') DEFAULT NULL,
  `poster_extension` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`idevent`),
  KEY `fk_event_grup1_idx` (`idgrup`),
  CONSTRAINT `fk_event_grup1` FOREIGN KEY (`idgrup`) REFERENCES `grup` (`idgrup`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event`
--

LOCK TABLES `event` WRITE;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grup`
--

DROP TABLE IF EXISTS `grup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `grup` (
  `idgrup` int(11) NOT NULL AUTO_INCREMENT,
  `username_pembuat` varchar(20) NOT NULL,
  `nama` varchar(45) DEFAULT NULL,
  `deskripsi` varchar(45) DEFAULT NULL,
  `tanggal_pembentukan` datetime DEFAULT NULL,
  `jenis` enum('Privat','Publik') DEFAULT NULL,
  `kode_pendaftaran` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`idgrup`),
  KEY `fk_grup_akun1_idx` (`username_pembuat`),
  CONSTRAINT `fk_grup_akun1` FOREIGN KEY (`username_pembuat`) REFERENCES `akun` (`username`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grup`
--

LOCK TABLES `grup` WRITE;
/*!40000 ALTER TABLE `grup` DISABLE KEYS */;
/*!40000 ALTER TABLE `grup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mahasiswa`
--

DROP TABLE IF EXISTS `mahasiswa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mahasiswa` (
  `nrp` char(9) NOT NULL,
  `nama` varchar(45) DEFAULT NULL,
  `gender` enum('Pria','Wanita') DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `angkatan` year(4) DEFAULT NULL,
  `foto_extention` varchar(4) DEFAULT NULL,
  PRIMARY KEY (`nrp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mahasiswa`
--

LOCK TABLES `mahasiswa` WRITE;
/*!40000 ALTER TABLE `mahasiswa` DISABLE KEYS */;
INSERT INTO `mahasiswa` VALUES ('160423033','Evan Daniel Tandiawan','Pria','2005-10-11',2023,'jpg'),('160423035','Rexell Stin','Pria','2025-10-01',2023,'jpg'),('160423053','Alvin Abel Darmawan','Pria','2025-10-09',2023,'jpg'),('160423099','Agnesha Riby Tjoanda','Wanita','2005-10-12',2023,'jpg'),('160423157','Nicole Olivia Tranggono','Wanita','2005-06-07',2023,'jpg'),('160423171','Yujiro Cokro','Pria','2025-09-29',2023,'jpg'),('160423185','Bobby Satria','Pria','2025-09-29',2023,'jpg'),('160423188','Robert Glenn Wunawan','Pria','2005-06-02',2023,'jpg'),('160723023','Jordan Tanadi','Pria','2005-07-06',2023,'jpg'),('160723026','Enrico Fery','Pria','2005-08-17',2023,'jpg'),('160723027','Grefarrel Leonard Novril Saputra','Pria','2005-11-12',2023,'jpg');
/*!40000 ALTER TABLE `mahasiswa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `member_grup`
--

DROP TABLE IF EXISTS `member_grup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `member_grup` (
  `idgrup` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  PRIMARY KEY (`idgrup`,`username`),
  KEY `fk_grup_has_akun_akun1_idx` (`username`),
  KEY `fk_grup_has_akun_grup1_idx` (`idgrup`),
  CONSTRAINT `fk_grup_has_akun_akun1` FOREIGN KEY (`username`) REFERENCES `akun` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_grup_has_akun_grup1` FOREIGN KEY (`idgrup`) REFERENCES `grup` (`idgrup`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `member_grup`
--

LOCK TABLES `member_grup` WRITE;
/*!40000 ALTER TABLE `member_grup` DISABLE KEYS */;
/*!40000 ALTER TABLE `member_grup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `thread`
--

DROP TABLE IF EXISTS `thread`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `thread` (
  `idthread` int(11) NOT NULL AUTO_INCREMENT,
  `username_pembuat` varchar(20) NOT NULL,
  `idgrup` int(11) NOT NULL,
  `tanggal_pembuatan` datetime DEFAULT NULL,
  `status` enum('Open','Close') DEFAULT 'Open',
  PRIMARY KEY (`idthread`),
  KEY `fk_thread_akun1_idx` (`username_pembuat`),
  KEY `fk_thread_grup1_idx` (`idgrup`),
  CONSTRAINT `fk_thread_akun1` FOREIGN KEY (`username_pembuat`) REFERENCES `akun` (`username`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_thread_grup1` FOREIGN KEY (`idgrup`) REFERENCES `grup` (`idgrup`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `thread`
--

LOCK TABLES `thread` WRITE;
/*!40000 ALTER TABLE `thread` DISABLE KEYS */;
/*!40000 ALTER TABLE `thread` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-10-22 22:53:03
