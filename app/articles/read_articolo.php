<?php
session_start();
require_once('../shared/navbar.php');   
require_once('../shared/footer.php');
include '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'db_connect.php';
$navbar = new NavBar();
if (isset($_SESSION['LogedIn']) && $_SESSION['LogedIn'] === true) {
    // Esegue quest'azione solo se l'utente è loggato
    $navbar->setLogin($_SESSION['username'], $_SESSION['ruolo']);
}
$token = $_SESSION['jwt'];
$id = isset($_GET['id']) ? $_GET['id'] : '';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articoli sui Prodotti Informatici</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Script jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>    
    <style>
        body {
            background-color: #f8f9fa; /* Colore di sfondo leggero */
        }
        .article-container {
            background-color: #ffffff; /* Sfondo bianco per l'articolo */
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }
        .article-container h1 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }
        .article-container img {
            max-width: 100%;
            height: auto;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .article-container p {
            font-size: 1.1rem;
            line-height: 1.6;
        }
        footer {
            margin-top: 3rem;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $.ajax({
                url: '<?php echo $url; ?>app/webservices/ws_articoli.php?action=get_articolo&id=' + <?php echo $id; ?>,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    // console.log(data[0].titolo);
                    $("#body").html(articolo(data[0]));
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
                    $("#body").html("Errore");
                }
            });            
            
            function articolo(data) {
                console.log(data);
                var corpo = `<div class='article-container'>
                    <h1>${data.titolo}</h1>
                    <img src="<?php echo $url; ?>app/webservices/ws_immagini.php?id=${data.id_immagine}" alt="Slide">
                    <p>${data.testo}</p>
                </div>`;
                return corpo;
            }

            return false;
        });
    </script>
</head>
<body>
    <div class="container-fluid">
        <header id="header" class="mb-3" role="banner">
            <?php echo $navbar->getNavBar(); ?>
        </header>
        <!-- Corpo della pagina -->
        <div id="body" class="container"></div>
        <?php $footer = new Footer(); echo $footer->getFooter(); ?>
    </div>
    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
