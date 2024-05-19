-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: localhost    Database: mydreambuild
-- ------------------------------------------------------
-- Server version	8.0.33

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
-- Table structure for table `prodotto`
--

DROP TABLE IF EXISTS `prodotto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `prodotto` (
  `id_prodotto` int NOT NULL AUTO_INCREMENT,
  `id_categoria` int NOT NULL,
  `marca` varchar(255) NOT NULL,
  `modello` varchar(255) NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `descrizione` text,
  `prezzo` decimal(10,2) NOT NULL,
  `frequenza_base` decimal(10,2) DEFAULT NULL,
  `c_frequenza_boost` decimal(10,2) DEFAULT NULL,
  `c_n_core` int DEFAULT NULL,
  `c_n_thread` int DEFAULT NULL,
  `c_consumo_energetico` int DEFAULT NULL,
  `c_dim_cache` int DEFAULT NULL,
  `g_memoria` int DEFAULT NULL,
  `g_tipo_memoria` varchar(255) DEFAULT NULL,
  `m_formato` varchar(255) DEFAULT NULL,
  `m_chipset` varchar(255) DEFAULT NULL,
  `m_numero_slot_ram` int DEFAULT NULL,
  `m_tipologia_ram` varchar(255) DEFAULT NULL,
  `m_numero_slot_pcie` int DEFAULT NULL,
  `m_version_pcie` varchar(255) DEFAULT NULL,
  `r_dimensione` int DEFAULT NULL,
  `r_velocita` int DEFAULT NULL,
  `r_tipo` varchar(50) DEFAULT NULL,
  `a_tipo_archiviazione` varchar(255) DEFAULT NULL,
  `capacita_gb` int DEFAULT NULL,
  `fattore_di_forma` varchar(255) DEFAULT NULL,
  `a_velocita_rotazione` int DEFAULT NULL,
  `a_cache_mb` int DEFAULT NULL,
  `a_interfaccia` varchar(255) DEFAULT NULL,
  `a_velocita_lettura_mb_s` int DEFAULT NULL,
  `a_velocita_scrittura_mb_s` int DEFAULT NULL,
  `p_watt` int DEFAULT NULL,
  `p_schema_alimentazione` varchar(255) DEFAULT NULL,
  `cs_colore` varchar(255) DEFAULT NULL,
  `cs_peso` int DEFAULT NULL,
  `dimensioni` varchar(255) DEFAULT NULL,
  `cs_finestra_laterale` tinyint(1) DEFAULT NULL,
  `tipo_cooling` int DEFAULT NULL,
  `id_immagine` int DEFAULT NULL,
  PRIMARY KEY (`id_prodotto`),
  KEY `id_immagine` (`id_immagine`),
  KEY `id_categoria` (`id_categoria`),
  CONSTRAINT `prodotto_ibfk_1` FOREIGN KEY (`id_immagine`) REFERENCES `immagini` (`id_immagine`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `prodotto_ibfk_2` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `prodotto`
--

LOCK TABLES `prodotto` WRITE;
/*!40000 ALTER TABLE `prodotto` DISABLE KEYS */;
INSERT INTO `prodotto` VALUES (6,1,'AMD','Raedon 7','napoli.com/raedon7','Fa schifo',12.00,0.10,1.20,1,100,104,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(12,1,'Intel','Core I5','https://amzn.eu/d/gYDcUWA','L\'Intel Core i5-12400F è un processore desktop di 12a generazione basato sull\'architettura Alder Lake. Offre un eccellente rapporto qualità-prezzo, rendendolo una scelta popolare per i gamer e gli utenti che desiderano prestazioni elevate a un prezzo accessibile.',134.00,2.50,4.40,6,12,65,18,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6);
/*!40000 ALTER TABLE `prodotto` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-05-19 23:01:17
