<?php
include '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'db_connect.php';
include 'common' . DIRECTORY_SEPARATOR . 'auth.php';

$conn = getConnection();

// Recupera il metodo richiesto
$method = $_SERVER['REQUEST_METHOD'];

$headers = getallheaders();

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
            case 'get_carosello':
                // Ritorna i 4 articoli più recenti (funziona)
                getCarosello();
                break;
            
            case 'get_card':
                // Ritorna un tot di articoli per generare le card nella home page
                break;

            case 'get_articolor':
                // Ritorna un singolo articolo dato l'id
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
        // Solo un utente autorizzato può accedere a questo codice
        $token = $gestioneJWT->getJWT($headers);
        $userRole = $gestioneJWT->decode($token);

        if ($userRole->ruolo === '1' || $userRole->ruolo === '2') {
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['errore' => 'Errore nel caricamento dell\'immagine']);
                http_response_code(400); // BAD REQUEST
                exit();
            }

            $titolo = $_FILES['image']['name'];
            $dimensioni = $_FILES['image']['size'];
            $immagine = file_get_contents($_FILES['image']['tmp_name']);
            $tipo = $_FILES['image']['type'];

            $query = "INSERT INTO Immagini (titolo, dimensioni, immagine, tipo) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                echo json_encode(['errore' => 'Errore nella preparazione della query']);
                http_response_code(500); // INTERNAL SERVER ERROR
                exit();
            }

            $stmt->bind_param("sibs", $titolo, $dimensioni, $immagine, $tipo);

            if ($stmt->execute()) {
                header("Content-Type: application/json; charset=UTF-8");
                $imageId = $conn->insert_id;
                echo json_encode(["id_immagine" => $imageId]);
                http_response_code(200); // OK
            } else {
                echo json_encode(['errore' => 'Errore nell\'inserimento dell\'immagine']);
                http_response_code(500); // INTERNAL SERVER ERROR
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

    $query = "SELECT * FROM Articolo ORDER BY data_pubblicazione LIMIT 4;";
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
