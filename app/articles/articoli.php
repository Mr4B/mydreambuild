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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Script jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>    
    <style>
        .article-thumbnail {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .article-thumbnail img {
            width: 100%;
            height: auto;
        }

        .article-title {
            font-size: 1.5rem;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .article-summary {
            color: #666;
            margin-bottom: 20px;
        }

        .article-link {
            color: #007bff;
            text-decoration: none;
            font-weight: bold;
        }

        .article-link:hover {
            text-decoration: underline;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            
            $.ajax({
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_articoli.php?action=get_pubblicati',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    console.log(data);
                    const articlesContainer = $('#articoli');

                    data.forEach(function(article) {
                        const articleHTML = `
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="article-thumbnail">
                                        <img src="http://localhost/mydreambuild/capolavoro/app/webservices/ws_immagini.php?id=${article.id_immagine}" alt="Thumbnail">
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <h2 class="article-title">${article.titolo}</h2>
                                    <p class="article-summary">${article.summary}</p>
                                    <a href="../articles/read_articolo.php?id=${article.id}" class="article-link">Leggi di più</a>
                                </div>
                            </div>
                            <hr>
                        `;
                        articlesContainer.append(articleHTML);
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
                    $("#articoli").html("Errore");
                }
            });
            
        });
    </script>
</head>
<body>
    <!--Esempio di header -->
    <header id="header" class role="banner">
        <?php echo $navbar->getNavBar(); ?>
    </header>
    <!-- Corpo della pagina -->
    <div class="container-fluid">
        <div id="articoli">
            <!-- Gli articoli verranno aggiunti qui dinamicamente -->
        </div>
    </div>
    <?php $footer = new Footer(); echo $footer->getFooter(); ?>
    
    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>