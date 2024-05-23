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

    } else {
        $token = $_SESSION['jwt'];
    }
} else {
    if(!isset($_SESSION['jwt'])) {
        // Crea il token per l'utente guest
    
        // Creazione del payload del token con il ruolo
        $payload = array(
            "ruolo" => 4,
            "exp" => time() + 3600*2 // Scadenza del token impostata a 2 ora (3600 secondi)
        );
        $token = $gestioneJWT->encode($payload);
        $_SESSION['jwt'] = $token;
        $_SESSION['ruolo'] = 4;
    } else {
        $token = $_SESSION['jwt'];
    }

    $gestioneJWT->validate($token);
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="stile_home.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            // Carosello
            $.ajax({
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_articoli.php?action=get_carosello',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    $("#carousel-inner").html(createCarosello(data));
                    $('#carosello').carousel();
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
                    $("#carousel-inner").html("Errore durante il recupero dei dati del carosello.");
                }
            });

            function createCarosello(data) {
                if (!data || !data.length) {
                    return '<div>Errore durante il recupero dei dati</div>';
                }

                var carouselHTML = '';
                var counter = 0;

                data.forEach(function(product) {
                    var activeClass = counter === 0 ? 'active' : '';
                    counter++;

                    carouselHTML += `
                    <div class="carousel-item ${activeClass}">
                        <img class="d-block w-100" 
                            src="http://localhost/mydreambuild/capolavoro/app/webservices/ws_immagini.php?id=${product.id_immagine}" width="100" alt="Slide">
                        <div class="carousel-caption d-none d-md-block">
                            <h4>${product.titolo}</h4>
                            <p><a href="../articles/read_articolo.php?id=${product.id}" class="carousel-description">${product.summary}</a></p>
                        </div>
                    </div>
                    `;
                });
                return carouselHTML;
            }

            // Build consigliate
            $.ajax({
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_configurazioni.php?action=get_defaultconfiguration',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    const container = $('#recommended-builds');
                    const selectedBuilds = data.slice(0, 8);

                    selectedBuilds.forEach(function(config, index) {
                        const imgSrc = config.id_immagine ? `http://localhost/mydreambuild/capolavoro/app/webservices/ws_immagini.php?id=${config.id_immagine}` : 'https://via.placeholder.com/150/000000/FFFFFF/?text=No+Image';
                        const card = `
                            <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                                <div class="config-card" data-id="${config.id}">
                                    <img  src="${imgSrc}" alt="Immagine">
                                    <div class="config-details">
                                        <h5>${config.denominazione}</h5>
                                        <p>${config.prezzo_totale}€</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        container.append(card);
                    });

                    // Aggiungi l'evento click alle card
                    $('.config-card').on('click', function() {
                        const configId = $(this).data('id');
                        window.location.href = `../configurations/dettagli_configurazione.php?id=${configId}`;
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
                    $("#recommended-builds").html("Errore durante il recupero delle configurazioni consigliate.");
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
            <?php echo $navbar->getNavBar();?>
        </header>

        <!-- Carosello -->
        <div class="container2">
            <div id="carosello" class="carousel" data-ride="carousel" data-interval="100000">
                <div id="carousel-inner" class="carousel-inner"></div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carosello" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Precedente</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carosello" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Successivo</span>
                </button>
            </div>
        </div>

        <!-- Build Consigliate -->
        <div class="container config-section">
            <h4>Build consigliate</h4>
            <div id="recommended-builds" class="row">
                <!-- Configurazioni consigliate verranno aggiunte qui dinamicamente -->
            </div>
        </div>
    </div>

</body>
</html>
