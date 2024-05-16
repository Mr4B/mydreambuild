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

    }
} else {
    // Crea il token per l'utente guest

    // Creazione del payload del token con il ruolo
    $payload = array(
        "ruolo" => 4,
        "exp" => time() + 3600*2 // Scadenza del token impostata a 2 ora (3600 secondi)
    );
    $token = $gestioneJWT->encode($payload);
    $_SESSION['jwt'] = $token;
    $_SESSION['ruolo'] = 4;
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
    <!-- Aggiorna bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css"> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" type="text/css" href="stile.css">
    <script type="text/javascript">
        $(document).ready(function(){
            $.ajax({
                // url: 'http://10.25.0.15/~s_bttkvn05l18d488f/capolavoro-main/app/webservices/ws_articoli.php?action=get_carosello',
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_articoli.php?action=get_carosello',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    console.log(data);
                    // Creazione tabella con tutti i prodotti
                   
                    $("#table").html(createCarosello(data));
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
                    $("#table").html("Errore");
                }
            });   
            
            function createCarosello(data) {
                // Per ogni articolo ritornato dalla chiamata cicla e completa il carosello
                $.each(data, function(index, product) {
                    table += '<tr>';
                    table += '<td>' + product.id_prodotto + '</td>';
                    if (product.id_immagine) {
                        // table += '<td>http://localhost/mydreambuild/capolavoro/app/webservices/ws_immagini.php?id=' + product.id_immagine + '</td>';
                        // NON SO COME RIPRENDERE LE IMMAGINI                            
                    } else {
                        table += '<td>No</td>';
                    }
                    table += '<td>' + product.marca + '</td>';
                    table += '<td>' + product.modello + '</td>';
                    table += '<td>' + product.prezzo + '</td>';
                    table += '<td><a href="edit_prodotto.php?id=' + product.id_prodotto + '"><img src="../img/edit.png" alt="modifica" width="25" height="25"></a></td>';
                    table += '</tr>';
                });
                table += '</tbody></table>';

                return table;
            }

            return false;
        });
    </script>

</head>
<body>
<!--Esempio di header -->
    <div class="container-fluid">
        <header id="header" class role="banner">
            <!--Jumbotron-->
            <div class="jumbotron jumbotron-fluid mb-2">
                <div class="container">  
                    <!-- <img src="../img/logo_mecc.avif" class="d-inline-block align-top" style="float: left; margin-right: 30px;" alt="Logo meccanico" height="75" width="70"> -->
                    <h1 class="display-4">MYDREAMBUILD</h1>
                </div>
            </div>
            <?php echo $navbar->getNavBar();?>
        </header>

        <!-- Resto della pagina -->
        <!-- Riprendere gli articoli ordinati in base alla data di pubblicazione -->

        <!--Carosello-->
        <div class="container2">
            <div id="carosello" class="carousel slide" data-ride="carousel" data-interval="1000000"> <!--data-interval = tempo di scorrimento delle immagini-->
                <!--tastini sotto le immagini-->
                <ol class="carousel-indicators">
                    <li data-target="#carosello" data-slide-to="0" class="active"></li>
                    <li data-target="#carosello" data-slide-to="1"></li>
                    <li data-target="#carosello" data-slide-to="2"></li>
                    <li data-target="#carosello" data-slide-to="3"></li>
                </ol>
                
                <!--slide che scorrono-->
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img class="d-block carosello" src="../img/articolo1.jpg" alt="Prima slide">
                        <div class="carousel-caption d-none d-md-block">
                            <h4>Nvidia RTX 4090 due volte piu’ veloce della 3090</h4>
                            <!-- Nell'href fare un webservice con get_articolo?id=
                                o rimandarlo ad una pagina (mostra articoli) passandogli l'id -->
                            <p><a href="" class="carousel-description">La RTX 4090 dopo gli ultimi leak, sembra essere potenzialmente due volte piu’ veloce di una RTX 3090</a></p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img class="d-block carosello" src="../img/RTX-3070Ti-16GB.jpg" alt="Seconda slide">
                        <div class="carousel-caption d-none d-md-block">
                            <h4>Leak: RTX 3070Ti 16GB</h4>
                            <p><a href="" class="carousel-description">Pare sia in arrivo una nuova 3070Ti con il doppio della memoria</a></p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img class="d-block carosello" src="img/reforzate.jpg" alt="Terza slide">
                        <div class="carousel-caption d-none d-md-block">
                            <h4>Reforzate</h4>
                            <p><a href="frazioni/refo/reforzate.html" class="carousel-description">Risorante "La Piadina" con i suoi magnifici piatti..</a></p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img class="d-block carosello" src="img/unionmoda.jpg" alt="Quarta slide">
                        <div class="carousel-caption d-none d-md-block">
                            <h4>Unionmoda</h4>
                            <p><a href="frazioni/pdr/piandirose.html" class="carousel-description">L'outlet più grande delle Marche a Pian di Rose..</a></p>
                        </div>
                    </div>
                </div>

                <!--freccette per scorrere all'immagine precedente e successiva-->
                <a class="carousel-control-prev" href="#carosello" role="button" data-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="sr-only"></span>
                </a>

                <a class="carousel-control-next" href="#carosello" role="button" data-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="sr-only"></span>
                </a>
            </div>
        </div>

    </div>
</body>
</html>