<!-- Pagina per tutti gli utenti, dove poi i loggati potranno vedere le loro configurazioni -->
<?php
session_start();

require_once('../shared/navbar.php');   
require_once('../shared/footer.php');
include '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'db_connect.php';
$navbar = new NavBar();
if(isset($_SESSION['LogedIn']) && $_SESSION['LogedIn'] === true) {
    $navbar ->setLogin($_SESSION['username'], $_SESSION['ruolo']);
}
$token = $_SESSION['jwt'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="stile.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Script jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>    
    <style>
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            
            // Configurazioni consigliate
            $.ajax({
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_accesso.php?action=',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    // console.log(data);
                },
                error: function(xhr, status, error) {
                    // console.error('Errore durante la richiesta:', status, error);
                    $("#table").html("Errore");
                }
            });
        });
    </script>
</head>
<body>
    <!--Esempio di header -->
    <div class="container-fluid">
        <header id="header" class role="banner">
            <?php echo $navbar->getNavBar(); ?>
        </header>
        <!-- Corpo della pagina -->
        <img src="../img/login.png" width="100" height="100">
        <?php 
        echo '<h1>'.$_SESSION['username'].'</h1>';
        ?>
        <?php $footer = new Footer(); echo $footer->getFooter(); ?>
    </div>
    
    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
