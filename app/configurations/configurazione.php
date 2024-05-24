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
            <?php 
            if(isset($_SESSION['LogedIn']) && $_SESSION['LogedIn'] === true) { ?>
            // Accede a questo codice solamente se l'utente è loggato
            // Le mie configurazioni
            $.ajax({
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_configurazioni.php?action=get_myconfiguration&id_utente=<?php if(isset($_SESSION['LogedIn']) && $_SESSION['LogedIn'] === true) echo $_SESSION['username']; else 'errore'; ?>',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    console.log(data);
                    const container = $('#your-configurations');
                    // console.log(data.length);
                    if(data.length) {

                        
                        const cardContainer = $('<div class="config-card-container"></div>');
                        
                        data.forEach(function(config) {
                            const imgSrc = config.id_immagine ? `http://localhost/mydreambuild/capolavoro/app/webservices/ws_immagini.php?id=${config.id_immagine}` : 'https://via.placeholder.com/150/000000/FFFFFF/?text=No+Image';
                            const card = `
                            <div class="config-card" data-id="${config.id}">
                                <div class="config-details">
                                    <h5>${config.denominazione}</h5>
                                    <p>${config.prezzo_totale}€</p>
                                </div>
                            </div>
                            `;
                            cardContainer.append(card);
                        });
                        
                        container.append(cardContainer);
                        
                        // Aggiungi l'evento click alle card
                        $('.config-card').on('click', function() {
                            const configId = $(this).data('id');
                            window.location.href = `dettagli_configurazione.php?id=${configId}`;
                        });
                    } else {
                        container.html('<p style="color:red">Nessuna configurazione</p>');
                    }
                    

                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
                    $("#table").html("Errore");
                }
            });         
            <?php }?>
            
            // Configurazioni consigliate
            $.ajax({
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_configurazioni.php?action=get_defaultconfiguration',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    // console.log(data);
                    const container = $('#quotate');
                    
                    const tipi = {};  // Oggetto per raccogliere le configurazioni per tipologia

                    data.forEach(function(config) {
                        if (!tipi[config.tipologia]) {
                            tipi[config.tipologia] = [];
                        }
                        tipi[config.tipologia].push(config);
                    });

                    for (const tipo in tipi) {
                        const section = $(`
                            <div class="config-section">
                                <h4>${tipo}</h4>
                                <div class="config-card-container"></div>
                            </div>
                        `);

                        const cardContainer = section.find('.config-card-container');
                        
                        tipi[tipo].forEach(function(config) {
                            const imgSrc = config.id_immagine ? `http://localhost/mydreambuild/capolavoro/app/webservices/ws_immagini.php?id=${config.id_immagine}` : 'https://via.placeholder.com/150/000000/FFFFFF/?text=No+Image';
                            const card = `
                                <div class="config-card" data-id="${config.id}">
                                    <img src="${imgSrc}" alt="Immagine">
                                    <div class="config-details">
                                        <h5>${config.denominazione}</h5>
                                        <p>${config.prezzo_totale}€</p>
                                    </div>
                                </div>
                            `;
                            cardContainer.append(card);
                        });

                        container.append(section);
                    }

                    // Aggiungi l'evento click alle card
                    $('.config-card').on('click', function() {
                        const configId = $(this).data('id');
                        window.location.href = `dettagli_configurazione.php?id=${configId}`;
                    });
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
        <div id="your">
            <h5>Le mie configurazioni</h5>
            <div id="your-configurations" class="config-section">
                <!-- Configurazioni personali verranno aggiunte qui dinamicamente -->
            </div>
        <?php 
            if(isset($_SESSION['LogedIn']) && $_SESSION['LogedIn'] === true) {
                echo '<button type="button" class="btn btn-outline-secondary mb-3" onclick="window.location.href=\'new_configurazione.php\'">Crea nuova</button>';   
            } else {
                echo '<a class="link" href="../user/login.php">Accedi per creare la tua configurazione</a>';   
            }
        ?>
        </div>
        <div id="quotate">
            <h5>Configurazioni consigliate</h5>
            <div class="d-flex flex-wrap">
                <!-- Configurazioni consigliate verranno aggiunte qui dinamicamente -->
            </div>
        </div>
        <?php $footer = new Footer(); echo $footer->getFooter(); ?>
    </div>
    
    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
