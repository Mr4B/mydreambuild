<?php 
// include 'shared/navbar.php';
session_start();
require_once('../shared/navbar.php');
require_once('../webservices/common/auth.php');

// Token Authentication
$gestioneJWT = new TokenJWT('ciao');
// Ruolo dell'utente da includere nel token
$role = 4; // di base imposta 'guest'
// Creazione del payload del token con il ruolo
$payload = array(
    "ruolo" => $role,
    "exp" => time() + 3600*2 // Scadenza del token impostata a 1 ora (3600 secondi)
);
$token = $gestioneJWT->encode($payload);
$_SESSION['jwt'] = $token;
$_SESSION['ruolo'] = 4;

echo "<script>console.log(".$token.");</script>";

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                <!-- <div class="container">
                    <img src="../img/logo_mecc.avif" class="d-inline-block align-top" style="float: left; margin-right: 30px;" alt="Logo meccanico" height="75" width="70">
                    <h1 class="display-4">Benvenuto</h1>
                </div> -->
            </div>
            <?php echo $navbar->getNavBar();?>
        </header>

        <!-- Resto della pagina -->
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</html>