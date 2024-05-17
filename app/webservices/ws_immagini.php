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
/* $token = $gestioneJWT->getJWT($headers);

if (!$gestioneJWT->validate($token)) {
    echo json_encode(['errore' => 'Token errato']);
    http_response_code(404);
    exit();
} */

switch ($method) {
    case 'GET':
        // Recupera il valore del parametro "id" dall'URL
        $id_immagine = isset($_GET['id']) ? intval($_GET['id']) : null;

        if ($id_immagine === null) {
            echo json_encode(['errore' => 'ID immagine mancante']);
            http_response_code(400); // BAD REQUEST
            exit();
        }

        $query = "SELECT titolo, dimensioni, immagine, tipo FROM Immagini WHERE id_immagine = ?";
        $stmt = $conn->prepare($query);

        if (!$stmt) {
            echo json_encode(['errore' => 'Errore nella preparazione della query']);
            http_response_code(500); // INTERNAL SERVER ERROR
            exit();
        }

        $stmt->bind_param("i", $id_immagine);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            echo json_encode(['errore' => 'Immagine non trovata']);
            http_response_code(404); // NOT FOUND
            exit();
        }

        $stmt->bind_result($titolo, $dimensioni, $immagine, $tipo);
        $stmt->fetch();

        // Imposta i corretti header per il tipo di immagine
        header("Content-Type: $tipo");
        header("Content-Length: $dimensioni");
        header("Content-Disposition: inline; filename=\"$titolo\"");

        // Stampa l'immagine
        echo $immagine;
        // echo '<img src="data:'.$tipo.';base64,'.base64_encode($immagine).'"/>';
        http_response_code(200); // OK
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

            $query = "INSERT INTO Immagini (titolo, immagine, dimensioni, tipo) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($query);

            if (!$stmt) {
                echo json_encode(['errore' => 'Errore nella preparazione della query']);
                http_response_code(500); // INTERNAL SERVER ERROR
                exit();
            }

            $stmt->bind_param("sbis", $titolo, $null, $dimensioni, $tipo);
            $stmt->send_long_data(1, $immagine);

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
?>
