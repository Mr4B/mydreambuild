-- Tabelle database mydreambuild
/* 
    hostato nel sito "000webhost.com"
*/

CREATE TABLE Utente (
    username VARCHAR(255) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    nome VARCHAR(255),
    cognome VARCHAR(255),
    ruolo INTEGER NOT NULL,
    FOREIGN KEY (ruolo) REFERENCES Ruolo(id) ON DELETE NO ACTION ON UPDATE CASCADE
);

CREATE TABLE Ruolo (
    id INTEGER PRIMARY KEY,
    denominazione VARCHAR(255) NOT NULL
);

INSERT INTO Ruolo (id, denominazione) VALUES
(1, 'admin'),
(2, 'moderator'),
(3, 'user'),
(4, 'guest');


-- [chiamata ajax che resituisce tutti gli articoli pubblicato = true e li mette in delle card, mostrando titolo e summary]
CREATE TABLE Articolo (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    pubblicato BOOLEAN DEFAULT FALSE,
    data_pubblicazione DATE DEFAULT NULL,
    titolo VARCHAR(255),
    summary VARCHAR(500),
    testo TEXT,
    id_redattore VARCHAR(255),
    id_immagine INT,
    FOREIGN KEY (id_redattore) REFERENCES Utente(username) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (id_immagine) REFERENCES Immagini(id_immagine) ON DELETE SET NULL ON UPDATE CASCADE
);

INSERT INTO `mydreambuild`.`articolo` (`data_pubblicazione`, `titolo`, `summary`, `testo`, `id_redattore`) VALUES ('2024/05/16', 'Nvidia RTX 4090 due volte piu’ veloce della 3090', 'La RTX 4090 dopo gli ultimi leak, sembra essere potenzialmente due volte piu’ veloce di una RTX 3090', 'La futura top di gamma di casa Nvidia, secondo i leak da parte dell’utente di twitter kopite7kimi, includerà 126 multiprocessori di streaming, per un totale di 16128 core CUDA . È molto meno di quanto si dicesse in precedenza 140-142. Ricordiamo che la GPU AD102 completa ne ha 144, il che significa che 2304 core saranno disabilitati. Probabilmente i core disabilitati saranno disponibili nella futura RTX 4090 Ti.', 'kevin');
INSERT INTO `mydreambuild`.`articolo` (`pubblicato`, `data_pubblicazione`, `titolo`, `summary`, `testo`, `id_redattore`) VALUES ('1', '2024-05-01', 'Leak: RTX 3070Ti 16GB', 'Pare sia in arrivo una nuova 3070Ti con il doppio della memoria', 'Come riportato anche da Videocardz.com, Nvidia ha pronta una nuova versione della RTX 3070Ti con 16GB di memoria. Questa notizia sembra essere confermata dal fatto che due case produttrici di schede video, ASUS e Gigabyte, hanno presentato all’ufficio di regolamentazione della commissione economica euroasiatica, i nuovi modelli di RTX 3070Ti 16GB. Si era gia’ parlato di una possibile data di uscita di questa scheda per l’11 gennaio, ma ovviamente e’ stata posticipata. In realta’ una 3070Ti 16GB era gia’ stata presentata da parte di Gigabyte alla EEC il dicembre scorso. Questo nuovo leak almeno per Gigabyte si reiferisce quindi ad un aggiornamento di nuovi modelli di 3070Ti 16GB.', 'kevin');


CREATE TABLE articolo_tag (
    id_articolo INTEGER,
    id_tag VARCHAR(15),
    PRIMARY KEY (id_articolo, id_zonainteresse),
    FOREIGN KEY (id_articolo) REFERENCES Articolo(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_tag) REFERENCES Tag(tag) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Tag (
    tag VARCHAR(15),
    descrizione VARCHAR(500) DEFAULT NULL
);

-- relazione n/n fra i componenti e il tag a loro associato
CREATE TABLE componenti_tag (

);

CREATE TABLE Configurazione (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    denominazione VARCHAR(50),
    descrizione TEXT ,
    id_utente VARCHAR(255),
    prezzo_totale DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_utente) REFERENCES Utente(username) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE prodotti_configurazione (
    id_configurazione INT,
    id_prodotto VARCHAR(255),
    PRIMARY KEY(id_configurazione, id_prodotto),
    FOREIGN KEY (id_configurazione) REFERENCES Configurazione(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_prodotto) REFERENCES Prodotto(id_prodotto) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Lista (
    id INTEGER PRIMARY KEY AUTO_INCREMENT, -- composto da prima parte di email e numero lista
    denominazione VARCHAR(50),
    id_utente VARCHAR(20),
    FOREIGN KEY (id_utente) REFERENCES Utente(username) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE articoli_lista (
    id_lista INTEGER,
    id_prodotto VARCHAR(9),
    PRIMARY KEY(id_lista, id_prodotto),
    FOREIGN KEY (id_lista) REFERENCES Lista(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_prodotto) REFERENCES Prodotto(id_prodotto) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Le GPUUUUUU!!!!!!

CREATE TABLE Prodotto (
    id_prodotto INT PRIMARY KEY AUTO_INCREMENT, -- studiarsi una primarykey fatta bene che mi aiuti nella ricerca
    id_categoria INT NOT NULL, -- foreign key alla tabella che mi definisce la categoria (cpu, psu, ram, ecc.)
    marca VARCHAR(255) NOT NULL,
    modello VARCHAR(255) NOT NULL,
    link VARCHAR(255), -- amazon
    descrizione TEXT,
    prezzo DECIMAL(10,2) NOT NULL,
-- cpu
    frequenza_base DECIMAL(10,2), -- Anche GPU
    c_frequenza_boost DECIMAL(10,2),
    c_n_core INT,
    c_n_thread INT,
    c_consumo_energetico INT,
    c_dim_cache INT,
-- gpu
    g_memoria INT,
    g_tipo_memoria VARCHAR(255),
-- motherboard
    m_formato VARCHAR(255),
    m_chipset VARCHAR(255),
    m_numero_slot_ram INT,
    m_tipologia_ram VARCHAR(255),
    m_numero_slot_pcie INT, -- elimina
    m_version_pcie VARCHAR(255),
-- ram
    r_dimensione INT,
    r_velocita INT, -- MHz
    r_tipo VARCHAR(50), -- ddrx
-- archiviazione
    a_tipo_archiviazione VARCHAR(255),
    capacita_gb INT, -- Anche GPU
    fattore_di_forma VARCHAR(255), -- 3,5 pollici, m.2, ecc. ANCHE PER LA PSU E CASE (atx)
    a_velocita_rotazione INT,
    a_cache_mb INT,
    a_interfaccia VARCHAR(255), -- NVMe PCIe, sata 6 0 gestiti da client
    a_velocita_lettura_mb_s INT,
    a_velocita_scrittura_mb_s INT,
-- psu
    p_watt INT,
    p_schema_alimentazione VARCHAR(255), -- modulare, semi-modulare, ...
-- Case
    cs_colore VARCHAR(255),
    cs_peso INT,
    dimensioni VARCHAR(255), -- GPU
    cs_finestra_laterale BOOLEAN,
-- cooling
    tipo_cooling INT, -- 1 liquido, 2 aria
    id_immagine INT,
    FOREIGN KEY (id_immagine) REFERENCES Immagini(id_immagine) ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES Categoria(id) ON DELETE CASCADE ON UPDATE CASCADE
    -- i socket sono gestiti nella relazione n/n
);

ALTER TABLE `mydreambuild`.`prodotto` 
CHANGE COLUMN `id_prodotto` `id_prodotto` INT NOT NULL AUTO_INCREMENT ;


CREATE TABLE Categoria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    definizione VARCHAR(255) NOT NULL 
);

/* INSERT INTO Categoria (definizione) VALUES ('RAM');
INSERT INTO Categoria (definizione) VALUES ('GPU');
INSERT INTO Categoria (definizione) VALUES ('CPU');
INSERT INTO Categoria (definizione) VALUES ('Scheda Madre');
INSERT INTO Categoria (definizione) VALUES ('Alimentatore');
INSERT INTO Categoria (definizione) VALUES ('Hard Disk');
INSERT INTO Categoria (definizione) VALUES ('SSD');
INSERT INTO Categoria (definizione) VALUES ('Dissipatore');
INSERT INTO Categoria (definizione) VALUES ('Case');
INSERT INTO Categoria (definizione) VALUES ('Scheda Audio');
INSERT INTO Categoria (definizione) VALUES ('Scheda di Rete');
INSERT INTO Categoria (definizione) VALUES ('Lettore DVD/Blu-ray');
INSERT INTO Categoria (definizione) VALUES ('Monitor');
INSERT INTO Categoria (definizione) VALUES ('Tastiera');
INSERT INTO Categoria (definizione) VALUES ('Mouse');
INSERT INTO Categoria (definizione) VALUES ('Ventole');
 */

CREATE TABLE prodotto_Socket ( -- tabella nn per collegare i socket compatibili con un prodotto
    id_prodotto INT,
    id_socket INT,
    PRIMARY KEY(id_prodotto, id_socket),
    FOREIGN KEY (id_prodotto) REFERENCES Prodotto(id_prodotto) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_socket) REFERENCES Socket(id_socket) ON DELETE CASCADE ON UPDATE CASCADE
);

ALTER TABLE `mydreambuild`.`prodotto_socket` 
DROP FOREIGN KEY `prodotto_socket_fkprodotto`;
ALTER TABLE `mydreambuild`.`prodotto_socket` 
CHANGE COLUMN `id_prodotto` `id_prodotto` INT NOT NULL ;
ALTER TABLE `mydreambuild`.`prodotto_socket` 
ADD CONSTRAINT `prodotto_socket_fkprodotto`
  FOREIGN KEY (`id_prodotto`)
  REFERENCES `mydreambuild`.`prodotto` (`id_prodotto`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;


CREATE TABLE Socket (
    id_socket INT PRIMARY KEY AUTO_INCREMENT,
    denominazione VARCHAR(255)
);

CREATE TABLE Immagini (
    id_immagine INT AUTO_INCREMENT PRIMARY KEY,
    titolo VARCHAR(255), -- posso salvare con main quelle principali e poi farci una query
    dimensioni INT,
    immagine BLOB,
    tipo VARCHAR(45) -- jpeg, png, ecc..
);

ALTER TABLE `mydreambuild`.`immagini` 
CHANGE COLUMN `dimensioni` `dimensioni` INT NULL DEFAULT NULL ;



/*

CREATE TABLE CPU (
    id_cpu INT PRIMARY KEY AUTO_INCREMENT  --VARCHAR(9) PRIMARY KEY, -- xxxxxxxxx 1° dice la marca (intel/amd),  2/3 il modello (ix/rx), 
    marca VARCHAR(10) NOT NULL,
    famiglia VARCHAR(10) NOT NULL, -- (core i3, Ryzen 7, ecc...)
    modello VARCHAR(10), -- (12400, ecc...)
    frequenza_base DECIMAL(10,2) NOT NULL,
    frequenza_boost DECIMAL(10,2),
    n_core INT NOT NULL,
    n_thread INT NOT NULL,
    consumo_energetico INT NOT NULL,
    dim_cache INT NOT NULL,
    id_socket INT NOT NULL,
    link VARCHAR(255), --amazon
    descrizione TEXT,
    prezzo DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_socket) REFERENCES Socket(id_socket) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Scheda_madre (
    id_motherboard INT PRIMARY KEY AUTO_INCREMENT, --Da studiare poi
    modello VARCHAR(255) NOT NULL,
    formato VARCHAR(255) NOT NULL,
    id_socket INT NOT NULL,
    chipset VARCHAR(255) NOT NULL,
    numero_slot_ram INT NOT NULL,
    tipologia_ram VARCHAR(255) NOT NULL,
    numero_slot_pcie INT NOT NULL,
    version_pcie VARCHAR(255) NOT NULL,
    link VARCHAR(255), --amazon
    descrizione TEXT,
    prezzo DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_socket) REFERENCES Socket(id_socket) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE RAM (
    id_ram INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(255),
    modello VARCHAR(255),
    dimensione INT,
    velocita INT, --MHz
    tipo VARCHAR(50), --ddrx
    link VARCHAR(255), --amazon
    descrizione TEXT
);

CREATE TABLE HDD (
    id_hdd INT PRIMARY KEY AUTO_INCREMENT,
    marca VARCHAR(255),
    modello VARCHAR(255) NOT NULL,
    capacita_gb INT NOT NULL,
    fattore_di_forma VARCHAR(255) NOT NULL, --3,5 pollici
    velocita_rotazione INT NOT NULL,
    tipologia_interfaccia VARCHAR(255) NOT NULL, --sata 6 0 gb
    cache_mb INT NOT NULL,
    prezzo DECIMAL(10,2) NOT NULL,
    link VARCHAR(255), --amazon
    descrizione TEXT
);

CREATE TABLE SSD (
    id_ssd INT PRIMARY KEY AUTO_INCREMENT,
    modello VARCHAR(255) NOT NULL,
    capacita_tb INT NOT NULL,
    tipologia_ssd VARCHAR(255) NOT NULL,
    fattore_di_forma VARCHAR(255) NOT NULL, --m.2
    interfaccia VARCHAR(255) NOT NULL, --NVMe PCIe, gestiti da client
    velocita_lettura_mb_s INT NOT NULL,
    velocita_scrittura_mb_s INT NOT NULL,
    prezzo DECIMAL(10,2) NOT NULL,
    link VARCHAR(255), --amazon
    descrizione TEXT
);

CREATE TABLE PSU (
    id_psu INT PRIMARY KEY AUTO_INCREMENT,
    marca VARCHAR(255) NOT NULL,
    modello VARCHAR(255) NOT NULL,
    fattore_di_forma VARCHAR(255) NOT NULL, --ATX
    watt INT,
    schema_alimentazione VARCHAR(255) NOT NULL, --modulare, semi-modulare, ...
    prezzo DECIMAL(10,2) NOT NULL,
    link VARCHAR(255), --amazon
    descrizione TEXT
);

CREATE TABLE Case (
    id_case INT PRIMARY KEY AUTO_INCREMENT,
    marca VARCHAR(255),
    modello VARCHAR(255),
    categoria INT NOT NULL, --atx, micro-atx, ecc..
    colore VARCHAR(255),
    link VARCHAR(255), --amazon
    descrizione TEXT,
    pesi INT,
    dimensioni VARCHAR(255),
    finestra_laterale BOOLEAN,
    prezzo DECIMAL(10,2) NOT NULL
);

CREATE TABLE Raffreddamento (
    id_cooling INT PRIMARY KEY AUTO_INCREMENT,
    marca VARCHAR(255) NOT NULL,
    modello VARCHAR(255) NOT NULL,
    tipologia INT, --1 liquido, 2 aria
    prezzo DECIMAL(10,2) NOT NULL,
    link VARCHAR(255), --amazon
    descrizione TEXT,
);

CREATE TABLE Monitor (
    id_monitor INT PRIMARY KEY AUTO_INCREMENT,
    modello VARCHAR(255) NOT NULL,
    marca VARCHAR(255) NOT NULL,
    dimensione_schermo DECIMAL(10,2) NOT NULL,
    risoluzione VARCHAR(255) NOT NULL,
    proporzioni VARCHAR(255) NOT NULL,
    tipo_display VARCHAR(255) NOT NULL, -- IPS, FULL HD
    connettori VARCHAR(255) NOT NULL, --HDMI, VGA, ecc..
    frequenza_di_aggiornamento INT NOT NULL,
    tempo_di_risposta DECIMAL(10,2) NOT NULL,
    autoparlanti BOOLEAN,
    prezzo DECIMAL(10,2) NOT NULL,
    link_acquisto VARCHAR(255),
    descrizione TEXT
);

CREATE TABLE Periferiche (

);

-- per tipologia periferiche è inteso: mouse, tastiera e telecamera
CREATE TABLE tipologia_periferiche (

);
*/