<?php
session_start();

/* if (!isset($_SESSION['isLoged']) || $_SESSION['isLoged'] !== true) {
    header('Location: ../user/login.php'); // Reindirizza alla pagina di login
    exit; // Termina l'esecuzione dello script
} */

require_once('../shared/navbar.php');   
include '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'db_connect.php';
$navbar = new NavBar();
$navbar->setLogin($_SESSION['username'], $_SESSION['ruolo']);
$token = $_SESSION['jwt'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurazioni</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Script jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>    
    <style>
        .image {
            max-width: 110px;
            height: auto;
        }

        .btn-new-config {
            margin-bottom: 20px;
        }

        .id {
            width: 100px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $.ajax({
                url: '<?php echo $url; ?>app/webservices/ws_configurazioni.php?action=get_defaultconfiguration',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    console.log(data);
                    var table = '<table class="table table-striped"><thead><tr><th>ID Configurazione</th><th>Immagine</th><th>Denominazione</th><th>Tipologia</th><th>Prezzo Totale</th><th></th></tr></thead><tbody>';
                    $.each(data, function(index, configuration) {
                        table += '<tr>';
                        table += '<td class="id">' + configuration.id + '</td>';
                        if (configuration.id_immagine) {
                            table += '<td><img class="image" src="<?php echo $url; ?>app/webservices/ws_immagini.php?id=' + configuration.id_immagine + '"></td>';
                        } else {
                            table += '<td>No</td>';
                        }
                        table += '<td>' + configuration.denominazione + '</td>';
                        table += '<td>' + configuration.tipologia + '</td>';
                        table += '<td>' + configuration.prezzo_totale + '€</td>';
                        // table += '<td><a href="edit_configurazione.php?id=' + configuration.id + '"><img src="../img/edit.png" alt="modifica" width="25" height="25"></a></td>';
                        table += '<td><a href="#"><img src="../img/edit.png" alt="modifica" width="25" height="25"></a></td>';
                        table += '</tr>';
                    });
                    table += '</tbody></table>';
                    $("#table").html(table);
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
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
        <button type="button" class="btn btn-outline-info btn-new-config" onclick="window.location.href='new_configurazione_mod.php'">Nuova configurazione</button>
        <div id="table"></div>
    </div>
    
    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
