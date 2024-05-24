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
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Script jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>    
    <script type="text/javascript">
        $(document).ready(function(){
            $.ajax({
                // url: 'http://10.25.0.15/~s_bttkvn05l18d488f/capolavoro-main/app/webservices/ws_prodotti.php?action=get_products',
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_articoli.php?action=get_articoli',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    console.log(data);
                    // Creazione tabella con tutti i prodotti
                    var table = '<table class="table"><thead><tr><th class="col-1">ID Articolo</th><th>Immagine</th><th>Titolo</th><th>Pubblicato</th><th>Data pubblicazione</th><th></th></tr></thead><tbody>';
                    $.each(data, function(index, product) {
                        table += '<tr>';
                        table += '<td class="col-1">' + product.id + '</td>';
                        if (product.id_immagine) {
                            // table += '<td><img src="http://10.25.0.15/~s_bttkvn05l18d488f/capolavoro-main/app/webservices/ws_immagini.php?id=' + product.id_immagine + '" width="120"></td>';
                            table += '<td><img class="image" src="http://localhost/mydreambuild/capolavoro/app/webservices/ws_immagini.php?id=' + product.id_immagine + '" width="90"></td>';
                        } else {
                            table += '<td>No</td>';
                        }
                        table += '<td>' + product.titolo + '</td>';
                        table += '<td>' + (product.pubblicato ? 'Si' : 'No') + '</td>';
                        table += '<td>' + (product.data_pubblicazione ? product.data_pubblicazione : '') + '</td>';
                        table += '<td><a href="edit_prodotto.php?id=' + product.id_prodotto + '"><img src="../img/edit.png" alt="modifica" width="25" height="25"></a></td>';
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
        <button type="button" class="btn btn-outline-info" onclick="window.location.href='new_articolo.php'">Nuovo Articolo</button>
        <!-- Tabella con tutti i prodotti in ordine di categoria -->
        <div id="table" class="mt-3"></div>
    </div>
    
    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
