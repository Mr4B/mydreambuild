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
    <style>
        .config-section {
            margin-bottom: 30px;
        }
        .config-section h4 {
            /* font-size: 1.5em; */
            margin-bottom: 10px;
            padding: 10px;
            background-color: #f8f9fa;
            border-left: 5px solid #198754;
        }
        .config-card-container {
            display: flex;
            overflow-x: auto;
            padding: 10px 0;
        }
        .config-card {
            flex: 0 0 auto;
            width: 200px;
            margin: 0 10px;
            text-align: center;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            transition: transform 0.2s;
        }
        .config-card:hover {
            transform: scale(1.05);
        }
        .config-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
        }
        .config-card .config-details {
            padding: 10px;
            background-color: white;
        }
        .config-card .config-details h5 {
            margin: 0;
            font-size: 1.2em;
        }
        .config-card .config-details p {
            margin: 5px 0 0;
            font-size: 1em;
            color: #555;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            <?php 
            if(isset($_SESSION['LogedIn']) && $_SESSION['LogedIn'] === true) { ?>
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
                    // Aggiungi codice per gestire "Le mie configurazioni" qui
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
                    console.log(data);
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
            <div class="d-flex flex-wrap">
                <!-- Configurazioni consigliate verranno aggiunte qui dinamicamente -->
            </div>
        </div>

    </div>
    
    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
