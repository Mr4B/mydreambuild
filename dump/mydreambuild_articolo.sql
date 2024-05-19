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
-- Table structure for table `articolo`
--

DROP TABLE IF EXISTS `articolo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articolo` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pubblicato` tinyint(1) DEFAULT '0',
  `data_pubblicazione` date DEFAULT NULL,
  `titolo` varchar(255) DEFAULT NULL,
  `summary` varchar(500) DEFAULT NULL,
  `testo` text,
  `id_redattore` varchar(255) DEFAULT NULL,
  `id_immagine` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_redattore` (`id_redattore`),
  KEY `id_immagine` (`id_immagine`),
  CONSTRAINT `articolo_ibfk_1` FOREIGN KEY (`id_redattore`) REFERENCES `utente` (`username`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `articolo_ibfk_2` FOREIGN KEY (`id_immagine`) REFERENCES `immagini` (`id_immagine`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articolo`
--

LOCK TABLES `articolo` WRITE;
/*!40000 ALTER TABLE `articolo` DISABLE KEYS */;
INSERT INTO `articolo` VALUES (3,1,'2024-05-17','Nvidia RTX 4090 due volte piu’ veloce della 3090','La RTX 4090 dopo gli ultimi leak, sembra essere potenzialmente due volte piu’ veloce di una RTX 3090','La futura top di gamma di casa Nvidia, secondo i leak da parte dell’utente di twitter kopite7kimi, includerà 126 multiprocessori di streaming, per un totale di 16128 core CUDA . È molto meno di quanto si dicesse in precedenza 140-142. Ricordiamo che la GPU AD102 completa ne ha 144, il che significa che 2304 core saranno disabilitati. Probabilmente i core disabilitati saranno disponibili nella futura RTX 4090 Ti.','kevin',8),(5,1,'2024-05-19','DDR4 vs DDR5 cosa cambia?','Quali sono le sostaziali differenze fra le due memorie e quale conviene comprare?','COSA ABBIAMO IN PIU’?\n\nCon il lancio dei processori Intel Alder Lake-S assisteremo all’introduzione dello standard DDR5 per le memorie RAM.\n\nMa cosa offrira’ in piu’ questo standard rispetto al vecchio DDR4?\n\nQuesto nuovo standard nasce con un obiettivo preciso: offrire all’utente un raddoppio della densita’ e della bandwidth rispetto alle DDR4 e allo stesso tempo una maggiore efficienza.\n\nIl perche’ di queste nuove RAM e’ semplice, sono nate per assecondare la richiesta sempre piu’ alta di bandwidth dei processori in commercio e futuri.\n\nInfatti con l’aumento dei core delle CPU assistiamo ad una maggiore richiesta di banda da parte dei processori.\n\n\nUna delle novita’ piu’ interessanti riguarda appunto la densita’ dei chip presenti sui banchi di memoria RAM. Passa dai 16Gbit a 64Gbit. Grazie al Die Stacking (Chip Stacking, Vertical Integration) ovvero la tecnologia che permette ad ogni singolo chip sul banco di memoria di avere 8 strati sovrapposti, potenzialmente potranno essere raggiunti i 2TB di capacita’ per singolo modulo.\n\n\n\nPIU’ EFFICIENTI!\n\nAltro cambiamento rispetto allo standard attuale DDR4 sara’ la gestione dell’energia. Poiche’ i chip che si occupano appunto di questo verranno spostati dalla scheda madre direttamente sul modulo RAM. Questo permettera’ di gestire in modo piu’ preciso la tensione e di garantire una minore rumorosita’ ed una maggiore integrita’ del segnale.\n\nIn termini di architettura il cambiamento nelle DDR5 consiste nel fatto che ogni DIMM ha due canali con un’ampiezza di 40 bit per ognuno di essi (32 bit per i dati e 8 bit di ECC) rispetto alle DDR4 che hanno un canale a 72 bit (64 bit per i dati e 8 bit ECC).\n\nQuesta differenza si traduce in una maggiore efficienza nell’accesso alla memoria.\n\nInoltre cambia non solo l’architettura del canale DIMM ma anche quella del modulo stesso. Sia il lato destro che sinistro del modulo hanno ora un canale indipendente a 40bit ciascuno con un RCD condiviso. Grazie a queste modifiche viene risolto il problema che si origina con la diminuzione della tensione di alimentazione ovvero un margine di rumore piu’ basso.\n\n\n\nNei moduli DDR5 inoltre il Burst Lenght, vale a dire la quantita’ di dati trasferiti tra la CPU e la RAM in ogni trasmissione. Passa da 8 a 16 e quindi permette di avere accesso a 64 byte di dati che in questo caso e’ la stessa quantita’ della cache delle attuali CPU.\n\nDIFFERENZE FISICHE\n\nFisicamente la differenza tra i moduli DDR4 e DDR5 nonostante mantengano entrambi i 288 pin e’ legata alla posizione del notch che quindi non permettera’ l’inserimento delle memorie DDR4 negli slot DDR5 e viceversa.\n\nInsomma queste nuove RAM promettono di offrire una maggiore banda e capienza ed una migliore gestione energetica.\n\nL’unico “svantaggio” al lancio, ovviamente oltre al prezzo, sara’ la latenza CL dei nuovi moduli. Troviamo, infatti in nuovi moduli con una latenza CL=40 che potrebbe portare inizialmente i moduli DDR4 di fascia alta a poter competere con i moduli DDR5 entry level. Ovviamente ci aspettiamo che la latenza come avvenuto per le precedenti generazioni migliorera’ nel tempo piu’ la tecnologia diventera’ matura.','kevin',10),(6,0,NULL,'ADATA SSD PCIe Gen5 M.2','ADATA mostra gli SSD PCIe Gen5 M.2 con velocita’ di scrittura fino a 14GB/s','Project Nighthawk e Project Blackbird, questi sono i nomi in codice dei nuovi SSD PCIe di ADATA. L’azienda li presentera’ il 5 gennaio 2022 durante l’evento del CES, assieme ad altri prodotti come memorie DDR5 e periferiche di gioco. Secondo il comunicato stampa di ADATA, questi SSD sono basati sull’interfaccia PCIe Gen5 x4 e sul protocollo NVMe 2.0, ed avranno una capacita’ massima di 8TB.\n\nIn quanto a velocità, ADATA riporta che l’opzione più veloce avrà fino a 14 GB/s di larghezza di banda di lettura sequenziale. Ma saranno previste anche opzioni piu’ “lente” da 12 GB/se 10 GB/s.\n \n\nQuesti nuovi storage offriranno quindi una velocità di lettura del 103% più veloce e una velocità di scrittura del 67% più veloce (7 GB/s) rispetto allo standard Gen4. MSI oltretutto ha gia’ dichiarato che ha in sviluppo un M.2 Expander basato sulla nuova interfaccia. In questo momento, nessuna piattaforma desktop consente la compatibilità diretta M.2 PCIe Gen5. Solo gli slot PCIe Gen5 x16 sulle schede madri Intel Z690 basate su LGA1700 supportano questa tecnologia, il che significa che lo storage ADATA PCIe Gen5 richiede al momento un adattatore PCIe a M.2 compatibile.','kevin',11);
/*!40000 ALTER TABLE `articolo` ENABLE KEYS */;
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
