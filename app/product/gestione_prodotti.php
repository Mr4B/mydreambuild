<?php
session_start();
require_once('../shared/navbar.php');   
$navbar = new NavBar();
$navbar->setLogin($_SESSION['username'], $_SESSION['ruolo']);
$token = $_SESSION['jwt'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>
    <!--Esempio di header -->
    <div class="container-fluid">
        <header id="header" class role="banner">
            <?php echo $navbar->getNavBar(); ?>
        </header>
        <!-- Corpo della pagina -->
        <div id="response" class="mt-3"></div>
    </div>

    <!-- Script jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            $.ajax({
                url: 'http://10.25.0.15/~s_bttkvn05l18d488f/capolavoro-main/app/webservices/ws_prodotti.php?action=get_products',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    console.log(data);
                    // Inserisci qui il codice per gestire la risposta JSON
                    // Ad esempio, visualizza i prodotti nella pagina HTML
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
                    $("#response").html("Errore");
                }
            });            
            return false;
        });
    </script>
    
    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzXuhxVwGO09UHs0PkBu3nBb2S2HgpmTk5U4xZPbNNiueEByzPKiUarJHfpeX6lw" crossorigin="anonymous"></script>
</body>
</html>
