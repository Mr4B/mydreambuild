<!-- Pagina per visualizzare una configurazione specifica dato il suo id -->

<?php
session_start();
require_once('../shared/navbar.php');   
include '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'db_connect.php';
$navbar = new NavBar();
if(isset($_SESSION['LogedIn']) && $_SESSION['LogedIn'] === true) 
    // Esegue quest'azione sol se l'utente è loggato
    $navbar->setLogin($_SESSION['username'], $_SESSION['ruolo']);
$token = $_SESSION['jwt'];
$id = isset($_GET['id']) ? $_GET['id'] : ''; // Riprende l'id corretto
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
    <script type="text/javascript">
        $(document).ready(function(){
    $.ajax({
        url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_configurazioni.php?action=get_byid&id=' + <?php echo $id; ?>,
        type: 'GET',
        dataType: 'json',
        headers: {
            'Accept': 'application/json',
            'Authorization': 'Bearer <?php echo $token; ?>'
        },
        success: function(data) {
            $("#corpo").html(buildPage(data[0]));
        },
        error: function(xhr, status, error) {
            console.error('Errore durante la richiesta:', status, error);
            $("#corpo").html("Errore");
        }
    });

    function buildPage(data) {
        var html = `
            ${data.tipologia ? `<div class="tipologia">${data.tipologia}</div>` : ''}
            <h2>${data.denominazione}</h2>
            <p class="descrizione">${data.descrizione}</p><hr>
            <div id="prodotti"></div>
            <p class="text-end">Totale: <strong>${data.prezzo_totale}€</strong></p>
        `;

        $("#corpo").html(html);

        data.prodotti.forEach(element => {
            $.ajax({
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_prodotti.php?action=get_byID&id=' + element.id_prodotto,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(result) {
                    $("#prodotti").append(buildProdotto(result[0]));
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
                    $("#corpo").html("Errore");
                }
            });
        });

        return html;
    }

    function buildProdotto(prodotto) {
        const imgSrc = prodotto.id_immagine ? `http://localhost/mydreambuild/capolavoro/app/webservices/ws_immagini.php?id=${prodotto.id_immagine}` : 'https://via.placeholder.com/150/000000/FFFFFF/?text=No+Image';
        const html = `
            <div class="prodotto d-flex align-items-center">
                <a href="../product/dettagli_prodotto.php?id=${prodotto.id}" class="text-decoration-none text-dark">
                    <img src="${imgSrc}" alt="Immagine">
                    <div class="details">
                        <h5>${prodotto.marca} ${prodotto.modello}</h5>
                        <p>${prodotto.prezzo}€</p>
                    </div>
                </a>
            </div>
        `;
        return html;
    }

    return false;
});
    </script>
</head>
<body>
    <div class="container-fluid">
        <header id="header" class role="banner">
            <?php echo $navbar->getNavBar(); ?>
        </header>
        <!-- Corpo della pagina -->
        <div id="corpo" class="container"></div>
        
    </div>
    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
