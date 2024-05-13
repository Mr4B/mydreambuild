<?php 
// include 'shared/navbar.php';
session_start();
require_once('../shared/navbar.php');
require_once('../webservices/common/auth.php');

$gestioneJWT = new TokenJWT('ciao');
if(isset($_SESSION['LogedIn']) && $_SESSION['LogedIn'] === true) {
    if(!isset($_SESSION['jwt']) || !isset($_SESSION['username'])) {
        // Creazione token per l'utente registrato
        // Dati ripresi tramite url
        // echo "entrator";
        $role = isset($_GET['ruolo']) ? $_GET['ruolo'] : 3;
        $_SESSION['ruolo'] = $role;
        $username = isset($_GET['username']) ? $_GET['username'] : 'Lillo';
        $_SESSION['username'] = $username;
    
        // Creazione del payload del token con il ruolo
        $payload = array(
            "ruolo" => $role,
            "exp" => time() + 3600*2 // Scadenza del token impostata a 2 ora (3600 secondi)
        );
        $token = $gestioneJWT->encode($payload);
        $_SESSION['jwt'] = $token;

    }
} else {
    // Crea il token per l'utente guest

    // Creazione del payload del token con il ruolo
    $payload = array(
        "ruolo" => 4,
        "exp" => time() + 3600*2 // Scadenza del token impostata a 2 ora (3600 secondi)
    );
    $token = $gestioneJWT->encode($payload);
    $_SESSION['jwt'] = $token;
    $_SESSION['ruolo'] = 4;
}

// NAVBAR
$navbar = new Navbar();
// Controlla se l'utente è loggato e in caso positivo aggiorna la navbar
if(isset($_SESSION['LogedIn']) && $_SESSION['LogedIn'] === true) {
    $navbar ->setLogin($_SESSION['username'], $_SESSION['ruolo']);
}
$_SESSION['navbar'] = $navbar;

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <!-- Aggiorna bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
<!--Esempio di header -->
    <div class="container-fluid">
        <header id="header" class role="banner">
            <!--Jumbotron-->
            <div class="jumbotron jumbotron-fluid mb-2">
                <div class="container">  
                    <!-- <img src="../img/logo_mecc.avif" class="d-inline-block align-top" style="float: left; margin-right: 30px;" alt="Logo meccanico" height="75" width="70"> -->
                    <h1 class="display-4">MYDREAMBUILD</h1>
                </div>
            </div>
            <?php echo $navbar->getNavBar();?>
        </header>

        <!-- Resto della classe -->

    </div>
</body>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7og evT6WM0vSr0=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</html>