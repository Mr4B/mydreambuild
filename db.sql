-- Tabelle database mydreambuild
/* 
    dump e restore: https://phoenixnap.com/kb/how-to-backup-restore-a-mysql-database
*/

CREATE DATABASE IF NOT EXISTS mydreambuild;
USE mydreambuild;

CREATE TABLE IF NOT EXISTS Ruolo (
    id INTEGER PRIMARY KEY,
    denominazione VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS Utente (
    username VARCHAR(255) PRIMARY KEY,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    nome VARCHAR(255),
    cognome VARCHAR(255),
    ruolo INTEGER NOT NULL,
    FOREIGN KEY (ruolo) REFERENCES Ruolo(id) ON DELETE NO ACTION ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Immagini (
    id_immagine INT AUTO_INCREMENT PRIMARY KEY,
    titolo VARCHAR(255), -- posso salvare con main quelle principali e poi farci una query
    dimensioni INT,
    immagine LONGBLOB,
    tipo VARCHAR(45) -- jpeg, png, ecc..
);

CREATE TABLE IF NOT EXISTS Articolo (
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

CREATE TABLE IF NOT EXISTS Tipologia (
    denominazione VARCHAR(255) PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS Configurazione (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    denominazione VARCHAR(255),
    descrizione TEXT,
    id_utente VARCHAR(255),
    prezzo_totale DECIMAL(10,2) NOT NULL,
    tipologia VARCHAR(255),
    id_immagine INT,
    FOREIGN KEY (id_utente) REFERENCES Utente(username) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (tipologia) REFERENCES Tipologia(denominazione) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_immagine) REFERENCES Immagini(id_immagine) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS Categoria (
    id INT PRIMARY KEY AUTO_INCREMENT,
    definizione VARCHAR(255) NOT NULL 
);

CREATE TABLE IF NOT EXISTS Prodotto (
    id_prodotto INT PRIMARY KEY AUTO_INCREMENT, -- studiarsi una primarykey fatta bene che mi aiuti nella ricerca
    id_categoria INT NOT NULL,
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
    socket VARCHAR(255), -- anche CPU
    -- gpu
    g_memoria INT,
    g_tipo_memoria VARCHAR(255), -- GGDR6
    -- motherboard
    m_formato VARCHAR(255),
    m_chipset VARCHAR(255),
    m_numero_slot_ram INT,
    m_tipologia_ram VARCHAR(255),
    m_version_pcie VARCHAR(255),
    -- ram
    r_dimensione INT,
    r_velocita INT, -- MHz
    r_tipo VARCHAR(50), -- ddrx
    -- archiviazione
    capacita_gb INT,
    fattore_di_forma VARCHAR(255), -- 3,5 pollici, m.2, ecc. ANCHE PER LA PSU E CASE (atx)
    a_velocita_rotazione INT,
    a_interfaccia VARCHAR(255), -- NVMe PCIe gestiti da client solo ssd
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
);

CREATE TABLE IF NOT EXISTS prodotti_configurazione (
    id_configurazione INT,
    id_prodotto INT,
    PRIMARY KEY(id_configurazione, id_prodotto),
    FOREIGN KEY (id_configurazione) REFERENCES Configurazione(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_prodotto) REFERENCES Prodotto(id_prodotto) ON DELETE CASCADE ON UPDATE CASCADE
);



/* 
    -- Tabelle future --

    -- Non gestiti
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
*/


/* 
    -- Query utili

    INSERT INTO Categoria (definizione) VALUES ('RAM');
    INSERT INTO Categoria (definizione) VALUES ('GPU');
    INSERT INTO Categoria (definizione) VALUES ('CPU');
    INSERT INTO Categoria (definizione) VALUES ('Scheda Madre');
    INSERT INTO Categoria (definizione) VALUES ('PSU');
    INSERT INTO Categoria (definizione) VALUES ('HDD');
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



    ALTER TABLE Configurazione
    ADD CONSTRAINT fk_tipologia
    FOREIGN KEY (tipologia) REFERENCES Tipologia(denominazione)
    ON DELETE CASCADE;

    ALTER TABLE `mydreambuild`.`configurazione` 
    ADD COLUMN `id_immagine` INT NULL AFTER `tipologia`,
    ADD INDEX `configurazione_ibfk_3_idx` (`id_immagine` ASC) VISIBLE;
    ;
    ALTER TABLE `mydreambuild`.`configurazione` 
    ADD CONSTRAINT `configurazione_ibfk_3`
    FOREIGN KEY (`id_immagine`)
    REFERENCES `mydreambuild`.`immagini` (`id_immagine`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;


    INSERT INTO `mydreambuild`.`articolo` (`data_pubblicazione`, `titolo`, `summary`, `testo`, `id_redattore`) VALUES ('2024/05/16', 'Nvidia RTX 4090 due volte piu’ veloce della 3090', 'La RTX 4090 dopo gli ultimi leak, sembra essere potenzialmente due volte piu’ veloce di una RTX 3090', 'La futura top di gamma di casa Nvidia, secondo i leak da parte dell’utente di twitter kopite7kimi, includerà 126 multiprocessori di streaming, per un totale di 16128 core CUDA . È molto meno di quanto si dicesse in precedenza 140-142. Ricordiamo che la GPU AD102 completa ne ha 144, il che significa che 2304 core saranno disabilitati. Probabilmente i core disabilitati saranno disponibili nella futura RTX 4090 Ti.', 'kevin');
    INSERT INTO `mydreambuild`.`articolo` (`pubblicato`, `data_pubblicazione`, `titolo`, `summary`, `testo`, `id_redattore`) VALUES ('1', '2024-05-01', 'Leak: RTX 3070Ti 16GB', 'Pare sia in arrivo una nuova 3070Ti con il doppio della memoria', 'Come riportato anche da Videocardz.com, Nvidia ha pronta una nuova versione della RTX 3070Ti con 16GB di memoria. Questa notizia sembra essere confermata dal fatto che due case produttrici di schede video, ASUS e Gigabyte, hanno presentato all’ufficio di regolamentazione della commissione economica euroasiatica, i nuovi modelli di RTX 3070Ti 16GB. Si era gia’ parlato di una possibile data di uscita di questa scheda per l’11 gennaio, ma ovviamente e’ stata posticipata. In realta’ una 3070Ti 16GB era gia’ stata presentata da parte di Gigabyte alla EEC il dicembre scorso. Questo nuovo leak almeno per Gigabyte si reiferisce quindi ad un aggiornamento di nuovi modelli di 3070Ti 16GB.', 'kevin');

    INSERT INTO Ruolo (id, denominazione) VALUES
    (1, 'admin'),
    (2, 'moderator'),
    (3, 'user'),
    (4, 'guest');
*/