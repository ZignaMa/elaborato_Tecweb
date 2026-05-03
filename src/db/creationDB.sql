-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Table structure for table `utenti`
--
-- Server version	5.5.5-10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
--
-- Table structure for table `utenti`
--

create database centrostudio;
use centrostudio;
DROP TABLE IF EXISTS `assistenti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assistenti` (
  `email` varchar(255) NOT NULL,
  `nome` varchar(255) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assistenti`
--

LOCK TABLES `assistenti` WRITE;
/*!40000 ALTER TABLE `assistenti` DISABLE KEYS */;
INSERT INTO `assistenti` VALUES ('luca.neri@universita.it','Luca Neri'),('chiara.mancini@universita.it','Chiara Mancini');
/*!40000 ALTER TABLE `assistenti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classi`
--

DROP TABLE IF EXISTS `classi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `corso_id` int(11) NOT NULL,
  `anno_accademico` varchar(255) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `sezione` varchar(255) NOT NULL,
  `professore` varchar(255) NOT NULL,
  `assistente` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `IDCLASSI` (`corso_id`,`anno_accademico`,`nome`,`sezione`),
  KEY `FKrefers` (`anno_accademico`),
  KEY `FKcorso` (`corso_id`),
  KEY `FKassistente_idx` (`assistente`),
  KEY `FKprofessore_idx` (`professore`),
  CONSTRAINT `FKassistente` FOREIGN KEY (`assistente`) REFERENCES `assistenti` (`email`),
  CONSTRAINT `FKprofessore` FOREIGN KEY (`professore`) REFERENCES `professori` (`email`),
  CONSTRAINT `FKrefers` FOREIGN KEY (`anno_accademico`) REFERENCES `anni_accademici` (`anno`),
  CONSTRAINT `FKrelated` FOREIGN KEY (`corso_id`) REFERENCES `corsi` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classi`
--

LOCK TABLES `classi` WRITE;
/*!40000 ALTER TABLE `classi` DISABLE KEYS */;
INSERT INTO `classi` VALUES (1,1,'2025-2026','Analisi matematica','A','matteo.rinaldi@universita.it',NULL),(2,1,'2025-2026','Analisi matematica','B','alessandro.deluca@universita.it',NULL),(3,1,'2025-2026','Programmazione','A','andrea.lombardi@universita.it',NULL),(4,1,'2025-2026','Programmazione','B','andrea.lombardi@universita.it',NULL),(5,1,'2025-2026','OOP','A','francesco.conti@universita.it','luca.neri@universita.it'),(6,1,'2025-2026','OOP','B','francesco.conti@universita.it','luca.neri@universita.it'),(7,1,'2025-2026','Tecnologie web','A','luca.bianchi@universita.it','chiara.mancini@universita.it'),(8,1,'2025-2026','Tecnologie web','B','luca.bianchi@universita.it','chiara.mancini@universita.it'),(9,2,'2025-2026','Aerodinamica','A','stefano.ricci@universita.it',NULL),(10,2,'2025-2026','Propulsione','A','marco.romano@universita.it',NULL),(11,2,'2025-2026','Strutture aerospaziali','A','francesco.varani@università.it',NULL),(12,1,'2024-2025','Analisi matematica','A','giovanni.ferri@universita.it',NULL),(13,1,'2024-2025','Analisi matematica','B','paolo.galli@universita.it',NULL);
/*!40000 ALTER TABLE `classi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `classi_dei_clienti`
--

DROP TABLE IF EXISTS `classi_dei_clienti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `classi_dei_clienti` (
  `classe_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`email`,`classe_id`),
  KEY `FKCLA_CLA` (`classe_id`),
  KEY `FKusers` (`email`),
  CONSTRAINT `FKCLA_CLA` FOREIGN KEY (`classe_id`) REFERENCES `classi` (`id`),
  CONSTRAINT `FKCLA_CLI` FOREIGN KEY (`email`) REFERENCES `utenti` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `classi_dei_clienti`
--

LOCK TABLES `classi_dei_clienti` WRITE;
/*!40000 ALTER TABLE `classi_dei_clienti` DISABLE KEYS */;
INSERT INTO `classi_dei_clienti` VALUES (9,'gianfranco.lippi@client.it'),(1,'mario.rossi@client.it'),(2,'mario.rossi@client.it'),(9,'mario.rossi@client.it'),(12,'mario.rossi@client.it'),(13,'mario.rossi@client.it'),(1,'filippo.argenti@client.it');
/*!40000 ALTER TABLE `classi_dei_clienti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `commenti`
--

DROP TABLE IF EXISTS `commenti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `commenti` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `testo` text NOT NULL,
  `data_e_ora` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `pubblicazione_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `percorso` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FKpubblicazione_idx` (`pubblicazione_id`),
  KEY `FKemail` (`email`),
  KEY `FKcr` (`percorso`),
  CONSTRAINT `FKcr` FOREIGN KEY (`percorso`) REFERENCES `risorse_commenti` (`percorso`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FKemail` FOREIGN KEY (`email`) REFERENCES `utenti` (`email`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FKpubblicazione` FOREIGN KEY (`pubblicazione_id`) REFERENCES `pubblicazioni` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `commenti`
--

LOCK TABLES `commenti` WRITE;
/*!40000 ALTER TABLE `commenti` DISABLE KEYS */;
INSERT INTO `commenti` (`testo`,`data_e_ora`,`pubblicazione_id`,`email`,`percorso`) VALUES
('Prova a dividere il problema in sottoproblemi e poi applica la ricorsione.','2026-02-10 10:00:00',4,'andrea.neri@admin.it',NULL),
('Grazie, ho scaricato gli appunti, molto utili.','2026-02-11 12:30:00',5,'filippo.argenti@client.it','/uploads/media/1/13/2024-2025-A/notes.pdf'),
('Secondo me l\'esame sarà più pratico quest\'anno, hanno dato molti esercizi in laboratorio.','2026-02-12 15:10:00',6,'mario.rossi@client.it',NULL),
('Posso aiutarti con il progetto finale, ho esperienza con OOP.','2026-02-15 19:00:00',7,'marco.neri@client.it',NULL),
('Usa display:flex; align-items:center; justify-content:center; per centrare facilmente.','2026-02-16 11:05:00',8,'maria.neri@admin.it',NULL),
('Inizio una lista di risorse: articolo X, video Y e libro Z, li condivido a breve.','2026-02-17 13:00:00',9,'stefano.ricci@universita.it',NULL),
('Perfetto, grazie per l\'avviso — ci sarò!','2026-02-18 16:20:00',10,'giulia.verdi@client.it',NULL);
/*!40000 ALTER TABLE `commenti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risorse_commenti`
--

DROP TABLE IF EXISTS `risorse_commenti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `risorse_commenti` (
  `percorso` varchar(255) NOT NULL,
  PRIMARY KEY (`percorso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risorse_commenti`
--

LOCK TABLES `risorse_commenti` WRITE;
/*!40000 ALTER TABLE `risorse_commenti` DISABLE KEYS */;
/*!40000 ALTER TABLE `risorse_commenti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corsi`
--

DROP TABLE IF EXISTS `corsi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
CREATE TABLE `corsi` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nome_UNIQUE` (`nome`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
--
-- Dumping data for table `corsi`
--

LOCK TABLES `corsi` WRITE;
/*!40000 ALTER TABLE `corsi` DISABLE KEYS */;
INSERT INTO `corsi` VALUES (1,'Ingegneria e scienze informatiche'),(2,'Ingegneria aerospaziale');
/*!40000 ALTER TABLE `corsi` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `corsi_dei_clienti`
--

DROP TABLE IF EXISTS `corsi_dei_clienti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `corsi_dei_clienti` (
  `email` varchar(255) NOT NULL,
  `corso_id` int(11) NOT NULL,
  PRIMARY KEY (`email`,`corso_id`),
  KEY `FKCOU_COU` (`corso_id`),
  KEY `FKusers` (`email`),
  CONSTRAINT `FKCOU_CLI` FOREIGN KEY (`email`) REFERENCES `utenti` (`email`),
  CONSTRAINT `FKCOU_COU` FOREIGN KEY (`corso_id`) REFERENCES `corsi` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `corsi_dei_clienti`
--

LOCK TABLES `corsi_dei_clienti` WRITE;
/*!40000 ALTER TABLE `corsi_dei_clienti` DISABLE KEYS */;
INSERT INTO `corsi_dei_clienti` VALUES ('filippo.argenti@client.it',1),('gianfranco.lippi@client.it',2),('mario.rossi@client.it',1),('mario.rossi@client.it',2);
/*!40000 ALTER TABLE `corsi_dei_clienti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pubblicazioni`
--

DROP TABLE IF EXISTS `pubblicazioni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pubblicazioni` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `testo` text NOT NULL,
  `data_e_ora` datetime NOT NULL,
  `classe_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `percorso` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `IDPUBBLICAZIONI` (`classe_id`),
  KEY `FKdo` (`email`),
  KEY `FKrisorse_in_pubblicazioni` (`percorso`),
  CONSTRAINT `FKabout` FOREIGN KEY (`classe_id`) REFERENCES `classi` (`id`),
  CONSTRAINT `FKdo` FOREIGN KEY (`email`) REFERENCES `utenti` (`email`),
  CONSTRAINT `FKrisorse_in_pubblicazioni` FOREIGN KEY (`percorso`) REFERENCES `risorse_pubblicazioni` (`percorso`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pubblicazioni`
--

LOCK TABLES `pubblicazioni` WRITE;
/*!40000 ALTER TABLE `pubblicazioni` DISABLE KEYS */;
INSERT INTO `pubblicazioni` VALUES (1,'Ho un problema con un compito di algebra, come risolvo il determinante di una radice con gauss?','2026-01-21 15:30:00',1,'mario.rossi@client.it',NULL),(3,'Lorem ipsum dolor sit amet consectetur adipisicing elit. Cupiditate temporibus quam a commodi expedita nobis aut ratione facere modi. Dolorem nam ex, a amet eligendi corrupti aliquid accusamus temporibus totam?','2026-01-21 15:20:00',1,'mario.rossi@client.it',NULL),(4,'Qualcuno ha suggerimenti su esercizi di programmazione ricorsiva?','2026-02-10 09:15:00',3,'filippo.argenti@client.it',NULL),(5,'Ho caricato gli appunti della lezione di Tecnologie Web nella sezione risorse','2026-02-11 11:00:00',7,'docente.test@universita.it','/uploads/media/1/13/2024-2025-A/notes.pdf'),(6,'Esame: ci saranno più esercitazioni pratiche o teoria?','2026-02-12 14:40:00',7,'giulia.verdi@client.it',NULL),(7,'Cerco compagno per progetto finale OOP','2026-02-15 18:05:00',5,'sara.federici@client.it',NULL),(8,'Domanda rapida: come si usa flexbox per centrare un elemento?','2026-02-16 10:22:00',7,'marco.neri@client.it',NULL),(9,'Avete trovato buone risorse per Aerodinamica?','2026-02-17 12:00:00',9,'paola.lombardi@client.it',NULL),(10,'Annuncio: incontro di revisione esami mercoledì ore 17','2026-02-18 16:00:00',1,'assistente.test@universita.it',NULL);
/*!40000 ALTER TABLE `pubblicazioni` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risorse_pubblicazioni`
--

DROP TABLE IF EXISTS `risorse_pubblicazioni`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `risorse_pubblicazioni` (
  `percorso` varchar(255) NOT NULL,
  PRIMARY KEY (`percorso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risorse_pubblicazioni`
--

LOCK TABLES `risorse_pubblicazioni` WRITE;
/*!40000 ALTER TABLE `risorse_pubblicazioni` DISABLE KEYS */;
/*!40000 ALTER TABLE `risorse_pubblicazioni` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `professori`
--

DROP TABLE IF EXISTS `professori`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `professori` (
  `email` varchar(255) NOT NULL,
  `nome` varchar(255) NOT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `professori`
--

LOCK TABLES `professori` WRITE;
/*!40000 ALTER TABLE `professori` DISABLE KEYS */;
INSERT INTO `professori` VALUES ('matteo.rinaldi@universita.it','Matteo Rinaldi'),('luca.bianchi@universita.it','Luca Bianchi'),('francesco.conti@universita.it','Francesco Conti'),('alessandro.deluca@universita.it','Alessandro De Luca'),('giovanni.ferri@universita.it','Giovanni Ferri'),('marco.romano@universita.it','Marco Romano'),('paolo.galli@universita.it','Paolo Galli'),('stefano.ricci@universita.it','Stefano Ricci'),('andrea.lombardi@universita.it','Andrea Lombardi'),('francesco.varani@università.it','Francesco Varani');
/*!40000 ALTER TABLE `professori` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `anni_accademici`
--

DROP TABLE IF EXISTS `anni_accademici`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `anni_accademici` (
  `anno` varchar(255) NOT NULL,
  PRIMARY KEY (`anno`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `anni_accademici`
--

LOCK TABLES `anni_accademici` WRITE;
/*!40000 ALTER TABLE `anni_accademici` DISABLE KEYS */;
INSERT INTO `anni_accademici` VALUES ('2024-2025'),('2025-2026');
/*!40000 ALTER TABLE `anni_accademici` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risorse_non_collegate`
--

DROP TABLE IF EXISTS `risorse_non_collegate`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `risorse_non_collegate` (
  `classe_id` int(11) NOT NULL,
  `percorso` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`percorso`),
  KEY `FKgot` (`classe_id`),
  KEY `FKupload` (`email`),
  CONSTRAINT `FKgot` FOREIGN KEY (`classe_id`) REFERENCES `classi` (`id`),
  CONSTRAINT `FKupload` FOREIGN KEY (`email`) REFERENCES `utenti` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risorse_non_collegate`
--

LOCK TABLES `risorse_non_collegate` WRITE;
/*!40000 ALTER TABLE `risorse_non_collegate` DISABLE KEYS */;
/*!40000 ALTER TABLE `risorse_non_collegate` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `utenti`
--

DROP TABLE IF EXISTS `utenti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `utenti` (
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nome_utente` varchar(255) NOT NULL,
  `amministratore` tinyint(1) NOT NULL,
  `attivo` tinyint(1) NOT NULL,
  `img_profilo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `utenti`
--

LOCK TABLES `utenti` WRITE;
/*!40000 ALTER TABLE `utenti` DISABLE KEYS */;
INSERT INTO `utenti` VALUES ('alessandro.moro@admin.it','$argon2id$v=19$m=65536,t=4,p=1$aWJHcDN4ZmlzdXE5b0k2Mg$ZC/VLrCXXC/BWTOgJeUDy/SoZCdBkV+8HjDG8JmDTyU','alessandro',1,1,NULL),('martina.rossi@admin.it','$argon2id$v=19$m=65536,t=4,p=1$VmNqakV0aHdST090bVg1ag$QytS2cxXwd5OnOsWSsjMrWSkygU9OWO8UAVrnbvetxA','martina',1,1,NULL),('filippo.argenti@client.it','$argon2id$v=19$m=65536,t=4,p=1$S2dIQkpOekU2UjdNYklwWA$Z749ryDmUf5CKXBqXFqom1TspXVy+TYM6+PddacqtWQ','filippo',0,1,NULL),('gianfranco.lippi@client.it','$argon2id$v=19$m=65536,t=4,p=1$ZXAzUWY1SXhxR0R5eWpHdQ$ESJyYTsbEqzHj39sW/buxoYgYfJYHtNd+zuD8JYrlwY','gianfranco',0,0,NULL),('andrea.neri@admin.it','$argon2id$v=19$m=65536,t=4,p=1$UjdjNzJQUS9DTU9NS0VpQw$49qrxw+bQASk9Hrt9cyzlx4ekLq+VVL7iJ4IJOwhchs','andrea',1,1,NULL),('maria.neri@admin.it','$argon2id$v=19$m=65536,t=4,p=1$VzFjSDJLd2Z2MExqZFFRQw$mCiXA0XPP7JlTHQQbP7cQkBFaj0TLjOgxKdzlHg/850','castani neri',1,0,NULL),('mario.rossi@client.it','$argon2id$v=19$m=65536,t=4,p=1$eHVGLlFzc3BlYVNaaXlTcw$8dGoNGg0xJQxK0mZk46iDNJyWdnW7hECiL+kIDjGL8Q','mario',0,1,NULL),('admin.test@local','$argon2id$v=19$m=65536,t=4,p=1$Um9lY0hpSU1tWE8zM09ieQ$oTxscJ/TQskkvqbiN4KAOs9L8vrZ4IZvQDHfrkp54IQ','admin_test',1,1,NULL),('giulia.verdi@client.it','$argon2id$v=19$m=65536,t=4,p=1$S2dIQkpOekU2UjdNYklwWA$Z749ryDmUf5CKXBqXFqom1TspXVy+TYM6+PddacqtWQ','giulia',0,1,NULL),('paola.lombardi@client.it','$argon2id$v=19$m=65536,t=4,p=1$S2dIQkpOekU2UjdNYklwWA$Z749ryDmUf5CKXBqXFqom1TspXVy+TYM6+PddacqtWQ','paola',0,1,NULL),('sara.federici@client.it','$argon2id$v=19$m=65536,t=4,p=1$S2dIQkpOekU2UjdNYklwWA$Z749ryDmUf5CKXBqXFqom1TspXVy+TYM6+PddacqtWQ','sara',0,1,NULL),('marco.neri@client.it','$argon2id$v=19$m=65536,t=4,p=1$S2dIQkpOekU2UjdNYklwWA$Z749ryDmUf5CKXBqXFqom1TspXVy+TYM6+PddacqtWQ','marco',0,1,NULL),('docente.test@universita.it','$argon2id$v=19$m=65536,t=4,p=1$Um9lY0hpSU1tWE8zM09ieQ$oTxscJ/TQskkvqbiN4KAOs9L8vrZ4IZvQDHfrkp54IQ','docente',1,1,NULL),('assistente.test@universita.it','$argon2id$v=19$m=65536,t=4,p=1$Um9lY0hpSU1tWE8zM09ieQ$oTxscJ/TQskkvqbiN4KAOs9L8vrZ4IZvQDHfrkp54IQ','assistente',1,1,NULL);
/*!40000 ALTER TABLE `utenti` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-24 19:29:33
