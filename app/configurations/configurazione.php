<!-- Pagina per tutti gli utenti, dove poi i loggati potranno vedere le loro configurazioni -->
<?php
session_start();

require_once('../shared/navbar.php');   
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Script jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>    
    <script type="text/javascript">
        $(document).ready(function(){
            <?php 
            if(isset($_SESSION['LogedIn']) && $_SESSION['LogedIn'] === true) { ?>
            // Le mie configurazioni
            $.ajax({
                // url: 'http://10.25.0.15/~s_bttkvn05l18d488f/capolavoro-main/app/webservices/ws_prodotti.php?action=get_products',
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_configurazioni.php?action=get_myconfiguration&id_utente=<?php if(isset($_SESSION['LogedIn']) && $_SESSION['LogedIn'] === true) echo $_SESSION['username']; else 'errore'; ?>',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    console.log(data);
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
                    $("#table").html("Errore");
                }
            });         
            
            <?php }?>
            
            $.ajax({
                // url: 'http://10.25.0.15/~s_bttkvn05l18d488f/capolavoro-main/app/webservices/ws_prodotti.php?action=get_products',
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    console.log(data);
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
                    $("#table").html("Errore");
                }
            });            

            return false;
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
        <div id="your">
            <h5>Le mie configurazioni</h5>

        </div>
        <?php 
            if(isset($_SESSION['LogedIn']) && $_SESSION['LogedIn'] === true) {
                echo '<button type="button" class="btn btn-outline-secondary" onclick="window.location.href=\'new_configurazione.php\'">Crea la tua configurazione</button>';   
            } else {
                echo '<a class="link" href="../user/login.php">Accedi per creare la tua configurazione</a>';   
            }
        ?>
        <div id="quotate">
            <h5>Configurazioni consigliate</h5>

        </div>

    </div>
    
    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
