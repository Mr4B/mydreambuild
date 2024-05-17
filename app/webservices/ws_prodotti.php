<?php
/*  
i per interi (integer)
d per numeri con la virgola (double/decimal)
s per stringhe (string)
b per blob (binary large object) 
*/
include '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'db_connect.php';
include 'common' . DIRECTORY_SEPARATOR . 'auth.php';

$conn = getConnection();

//Recupera il metodo richiesto
$method = $_SERVER['REQUEST_METHOD'];

$headers = getallheaders();

// Tipo di contenuto del messaggio
$content_type = 'application/json';
// Risposta da restituire
$content_type_response = 'application/json';


$gestioneJWT = new TokenJWT('ciao');

$token = $gestioneJWT->getJWT($headers);


if(!($gestioneJWT -> validate($token))) {
    echo json_encode(['errore' => 'Token errato']);
    http_response_code(404);
}


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
                $query = "SELECT id_prodotto, id_categoria, marca, modello, prezzo, id_immagine 
                FROM Prodotto
                ORDER BY id_categoria;";
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
            
            case 'get_categorie':
                $query = "SELECT * FROM Categoria;";
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
        $userRole = $gestioneJWT->decode($token);
        // echo $userRole-> ruolo; funziona
        if($userRole -> ruolo === '1' || $userRole-> ruolo === '2') {
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

            // Gestione per l'inserimento dell'immagine
            // echo $data;
            // $conn->begin_transaction();

            // try {
            switch($action) {
                case 'post_cpu':

                    $query = "INSERT INTO Prodotto (id_immagine, id_categoria, marca, modello, descrizione, prezzo, link, frequenza_base, c_frequenza_boost, c_n_core, c_n_thread, c_consumo_energetico, c_dim_cache) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("iisssdsddiiii", $data['id_immagine'], $data['id_categoria'], $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['frequenza_base'], $data['frequenza_boost'], $data['n_core'], $data['n_thread'], $data['consumo_energetico'], $data['dim_cache']);
                
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
            http_response_code(401); //BAD REQUEST
        }

        break;

    case 'PUT':


        break;

    case 'DELETE':

        break;
}
