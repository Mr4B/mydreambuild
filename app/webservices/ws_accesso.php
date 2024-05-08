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
        $email = isset($_GET['email']) ? $_GET['email'] : '';
        $role = isset($_GET['role']) ? $_GET['role'] : '';

        switch($action) {
            case 'get_users':
                $query = "SELECT * FROM Utente ORDER BY ruolo;";
                break;

            case 'get_byRole':
                $query = "SELECT * FROM Utente WHERE ruolo = ?;";
                break;
            
            case 'user':
                $query = "SELECT * FROM Utente WHERE email = ?;";
                break;

            default:
                echo json_encode(['errore' => 'Indirizzo errato']);
                http_response_code(404);
                break;
        }

        $stmt = $conn->prepare($query);
        
        if($email!='') {
            $stmt->bind_param("s", $email);
        }
        
        if($role!='') {
            $stmt->bind_param("i", $role);    
        }

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
            
            // Recupera il valore del parametro "action" dall'URL
            $action = isset($_GET['action']) ? $_GET['action'] : '';

            $payload = file_get_contents('php://input');


            // Trasformazione del payload nell'array che contiene i dati
            if ($content_type == 'application/json') {
                $data =  json_decode($payload,true);
                $email = isset($data['email']) ? $data['email'] : '';
                $password = isset($data['password']) ? $data['password'] : '';
            } else {
                echo json_encode(['errore' => 'Content-Type non ammesso']);
                http_response_code(400); //BAD REQUEST
                exit();
            }

            header("Content-Type: application/json; charset=UTF-8");

            switch($action) {
                case 'signup':
                    verifyIfExists($data);
                    addUser($data);
                    break;

                case 'login':
                    login($email, $password);
                    break;

                default:
                    echo json_encode(['errore' => 'Indirizzo errato']);
                    http_response_code(400);
                    break;
            }

            /* 
        } else {
            echo json_encode(['errore' => 'Unauthorized']);
            http_response_code(401); //BAD REQUEST
        } */


        break;

    case 'PUT':
        // Recupera il payload (body of message)
        $payload = file_get_contents('php://input');

        // Trasformazione del payload in array che contiene i dati
        if ($content_type == 'application/json') {
            $data = json_decode($payload, true);
            $email = isset($data['email']) ? $data['email'] : '';
            // Altri dati che desideri modificare
        } else {
            echo json_encode(['errore' => 'Content-Type non ammesso']);
            http_response_code(400); //BAD REQUEST
            exit();
        }

        // Esegui la query per aggiornare l'utente
        $query = "UPDATE Utente SET nome=?, cognome=?, ruolo=? WHERE email=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssis", $data['nome'], $data['cognome'], $data['ruolo'], $email);
        $stmt->execute();

        // Verifica se l'aggiornamento è andato a buon fine
        if ($stmt->affected_rows > 0) {
            echo json_encode(["Success" => "Utente aggiornato con successo"]);
            http_response_code(200);
        } else {
            echo json_encode(['errore' => 'Errore durante l\'aggiornamento dell\'utente']);
            http_response_code(400); //BAD REQUEST
        }
        exit();
        break;

    case 'DELETE':
        // Recupera l'email dell'utente da eliminare dall'URL
        $email = isset($_GET['email']) ? $_GET['email'] : '';

        // Esegui la query per eliminare l'utente
        $query = "DELETE FROM Utente WHERE email=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();

        // Verifica se l'eliminazione è andata a buon fine
        if ($stmt->affected_rows > 0) {
            echo json_encode(["Success" => "Utente eliminato con successo"]);
            http_response_code(200);
        } else {
            echo json_encode(['errore' => 'Errore durante l\'eliminazione dell\'utente']);
            http_response_code(400); //BAD REQUEST
        }
        exit();
        break;
}

// Verifica se un utente esiste già
function verifyIfExists($user) {
    global $conn;
    $query = "SELECT * FROM Utente WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user['email']);
    $stmt->execute();

    if ($stmt->affected_rows > 0){
        echo json_encode(["Error" => "User already exists"]);
        http_response_code(400);
        exit();
    }
}

function addUser($data) {
    global $conn;

    $query = "INSERT INTO Utente (email, password, nome, cognome, ruolo) VALUES (?,?,?,?,?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssi", $data['email'], $data['password'], $data['nome'], $data['cognome'], $data['ruolo']);

    // Esecuzione query
    $stmt->execute();
    // Recupero dei risultati
    $result = $stmt->get_result();

    if ($stmt->affected_rows > 0){
        echo json_encode(["Success" => "Dati aggiunti con successo"]);
        http_response_code(200);
        exit();
    }else {
        echo json_encode(['errore' => $conn->error]);
        http_response_code(400); //BAD REQUEST
        exit();
    }
}

function login($email, $password) {
    global $conn;

    $query = "SELECT * FROM Utente WHERE email = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode("Login avvenuto con successo");
        echo json_encode($user);
        http_response_code(200);
    } else {
        // Altrimenti, restituisci un errore con il codice di stato HTTP 401.
        echo json_encode(['errore' => 'Credenziali non valide']);
        http_response_code(401);
    }
    exit(); // Termina lo script dopo il login
}