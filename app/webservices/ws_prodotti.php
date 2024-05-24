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
        $search = isset($_GET['search']) ? $_GET['search'] : '';
        $categoria = isset($_GET['cat']) ? $_GET['cat'] : '';

        $cat = null;

        switch($action) {
            case 'get_products':
                // Ritorna tutti i prodotti
                $query = "SELECT id_prodotto, id_categoria, marca, modello, prezzo, id_immagine 
                FROM Prodotto
                ORDER BY id_categoria;";
                break;
            
            case 'search_product':
                // Ritorna le configurazioni create da un utente
                searchProduct($search, $categoria);
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
                    $query = "INSERT INTO Prodotto (id_immagine, id_categoria, marca, modello, descrizione, prezzo, link, frequenza_base, c_frequenza_boost, c_n_core, c_n_thread, c_consumo_energetico, c_dim_cache, socket) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("iisssdsddiiiis", $data['id_immagine'], $data['id_categoria'], $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['frequenza_base'], $data['frequenza_boost'], $data['n_core'], $data['n_thread'], $data['consumo_energetico'], $data['dim_cache'], $data['socket']);
                    break;

                case 'post_gpu':
                    $query = "INSERT INTO Prodotto (id_immagine, id_categoria, marca, modello, descrizione, prezzo, link, g_memoria, g_tipo_memoria, frequenza_base, dimensioni) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("iisssdsisds", $data['id_immagine'], $data['id_categoria'], $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['g_memoria'], $data['g_tipo_memoria'], $data['frequenza_base'], $data['dimensioni']);
                    break;

                case 'post_ram':
                    $query = "INSERT INTO Prodotto (id_immagine, id_categoria, marca, modello, descrizione, prezzo, link, r_dimensione, r_velocita, r_tipo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("iisssdsiis", $data['id_immagine'], $data['id_categoria'], $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['r_dimensione'], $data['r_velocita'], $data['r_tipo']);
                    break;

                case 'post_hdd':
                    $query = "INSERT INTO Prodotto (id_immagine, id_categoria, marca, modello, descrizione, prezzo, link, capacita_gb, fattore_di_forma, a_velocita_rotazione, a_cache_mb, a_velocita_lettura_mb_s, a_velocita_scrittura_mb_s) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("iisssdsisiiii", $data['id_immagine'], $data['id_categoria'], $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['capacita_gb'], $data['fattore_di_forma'], $data['a_velocita_rotazione'], $data['a_cache_mb'], $data['a_velocita_lettura_mb_s'], $data['a_velocita_scrittura_mb_s']);
                    break;

                case 'post_ssd':
                    $query = "INSERT INTO Prodotto (id_immagine, id_categoria, marca, modello, descrizione, prezzo, link, capacita_gb, fattore_di_forma, a_interfaccia, a_velocita_lettura_mb_s, a_velocita_scrittura_mb_s) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("iisssdsissii", $data['id_immagine'], $data['id_categoria'], $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['capacita_gb'], $data['fattore_di_forma'], $data['a_interfaccia'], $data['a_velocita_lettura_mb_s'], $data['a_velocita_scrittura_mb_s']);
                    break;
                case 'post_case':
                    $query = "INSERT INTO Prodotto (id_immagine, id_categoria, marca, modello, descrizione, prezzo, link, cs_colore, dimensioni, cs_peso, fattore_di_forma, cs_finestra_laterale) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("iisssdsssisi", $data['id_immagine'], $data['id_categoria'], $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['cs_colore'], $data['dimensioni'], $data['cs_peso'], $data['fattore_di_forma'], $data['cs_finestra_laterale']);
                    break;

                case 'post_motherboard':
                    $query = "INSERT INTO Prodotto (id_immagine, id_categoria, marca, modello, descrizione, prezzo, link, m_formato, socket, m_chipset, m_numero_slot_ram, m_tipologia_ram, m_versione_pcie) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("iisssdssssiss", $data['id_immagine'], $data['id_categoria'], $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['m_formato'], $data['socket'], $data['m_chipset'], $data['m_numero_slot_ram'], $data['m_tipologia_ram'], $data['m_versione_pcie']);
                    break;
                
                case 'post_psu':
                    $query = "INSERT INTO Prodotto (id_immagine, id_categoria, marca, modello, descrizione, prezzo, link, fattore_di_forma, p_watt, p_schema_alimentazione) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("iisssdssis", $data['id_immagine'], $data['id_categoria'], $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['fattore_di_forma'], $data['p_watt'], $data['p_schema_alimentazione']);
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

            // DA FINIRE DI MODIFICARE!!!!
            switch($action) {
                case 'put_cpu':
                    $query = "UPDATE Prodotto SET marca = ?, modello = ?, descrizione = ?, prezzo = ?, link = ?, frequenza_base = ?, c_frequenza_boost = ?, c_n_core = ?, c_n_thread = ?, c_consumo_energetico = ?, c_dim_cache = ? WHERE id_prodotto = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("sssdsddiiiii", $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['frequenza_base'], $data['frequenza_boost'], $data['n_core'], $data['n_thread'], $data['consumo_energetico'], $data['dim_cache'], $data['id_prodotto']);
                    break;

                case 'put_gpu':
                    $query = "UPDATE Prodotto SET marca = ?, modello = ?, descrizione = ?, prezzo = ?, link = ?, g_memoria = ?, g_tipo_memoria = ?, frequenza_base = ?, dimensioni = ? WHERE id_prodotto = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("sssdsisdsi", $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['g_memoria'], $data['g_tipo_memoria'], $data['frequenza_base'], $data['dimensioni'], $data['id_prodotto']);
                    break;

                case 'put_ram':
                    $query = "UPDATE Prodotto SET marca = ?, modello = ?, descrizione = ?, prezzo = ?, link = ?, r_dimensione = ?, r_velocita = ?, r_tipo= ? WHERE id_prodotto = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("sssdsiisi", $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['r_dimensione'], $data['r_velocita'], $data['r_tipo'], $data['id_prodotto']);
                    break;

                case 'put_hdd':
                    $query = "UPDATE Prodotto SET marca = ?, modello = ?, descrizione = ?, prezzo = ?, link = ?, capacita_gb = ?, fattore_di_forma = ?, a_velocita_rotazione = ?, a_cache_mb = ?, a_velocita_lettura_mb_s = ?, a_velocita_scrittura_mb_s = ? WHERE id_prodotto = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("sssdsisiiiii", $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['capacita_gb'], $data['fattore_di_forma'], $data['a_velocita_rotazione'], $data['a_cache_mb'], $data['a_velocita_lettura_mb_s'], $data['a_velocita_scrittura_mb_s'], $data['id_prodotto']);
                    break;

                case 'put_ssd':
                    $query = "UPDATE Prodotto SET marca = ?, modello = ?, descrizione = ?, prezzo = ?, link = ?, capacita_gb = ?, fattore_di_forma = ?, a_interfaccia = ?, a_velocita_lettura_mb_s = ?, a_velocita_scrittura_mb_s = ? WHERE id_prodotto = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("sssdsissiii", $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['capacita_gb'], $data['fattore_di_forma'], $data['a_interfaccia'], $data['a_velocita_lettura_mb_s'], $data['a_velocita_scrittura_mb_s'], $data['id_prodotto']);
                    break;
                case 'put_case':
                    $query = "UPDATE Prodotto SET marca = ?, modello = ?, descrizione = ?, prezzo = ?, link = ?, cs_colore = ?, dimensioni = ?, cs_peso = ?, fattore_di_forma = ?, cs_finestra_laterale = ? WHERE id_prodotto = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("sssdsssisii", $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['cs_colore'], $data['dimensioni'], $data['cs_peso'], $data['fattore_di_forma'], $data['cs_finestra_laterale'], $data['id_prodotto']);
                    break;

                case 'put_motherboard':
                    $query = "UPDATE Prodotto SET marca = ?, modello = ?, descrizione = ?, prezzo = ?, link = ?, m_formato = ?, socket = ?, m_chipset = ?, m_numero_slot_ram = ?, m_tipologia_ram = ?, m_versione_pcie = ? WHERE id_prodotto = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("sssdssssissi", $data['id_immagine'], $data['id_categoria'], $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['m_formato'], $data['socket'], $data['m_chipset'], $data['m_numero_slot_ram'], $data['m_tipologia_ram'], $data['m_versione_pcie'], $data['id_prodotto']);
                    break;
                
                case 'put_psu':
                    $query = "UPDATE Prodotto SET marca = ?, modello = ?, descrizione = ?, prezzo = ?, link = ?, fattore_di_forma = ? p_watt = ? p_schema_alimentazione = ? WHERE id_prodotto = ?";
                    $stmt = $conn->prepare($query);
                    $stmt->bind_param("sssdssisi", $data['id_immagine'], $data['id_categoria'], $data['marca'], $data['modello'], $data['descrizione'], $data['prezzo'], $data['link'], $data['fattore_di_forma'], $data['p_watt'], $data['p_schema_alimentazione'], $data['id_prodotto']);
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

    case 'DELETE':

        break;
}

function searchProduct($search, $cat) {
    global $conn;

    $query = "SELECT * FROM Prodotto WHERE id_categoria = ? AND (marca LIKE ? OR modello LIKE ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo json_encode(['errore' => 'Errore nella preparazione della query: ' . $conn->error]);
        http_response_code(500);
        exit();
    }

    $searchTerm = $search . '%';
    if (!$stmt->bind_param("iss", $cat, $searchTerm, $searchTerm)) {
        echo json_encode(['errore' => 'Errore nel binding dei parametri: ' . $stmt->error]);
        http_response_code(500);
        exit();
    }

    if (!$stmt->execute()) {
        echo json_encode(['errore' => 'Errore nell\'esecuzione della query: ' . $stmt->error]);
        http_response_code(500);
        exit();
    }

    $result = $stmt->get_result();
    if ($result === false) {
        echo json_encode(['errore' => 'Errore nel recupero dei risultati: ' . $stmt->error]);
        http_response_code(500);
        exit();
    }
    
    // Impostazione del header field Content-Type
    header("Content-Type: application/json; charset=UTF-8");

    if ($result->num_rows > 0) {
        $lista = array();
        while ($row = $result->fetch_assoc()) {
            $lista[] = $row;
        }

        echo json_encode($lista);
        http_response_code(200);
        exit();
    } else {
        // Restituisce un messaggio specifico quando non ci sono prodotti trovati
        echo json_encode(['status' => 'empty']);
        http_response_code(200); // Usa 200 OK anche se non ci sono risultati
        exit();
    }

    // Chiudere lo statement
    $stmt->close();
}
