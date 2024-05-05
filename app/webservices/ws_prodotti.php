<?php
include '../../db_connect.php';
require __DIR__ . '/../../vendor/autoload.php';

use \Firebase\JWT\JWT;

//Recupera il metodo richiesto
$method = $_SERVER['REQUEST_METHOD'];

$headers = getallheaders();

// Tipo di contenuto del messaggio
$content_type = 'application/json';
// Risposta da restituire
$content_type_response = 'application/json';


// Funzione per verificare l'autenticazione dell'utente utilizzando il token JWT
// Funzione per autenticare un utente dato il token JWT e restituire i dati dell'utente se autenticato
function authenticateUser() {
    global $headers;

    // Controlla se l'header Authorization è presente nella richiesta
    if (!isset($headers['Authorization'])) {
        // Se l'header Authorization non è presente, l'utente non è autenticato
        header("HTTP/1.1 401 Unauthorized");
        exit();
    }

    // Estrai il token JWT dall'header Authorization
    $authHeader = $headers['Authorization'];

    // Verifica che l'header Authorization contenga un token JWT valido
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];

        // Decodifica il token JWT utilizzando la chiave segreta
        try {
            $decodedToken = \Firebase\JWT\JWT::decode($token, 'il_tuo_segreto', array('HS256'));

            // Ora puoi accedere ai dati dell'utente dal token decodificato, se necessario
            // Ad esempio, $decodedToken->userId, $decodedToken->username, etc.

            // Restituisci i dati dell'utente
            return $decodedToken;
        } catch (Exception $e) {
            // Errore nella decodifica del token JWT (token non valido o scaduto)
            header("HTTP/1.1 401 Unauthorized");
            exit();
        }
    } else {
        // Formato dell'header Authorization non corretto
        header("HTTP/1.1 401 Unauthorized");
        exit();
    }
}


// Funzione per verificare il ruolo dell'utente
/* function checkRole($requiredRole) {

    global $headers;

    // Ottieni il token JWT dall'header della richiesta
    $jwt = $headers['Authorization'];

    // Decodifica il token JWT per ottenere i dettagli dell'utente, compreso il ruolo
    $decodedToken = decodeJWT($jwt);

    // Ottieni il ruolo dell'utente dal token decodificato
    $userRole = $decodedToken['role'];

    // Verifica se il ruolo dell'utente corrisponde al ruolo richiesto
    if ($userRole != $requiredRole) {
        header("HTTP/1.1 403 Forbidden");
        exit();
    }
} */

authenticateUser();


/* Azione a seconda del metodo */
switch ($method) {
    case 'GET':
        // ---- metodo per fare più get filtrati ----
        // aggiunge un parametro alla richiesta url e poi in base al valore recuperato in uno switch scrive la query da eseguire

        // Recupera il valore del parametro "action" dall'URL
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $search = isset($_GET['search']) ? $_GET['id'] : '';

        switch($action) {
            case 'get_':
                // Azione per recuperare i primi dati
                $query = "";
                break;

            case 'get_':
                $query = "";
                break;

            case 'get_':
                $query = "";
                break;

            default:
                $query = "SELECT * FROM Veicolo LIMIT 1;";
                break;
        }

        /* Recupero dei dati dalla tabella */
        $result = $conn->query($query);
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
        // Recupero del payload (body of message)

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
            case 'post_':

                $query = "";
                break;

            case 'post_':
                $query = "";
                break;

            default:
                $query = "SELECT * FROM Veicolo LIMIT 1;";
                break;
        }


        $result = $conn->query($query);

        if ($result){
            echo json_encode($user);
            http_response_code(200);
        }else {
            echo json_encode(['errore' => $conn->error]);
            http_response_code(400); //BAD REQUEST
        }
        break;

    case 'PUT':


        break;

    case 'DELETE':

        break;
}