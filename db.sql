-- Tabelle database yourpc

CREATE TABLE Utente (
    username VARCHAR(20) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    nome VARCHAR(255),
    cognome VARCHAR(255),
    email VARCHAR(255),
    ruolo INTEGER NOT NULL,
    FOREIGN KEY (ruolo) REFERENCES Ruolo(id) ON DELETE NO ACTION ON UPDATE CASCADE
);

CREATE TABLE Ruolo (
    id INTEGER PRIMARY KEY,
    denominazione VARCHAR(255) NOT NULL
);

-- [chiamata ajax che resituisce tutti gli articoli pubblicato = true e li mette in delle card, mostrando titolo e summary]
CREATE TABLE Articolo (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    pubblicato BOOLEAN NOT NULL,
    data_pubblicazione DATE DEFAULT NULL,
    titolo VARCHAR(255),
    summary VARCHAR(500),
    testo TEXT,
    id_redattore VARCHAR(255),
    FOREIGN KEY (id_redattore) REFERENCES Utente(id_redattore) ON DELETE SET NULL ON UPDATE CASCADE
);

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
    id INTEGER PRIMARY KEY AUTOINCREMENT, --autoincrement???
    denominazione VARCHAR(50),
    descrizione VARCHAR(500) DEFAULT 'La mia configurazione',
    id_utente VARCHAR(20),
    FOREIGN KEY (id_utente) REFERENCES Utente(username) ON DELETE CASCADE ON UPDATE CASCADE,
    --ci vanno tutti gli id dei componenti del pc (foreign key)
);

CREATE TABLE Lista (
    id INTEGER PRIMARY KEY AUTOINCREMENT, --autoincrement???
    denominazione VARCHAR(50),
    id_utente VARCHAR(20),
    FOREIGN KEY (id_utente) REFERENCES Utente(username) ON DELETE CASCADE ON UPDATE CASCADE
);

/* 
non so come farla 
una tabella per ogni componente? (lista_cpu, lista_ram, ecc...)
un id_componente che non è foreign key e quindi poi va a cercare nelle tabelle?

-- chiarire anche le immagini nel db o il percorso?

-- tabella socket?
*/

CREATE TABLE componenti_lista (
    id_lista INTEGER,
    id_componente VARCHAR(9),
    PRIMARY KEY(id_lista, id_componente),
    FOREIGN KEY (id_lista) REFERENCES Lista(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_componente) REFERENCES Utente(username) ON DELETE CASCADE ON UPDATE CASCADE
);

-- TUTTI I COMPONENTI DEVONO AVERE LA CHIAVE PRIMARIA DELLO STESSO TIPO

CREATE TABLE CPU (
    id VARCHAR(9) PRIMARY KEY, -- xxxxxxxxx 1° dice la marca (intel/amd),  2/3 il modello (ix/rx), 
    marca VARCHAR(10) NOT NULL,
    famiglia VARCHAR(10) NOT NULL, -- (core i3, Ryzen 7, ecc...)
    modello VARCHAR(10), -- (12400, ecc...)
    frequenza_base DECIMAL(10,2) NOT NULL,
    frequenza_boost DECIMAL(10,2),
    n_core INT NOT NULL,
    n_thread INT NOT NULL,
    consumo_energetico INT NOT NULL,
    dim_cache INT NOT NULL,
    socket VARCHAR(10) NOT NULL,
    link VARCHAR(255), --amazon
    descrizione TEXT
);

CREATE TABLE Scheda_madre (
    id_scheda_madre INT PRIMARY KEY AUTO_INCREMENT, --Da studiare poi
    modello VARCHAR(255) NOT NULL,
    formato VARCHAR(255) NOT NULL,
    socket VARCHAR(255) NOT NULL,
    chipset VARCHAR(255) NOT NULL,
    numero_slot_ram INT NOT NULL,
    tipologia_ram VARCHAR(255) NOT NULL,
    numero_slot_pcie INT NOT NULL,
    version_pcie VARCHAR(255) NOT NULL,
    link VARCHAR(255), --amazon
    descrizione TEXT

);

CREATE TABLE RAM (
    ram_id INT AUTO_INCREMENT PRIMARY KEY,
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
    cache_mb INT NOT NULL
    link VARCHAR(255), --amazon
    descrizione TEXT
);

CREATE TABLE SSD (
    id_ssd INT PRIMARY KEY AUTO_INCREMENT,
    modello VARCHAR(255) NOT NULL,
    capacita_tb INT NOT NULL,
    tipologia_ssd VARCHAR(255) NOT NULL,
    fattore_di_forma VARCHAR(255) NOT NULL, --m.2
    interfaccia VARCHAR(255) NOT NULL, --NVMe PCIe
    velocita_lettura_mb_s INT NOT NULL,
    velocita_scrittura_mb_s INT NOT NULL
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
    id_coolyng INT PRIMARY KEY AUTO_INCREMENT,
    marca VARCHAR(255) NOT NULL,
    modello VARCHAR(255) NOT NULL,
    tipologia INT, --1 liquido, 2 aria
    --compatibilita relazione nn con socket 
    link VARCHAR(255), --amazon
    descrizione TEXT,
);

CREATE TABLE Raffreddamento_Socket (
    id_coolyng INT,
    id_socket INT,
    PRIMARY KEY(id_coolyng, id_socket),
    FOREIGN KEY (id_coolyng) REFERENCES Raffreddamento(id_coolyng) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_socket) REFERENCES Socket(id_socket) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE Socket (
    id_socket INT PRIMARY KEY AUTO_INCREMENT,
    denominazione VARCHAR(255)
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