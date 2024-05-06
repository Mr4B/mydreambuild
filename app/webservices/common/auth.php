<?php
require __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';


use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// Funzione per verificare l'autenticazione dell'utente utilizzando il token JWT
function authenticateUser() {
    global $headers;

    // Controlla se l'header Authorization è presente nella richiesta
    if (!isset($headers['Authorization'])) {
        // Se l'header Authorization non è presente, l'utente non è autenticato
        header("HTTP/1.1 401 Unauthorized");
        http_response_code(401); //Unauthorized
        exit();
    }

    // Estrai il token JWT dall'header Authorization
    $authHeader = $headers['Authorization'];

    // Verifica che l'header Authorization contenga un token JWT valido
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $token = $matches[1];

        // Decodifica il token JWT utilizzando la chiave segreta
        $key = 'ciao';
        try {
            $decodedToken = JWT::decode($token, new Key($key, 'HS256'));

            // L'utente è autenticato
            return $token;
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

// Funzione per ottenere il ruolo dall'utente dal token JWT
function getUserRole($token) {
    try {
        // Decodifica il token JWT utilizzando la chiave segreta
        $key = 'ciao';
        $decodedToken = JWT::decode($token, new Key($key, 'HS256'));

        // Verifica che il token decodificato contenga il campo 'ruolo'
        if (isset($decodedToken->ruolo)) {
            // Restituisci il ruolo dell'utente
            return $decodedToken->ruolo;
        } else {
            // Il token non contiene il campo 'ruolo'
            throw new Exception("Il token JWT non contiene il campo 'ruolo'.");
        }
    } catch (Exception $e) {
        // Gestione degli errori durante la decodifica del token JWT
        // Puoi registrare l'errore, inviare una risposta di errore, ecc.
        return null; // o gestisci l'errore in un altro modo
    }
}



?>