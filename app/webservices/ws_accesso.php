<?php
include '..\..\db_connect.php';
include 'common\auth.php';

$conn = getConnection();

//Recupera il metodo richiesto
$method = $_SERVER['REQUEST_METHOD'];

$headers = getallheaders();

// Tipo di contenuto del messaggio
$content_type = 'application/json';
// Risposta da restituire
$content_type_response = 'application/json';

$query = "";
// authenticateUser();


/* Azione a seconda del metodo */
switch ($method) {
    case 'GET':
        // ---- metodo per fare più get filtrati ----
        // aggiunge un parametro alla richiesta url e poi in base al valore recuperato in uno switch scrive la query da eseguire

        // Recupera il valore del parametro "action" dall'URL
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $id = isset($_GET['id']) ? $_GET['id'] : '';

        switch($action) {
            case 'get_users':
                // Ritorna tutti i prodotti
                $query = "SELECT * FROM Utente;";
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
        // Recupero del payload (body of message)
        $userRole = getUserRole(authenticateUser());
        // if($userRole === 'admin' || $userRole === 'moderator') {
            // Solo un autente autorizzato può accedere a questo codice

            $payload = file_get_contents('php://input');


            // Trasformazione del payload nell'array che contiene i dati
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
                case 'post_signup':
                    $query = "INSERT INTO Utente (email, password, nome, cognome, ruolo) VALUES (?,?,?,?,?)";
                    break;

                default:
                    $query = "SELECT * FROM Veicolo LIMIT 1;";
                    break;
            }

            $stmt = $conn->prepare($query);
            $stmt->bind_param("s,s,s,s,s", $data['email'], $data['password'], $data['nome'], $data['cognome'], $data['ruolo']);

            // Esecuzione query
            $stmt->execute();

            if ($result){
                echo json_encode(["Success" =>`Dati aggiunti con successo: $data`]);
                http_response_code(200);
            }else {
                echo json_encode(['errore' => $conn->error]);
                http_response_code(400); //BAD REQUEST
            }/* 
        } else {
            echo json_encode(['errore' => 'Unauthorized']);
            http_response_code(401); //BAD REQUEST
        } */


        break;

    case 'PUT':


        break;

    case 'DELETE':

        break;
}