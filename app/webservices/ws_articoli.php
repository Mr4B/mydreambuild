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

        $cat = null;

        switch($action) {
            case 'get_articoli':
                // Ritorna i 4 articoli più recenti (funziona)
                getArticoli();
                break;

            case 'get_carosello':
                // Ritorna i 4 articoli più recenti (funziona)
                getCarosello();
                break;
            
            case 'get_card':
                // Ritorna un tot di articoli per generare le card nella home page
                break;

            case 'get_articolo':
                getArticolo($id);
                break;

            default:
                echo json_encode(['errore' => 'Indirizzo errato']);
                http_response_code(404);
                break;
        }

        $stmt = $conn->prepare($query);
        
        // Esecuzione query
        $stmt->execute();

        // Recupero dei risultati
        $result = $stmt->get_result();

        $lista = Array();

        while ($row = $result->fetch_assoc()) {
            $lista[] = $row;
        }

        // Impostazione del header field Content-Type
        header("Content-Type: application/json; charset=UTF-8");
        echo json_encode($lista);

        http_response_code(200);
        break;
    
    case 'POST':
        $token = $gestioneJWT->getJWT($headers);
        $userRole = $gestioneJWT->decode($token);
        
        if ($userRole->ruolo === '1' || $userRole->ruolo === '2') {
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
                case 'insert_articolo':
                    $pubblicato = $data['pubblicato'] ? 1 : 0;
                    $query = "INSERT INTO Articolo (pubblicato, data_pubblicazione, titolo, summary, testo, id_redattore, id_immagine) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("isssssi", $pubblicato, $data['data_pubblicazione'], $data['titolo'], $data['summary'], $data['corpo'], $data['redattore'], $data['id_immagine']);
                    // I Boolean vanno interpretati come 1 o 0
                    break;

                case 'post_':
                    $query = "";
                    break;

                default:
                    throw new Exception("Azione non supportata");
                    break;
            }

            $stmt->execute();
            // $conn->commit();
            
            if ($stmt->affected_rows > 0){
                echo json_encode(["Success" => "Dati aggiunti con successo"]);
                http_response_code(200);
                exit();
            }else {
                echo json_encode(['errore' => $conn->error]);
                http_response_code(400); //BAD REQUEST
                exit();
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


function getCarosello() {
    global $conn;

    $query = "SELECT * FROM Articolo WHERE pubblicato = true ORDER BY data_pubblicazione DESC LIMIT 4;";
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
    exit(); // Termina lo script dopo il login
}


function getArticoli() {
    global $conn;

    $query = "SELECT * FROM Articolo ORDER BY data_pubblicazione DESC;";
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
    exit(); // Termina lo script dopo il login
}

function getArticolo($id) {
    global $conn;

    if($id != '') {
        $query = "SELECT * FROM Articolo WHERE id = ?;";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id);


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
            http_response_code(401);
        }
    } else {
        echo json_encode(['errore' => 'ID errato']);
        http_response_code(404);
    }
    exit(); // Termina lo script dopo il login

}