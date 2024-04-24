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
*/

CREATE TABLE componenti_lista (
    id_lista INTEGER,
    id_componente VARCHAR(9),
    FOREIGN KEY (id_lista) REFERENCES Lista(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (id_componente) REFERENCES Utente(username) ON DELETE CASCADE ON UPDATE CASCADE
);

-- TUTTI I COMPONENTI DEVONO AVERE LA CHIAVE PRIMARIA DELLO STESSO TIPO

CREATE TABLE CPU (

);

CREATE TABLE Scheda_madre (

);

CREATE TABLE RAM (

);

CREATE TABLE HDD (

);

CREATE TABLE SSD (

);

CREATE TABLE ssd_type (

);

CREATE TABLE Alimentatore (

);

CREATE TABLE Case (

);

CREATE TABLE Raffreddamento (

);

CREATE TABLE coolyng_type (

);

CREATE TABLE Ventole (

);

CREATE TABLE Monitor (

);

CREATE TABLE Periferiche (

);

-- per tipologia periferiche è inteso: mouse, tastiera e telecamera
CREATE TABLE tipologia_periferiche (

);