<?php
include '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'db_connect.php';
include 'common' . DIRECTORY_SEPARATOR . 'auth.php';

$conn = getConnection();

if ($conn === false) {
    die("Connection failed: " . $conn->connect_error);
}

//Recupera il metodo richiesto
$method = $_SERVER['REQUEST_METHOD'];

$headers = getallheaders();

// Tipo di contenuto del messaggio
$content_type = 'application/json';
// Risposta da restituire
$content_type_response = 'application/json';

$query = "";


$gestioneJWT = new TokenJWT('ciao');

$token = $gestioneJWT->getJWT($headers);

// Per validare il token ma non serve subito
// $gestioneJWT -> validate($token);


/* Azione a seconda del metodo */
switch ($method) {
    case 'GET':
        // ---- metodo per fare più get filtrati ----
        // aggiunge un parametro alla richiesta url e poi in base al valore recuperato in uno switch scrive la query da eseguire

        // Recupera il valore del parametro "action" dall'URL
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $username = isset($_GET['username']) ? $_GET['username'] : '';
        $role = isset($_GET['role']) ? $_GET['role'] : '';

        switch($action) {
            case 'get_users':
                $query = "SELECT * FROM Utente ORDER BY ruolo;";
                break;

            case 'get_byRole':
                $query = "SELECT * FROM Utente WHERE ruolo = ?;";
                break;
            
            case 'user':
                $query = "SELECT username, email, nome, cognome, ruolo FROM Utente WHERE username = ?;";
                break;
            
            case 'get_roles':
                $query = "SELECT * FROM Ruolo";
                break;

            default:
                echo json_encode(['errore' => 'Indirizzo errato']);
                http_response_code(404);
                break;
        }

        $stmt = $conn->prepare($query);
        
        if($username!='') {
            $stmt->bind_param("s", $username);
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
        // Recupera il valore del parametro "action" dall'URL
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        $payload = file_get_contents('php://input');


        // Trasformazione del payload nell'array che contiene i dati
        if ($content_type == 'application/json') {
            $data =  json_decode($payload,true);
            $username = isset($data['username']) ? $data['username'] : '';
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
                login($username, $password);
                break;

            default:
                echo json_encode(['errore' => 'Indirizzo errato']);
                http_response_code(400);
                break;
        }

        break;

    case 'PUT':
        
        $action = isset($_GET['action']) ? $_GET['action'] : '';

        // Recupera il payload (body of message)
        $payload = file_get_contents('php://input');

        // Trasformazione del payload in array che contiene i dati
        if ($content_type == 'application/json') {
            $data = json_decode($payload, true);
            // $username = isset($data['username']) ? $data['username'] : '';
            // Altri dati che desideri modificare
        } else {
            echo json_encode(['errore' => 'Content-Type non ammesso']);
            http_response_code(400); //BAD REQUEST
            exit();
        }

        header("Content-Type: application/json; charset=UTF-8");

        switch($action) {
            case 'update_role':
                updateRole($data);
                break;

            case 'update_user':
                updateUser($data);
                break;

            default:
                echo json_encode(['errore' => 'Indirizzo errato']);
                http_response_code(400);
                break;
        }
        break;

    case 'DELETE':
        // Recupera l'email dell'utente da eliminare dall'URL
        $username = isset($_GET['username']) ? $_GET['username'] : '';

        // Esegui la query per eliminare l'utente
        $query = "DELETE FROM Utente WHERE username=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $username);
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
    $query = "SELECT * FROM Utente WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $user['username']);
    $stmt->execute();
    $result = $stmt->get_result();

    // echo json_encode($stmt->affected_rows);

    if ($stmt->affected_rows > 0){
        echo json_encode(["Error" => "User already exists"]);
        http_response_code(400);
        exit();
    }
}

function addUser($data) {
    global $conn;

    $query = "INSERT INTO Utente (username, password,  email, nome, cognome, ruolo) VALUES (?,?,?,?,?,?)";
    $stmt = $conn->prepare($query);

    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

    $stmt->bind_param("sssssi", $data['username'], $hashed_password, $data['email'], $data['nome'], $data['cognome'], $data['ruolo']);

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

function login($username, $password) {
    global $conn;

    $query = "SELECT * FROM Utente WHERE username = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(['errore' => 'Errore nella preparazione della query: ' . $conn->error]);
        http_response_code(500);
        exit();
    }

    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if(password_verify($password, $user['password'])) {
            echo json_encode($user);
            http_response_code(200);
        } else {
            echo json_encode(['errore' => 'Password non valida']);
            http_response_code(400);
        }
    } else {
        // Altrimenti, restituisci un errore con il codice di stato HTTP 401.
        echo json_encode(['errore' => 'Utente inesistente']);
        http_response_code(400);
    }
    exit(); // Termina lo script dopo il login
}

function updateRole($data) {
    global $conn;

    // Verifica che i dati necessari siano presenti
    if (!isset($data['ruolo']) || !isset($data['username'])) {
        echo json_encode(['errore' => 'Dati mancanti: ruolo o username non specificati']);
        http_response_code(400); // BAD REQUEST
        exit();
    }

    // Prepara la query per aggiornare l'utente
    $query = "UPDATE Utente SET ruolo=? WHERE username=?";
    if ($stmt = $conn->prepare($query)) {
        // Lega i parametri
        if ($stmt->bind_param("is", $data['ruolo'], $data['username'])) {
            // Esegui la query
            if ($stmt->execute()) {
                // Verifica se l'aggiornamento è andato a buon fine
                if ($stmt->affected_rows > 0) {
                    echo json_encode(["Success" => "Utente aggiornato con successo"]);
                    http_response_code(200); // OK
                } else {
                    echo json_encode(['errore' => 'Nessuna modifica effettuata. Utente non trovato o ruolo già assegnato']);
                    http_response_code(400); // BAD REQUEST
                }
            } else {
                echo json_encode(['errore' => 'Errore durante l\'esecuzione della query: ' . $stmt->error]);
                http_response_code(500); // INTERNAL SERVER ERROR
            }
        } else {
            echo json_encode(['errore' => 'Errore durante il binding dei parametri: ' . $stmt->error]);
            http_response_code(500); // INTERNAL SERVER ERROR
        }
        $stmt->close();
    } else {
        echo json_encode(['errore' => 'Errore durante la preparazione della query: ' . $conn->error]);
        http_response_code(500); // INTERNAL SERVER ERROR
    }
}

function updateUser($data) {
    global $conn;
    // Esegui la query per aggiornare l'utente
    $query = "UPDATE Utente SET nome=?, cognome=?, ruolo=?, email=? WHERE username=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssiss", $data['nome'], $data['cognome'], $data['ruolo'], $data['email'], $username);
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
}