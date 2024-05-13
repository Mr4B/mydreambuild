<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class TokenJWT {

    private static $secretKey; // Chiave segreta per la firma (impostala nel constructor)
    private static $algo = 'HS256'; // Algoritmo di firma (HS256, SHA256, ecc.)

    public function __construct($secretKey) {
        self::$secretKey = $secretKey;
    }

    public static function encode($payload) {
        // Libreria JWT è necessaria

        // Crea l'oggetto JWT
        $jwt = new JWT;

        // Genera il token JWT
        $token = $jwt->encode($payload, self::$secretKey, self::$algo);

        return $token;
    }

    public static function decode($token) {
        // Libreria JWT è necessaria
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
        
        try {
            // Decodifica il token JWT
            $payload = JWT::decode($token, new KEY(self::$secretKey, self::$algo));

            return $payload;
        } catch (\Exception $e) {
            return false; // Errore di decodifica
        }
    }

    public static function validate($token) {
        // Libreria JWT è necessaria
        require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
        
        $jwt = new JWT;

        try {
            // Decodifica il token JWT
            $payload = JWT::decode($token, new KEY(self::$secretKey, self::$algo));

            // Verifica se il token è scaduto
            if (isset($payload->exp) && time() > $payload->exp) {
                return false; // Token scaduto
            }

            // Altri controlli di validità opzionali (es: audience, issuer)

            return true; // Token valido
        } catch (\Exception $e) {
            return false; // Errore di decodifica o validazione
        }
    }

    public function getJWT($headers) {
        // Controlla se l'header Authorization è presente nella richiesta
    if (!isset($headers['Authorization'])) {
        // Se l'header Authorization non è presente, l'utente non è autenticato
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(['errore' => 'Manca autorizzazione']);
        http_response_code(401); //Unauthorized
        exit();
    }
    
    // Estrai il token JWT dall'header Authorization
    $authHeader = $headers['Authorization'];
    // Verifica che l'header Authorization contenga un token JWT valido
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];
        return $token;
    } else {
        // Formato dell'header Authorization non corretto
        echo json_encode($authHeader);
        echo json_encode(['errore' => 'Token JWT passato male']);
        header("HTTP/1.1 401 Unauthorized");
        exit();
    }
    }
}


// Funzione per verificare l'autenticazione dell'utente utilizzando il token JWT?>