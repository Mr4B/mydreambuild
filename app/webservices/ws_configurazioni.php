<?php
include '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'db_connect.php';
include 'common' . DIRECTORY_SEPARATOR . 'auth.php';

$conn = getConnection();

// Recupera il metodo richiesto
$method = $_SERVER['REQUEST_METHOD'];

$headers = getallheaders();

$content_type = 'application/json';
$content_type_response = 'application/json';

// Gestione del token JWT
$gestioneJWT = new TokenJWT('ciao');
// Potrebbe generare errori
$token = $gestioneJWT->getJWT($headers);

if (!$gestioneJWT->validate($token)) {
    echo json_encode(['errore' => 'Token errato']);
    http_response_code(404);
    exit();
}

switch ($method) {
    case 'GET':
        // ---- metodo per fare più get filtrati ----
        // aggiunge un parametro alla richiesta url e poi in base al valore recuperato in uno switch scrive la query da eseguire

        // Recupera il valore del parametro "action" dall'URL
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $id_utente = isset($_GET['id_utente']) ? $_GET['id_utente'] : '';

        switch($action) {
            case 'get_configurazioni':
                // Ritorna tutte le configurazioni, per gestirle dai moderator
                getConfigurazioni();
                break;
            
            case 'get_defaultconfiguration':
                // Ritorna le configurazioni create dai moderator
                getDefaultConfiguration();
                break;

            case 'get_myconfiguration':
                // Ritorna le configurazioni create da un utente
                getMyConfiguration($id_utente);
                break;
            
            case 'get_tipologie':
                // Ritorna le configurazioni create da un utente
                getTipologie();
                break;

            default:
                echo json_encode(['errore' => 'Indirizzo errato']);
                http_response_code(404);
                break;
        }
        break;
    
    case 'POST':
        $token = $gestioneJWT->getJWT($headers);
        $userRole = $gestioneJWT->decode($token);
        
        if ($userRole->ruolo === '1' || $userRole->ruolo === '2' || $userRole->ruolo === '3') {
            // Solo un utente autorizzato può accedere a questo codice
            $payload = file_get_contents('php://input');


            // Trasformazione del payload nell'array che rappresenta il prodotto
            if ($content_type == 'application/json') {
                $data =  json_decode($payload,true);
            } else {
                echo json_encode(['errore' => 'Content-Type non ammesso']);
                http_response_code(400); //BAD REQUEST
                exit();
            }

            header("Content-Type: application/json; charset=UTF-8");

            // Recupera il valore del parametro "action" dall'URL
            $action = isset($_GET['action']) ? $_GET['action'] : '';
            

            switch($action) {
                case 'post_configurazione':
                    postConfigurazione($data);
                    break;

                case 'post_configurazione_mod':
                    modConfigurazione($data);
                    break;

                default:
                    throw new Exception("Azione non supportata");
                    break;
            }


        } else {
            echo json_encode(['errore' => 'Unauthorized']);
            http_response_code(401); // UNAUTHORIZED
        }
        break;

    default:
        echo json_encode(['errore' => 'Metodo non supportato']);
        http_response_code(405); // METHOD NOT ALLOWED
        break;
}

function postConfigurazione($data) {
    /*
    $data = {
        denominazione: string,
        descrizione: string,
        id_utente: string,
        prezzo_totale: decimal,
        prodotti: [2, 3, 50, 1, ...] // Ciclo qui e per ogni prodotto lo inserisce nella tabella n/n
    }
    */
    global $conn;
    
    // Avvio della transazione
    $conn->begin_transaction();

    try {
        // Query per inserire la configurazione
        $query = "INSERT INTO Configurazione (denominazione, descrizione, id_utente, prezzo_totale, tipologia) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssds", $data['denominazione'], $data['descrizione'], $data['id_utente'], $data['prezzo_totale'], $data['tipologia']);
        $stmt->execute();

        if ($stmt->affected_rows <= 0) {
            throw new Exception('Errore durante l\'inserimento della configurazione: ' . $conn->error);
        }

        // Ottenere l'ID della configurazione appena inserita
        $configurazione_id = $stmt->insert_id;

        // Query per inserire i prodotti nella tabella di relazione
        $query_prodotti = "INSERT INTO prodotti_configurazione (id_configurazione, id_prodotto) VALUES (?, ?)";
        $stmt_prodotti = $conn->prepare($query_prodotti);

        foreach ($data['prodotti'] as $prodotto_id) {
            $stmt_prodotti->bind_param("ii", $configurazione_id, $prodotto_id);
            $stmt_prodotti->execute();

            if ($stmt_prodotti->affected_rows <= 0) {
                throw new Exception('Errore durante l\'inserimento del prodotto ID ' . $prodotto_id . ': ' . $conn->error);
            }
        }

        // Completare la transazione
        $conn->commit();

        echo json_encode(["Success" => "Dati aggiunti con successo"]);
        http_response_code(200);

    } catch (Exception $e) {
        // Annullare la transazione in caso di errore
        $conn->rollback();
        echo json_encode(['errore' => $e->getMessage()]);
        http_response_code(400); // BAD REQUEST
    }
}


function getConfigurazioni() {
    global $conn;

    $query = "SELECT * FROM Configurazione ORDER BY id;";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(['errore' => 'Errore nella preparazione della query: ' . $conn->error]);
        http_response_code(500);
        exit();
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $lista = Array();

        while ($row = $result->fetch_assoc()) {
            $lista[] = $row;
        }

        // Impostazione del header field Content-Type
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($lista);
        http_response_code(200);
    } else {
        // Altrimenti, restituisci un errore con il codice di stato HTTP 401.
        echo json_encode(['errore' => 'Nessun articolo trovato']);
        http_response_code(404);
    }
    exit(); 
}

function getMyConfiguration($id_utente) {
    global $conn;
  
    $query = "SELECT * FROM Configurazione WHERE id_utente = ?;";
    $stmt = $conn->prepare($query);
  
    if (!$stmt) {
      echo json_encode(['errore' => 'Errore nella preparazione della query: ' . $conn->error]);
      http_response_code(500);
      exit();
    }
  
    $stmt->bind_param("s", $id_utente);
    $stmt->execute();
    $result = $stmt->get_result();
  
    $lista = [];
    if ($result->num_rows > 0) {
      while ($row = $result->fetch_assoc()) {
        $lista[] = $row;
      }
    }
  
    // Impostazione del header field Content-Type
    header("Content-Type: application/json; charset=UTF-8");
    echo json_encode($lista);
  
    // In ogni caso, il codice di stato HTTP deve essere 200
    http_response_code(200);
  
    exit();
  }
  

function getDefaultConfiguration() {
    global $conn;

    $query = "SELECT c.*
                FROM Configurazione AS c
                JOIN Utente AS u ON c.id_utente = u.username
                WHERE u.ruolo IN (1, 2);
                ";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(['errore' => 'Errore nella preparazione della query: ' . $conn->error]);
        http_response_code(500);
        exit();
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $lista = Array();

        while ($row = $result->fetch_assoc()) {
            $lista[] = $row;
        }

        // Impostazione del header field Content-Type
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($lista);
        http_response_code(200);
    } else {
        // Altrimenti, restituisci un errore con il codice di stato HTTP 401.
        echo json_encode(['errore' => 'Nessun articolo trovato']);
        http_response_code(404);
    }
    exit(); 
}

function getTipologie() {
    global $conn;

    $query = "SELECT *
                FROM Tipologia;
                ";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(['errore' => 'Errore nella preparazione della query: ' . $conn->error]);
        http_response_code(500);
        exit();
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $lista = Array();

        while ($row = $result->fetch_assoc()) {
            $lista[] = $row;
        }

        // Impostazione del header field Content-Type
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($lista);
        http_response_code(200);
    } else {
        // Altrimenti, restituisci un errore con il codice di stato HTTP 401.
        echo json_encode(['errore' => 'Nessun articolo trovato']);
        http_response_code(404);
    }
    exit(); 
}

function modConfigurazione($data) {
    global $conn;
    
    // Avvio della transazione
    $conn->begin_transaction();

    try {
        // Query per inserire la configurazione
        $query = "INSERT INTO Configurazione (denominazione, descrizione, id_utente, prezzo_totale, tipologia, id_immagine) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssdsi", $data['denominazione'], $data['descrizione'], $data['id_utente'], $data['prezzo_totale'], $data['tipologia'], $data['id_immagine']);
        $stmt->execute();

        if ($stmt->affected_rows <= 0) {
            throw new Exception('Errore durante l\'inserimento della configurazione: ' . $conn->error);
        }

        // Ottenere l'ID della configurazione appena inserita
        $configurazione_id = $stmt->insert_id;

        // Query per inserire i prodotti nella tabella di relazione
        $query_prodotti = "INSERT INTO prodotti_configurazione (id_configurazione, id_prodotto) VALUES (?, ?)";
        $stmt_prodotti = $conn->prepare($query_prodotti);

        foreach ($data['prodotti'] as $prodotto_id) {
            $stmt_prodotti->bind_param("ii", $configurazione_id, $prodotto_id);
            $stmt_prodotti->execute();

            if ($stmt_prodotti->affected_rows <= 0) {
                throw new Exception('Errore durante l\'inserimento del prodotto ID ' . $prodotto_id . ': ' . $conn->error);
            }
        }

        // Completare la transazione
        $conn->commit();

        echo json_encode(["Success" => "Dati aggiunti con successo"]);
        http_response_code(200);

    } catch (Exception $e) {
        // Annullare la transazione in caso di errore
        $conn->rollback();
        echo json_encode(['errore' => $e->getMessage()]);
        http_response_code(400); // BAD REQUEST
    }
}