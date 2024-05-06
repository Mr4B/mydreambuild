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

authenticateUser();


/* Azione a seconda del metodo */
switch ($method) {
    case 'GET':
        // ---- metodo per fare più get filtrati ----
        // aggiunge un parametro alla richiesta url e poi in base al valore recuperato in uno switch scrive la query da eseguire

        // Recupera il valore del parametro "action" dall'URL
        $action = isset($_GET['action']) ? $_GET['action'] : '';
        $id = isset($_GET['id']) ? $_GET['id'] : '';

        $cat = null;

        switch($action) {
            case 'get_products':
                // Ritorna tutti i prodotti
                $query = "SELECT * FROM Prodotto;";
                break;
                
            case 'get_byID':
                $query = "SELECT * FROM Prodotto WHERE id_prodotto = ?;";
                break;

            case 'get_GPU':
                // Ritorna tutte le schede video
                $query = "SELECT * FROM Prodotto WHERE id_categoria = ?;";
                $cat = 1;
                break;

            case 'get_CPU':
                // Ritorna tutte le schede video
                $query = "SELECT * FROM Prodotto WHERE id_categoria = ?;";
                $cat = 2;
                break;

            case 'get_motherboard':
                // Ritorna tutte le schede video
                $query = "SELECT * FROM Prodotto WHERE id_categoria = ?;";
                $cat = 3;
                break;
            
            case 'get_RAM':
                // Ritorna tutte le schede video
                $query = "SELECT * FROM Prodotto WHERE id_categoria = ?;";
                $cat = 4;
                break;

            case 'get_SSD':
                // Ritorna tutte le schede video
                $query = "SELECT * FROM Prodotto WHERE id_categoria = ?;";
                $cat = 5;
                break;

            case 'get_HDD':
                // Ritorna tutte le schede video
                $query = "SELECT * FROM Prodotto WHERE id_categoria = ?;";
                $cat = 6;
                break;

            case 'get_PSU':
                // Ritorna tutte le schede video
                $query = "SELECT * FROM Prodotto WHERE id_categoria = ?;";
                $cat = 7;
                break;

            case 'get_case':
                // Ritorna tutte le schede video
                $query = "SELECT * FROM Prodotto WHERE id_categoria = ?;";
                $cat = 8;
                break;

            case 'get_cooling':
                // Ritorna tutte le schede video
                $query = "SELECT * FROM Prodotto WHERE id_categoria = ?;";
                $cat = 9;
                break;

            default:
                echo json_encode(['errore' => 'Indirizzo errato']);
                http_response_code(404);
                break;
        }

        $stmt = $conn->prepare($query);

        if(isset($cat)){
            $stmt->bind_param("i", $cat);
        }

        if($id != '') {
            $stmt->bind_param("i", $id);
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
        if($userRole === 'admin' || $userRole === 'moderator') {
            // Solo un autente autorizzato può accedere a questo codice

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
        } else {
            echo json_encode(['errore' => 'Unauthorized']);
            http_response_code(401); //BAD REQUEST
        }


        break;

    case 'PUT':


        break;

    case 'DELETE':

        break;
}