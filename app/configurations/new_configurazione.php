<!-- Pagina (in cui puoi accedere solo da loggato) dove si crea la configurazione -->

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
    <title>Nuovo prodotto</title>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>  
    <link rel="stylesheet" type="text/css" href="stile.css">  
    <script type="text/javascript">
        $(document).ready(function(){
            // Fare tutte le chiamate di get per avere i vari articoli
            $("#articolo").submit(function(event){
                event.preventDefault();

                /* La variabile da passare al webservice di inserimento
                $data = {
                    denominazione: string,
                    descrizione: string,
                    id_utente: string,
                    prezzo_totale: decimal,
                    prodotti: [2, 3, 50, 1, ...] // Ciclo qui e per ogni prodotto lo inserisce nella tabella n/n
                }
                */
                var prodottiArray = Array();
                // prodotti.push();
                prodottiArray.push($("#").val());

                var data = {
                    denominazione: $("#nome").val(),
                    descrizione: $("#").val(),
                    id_utente: <?php echo $_SESSION['username']; ?>,
                    prezzo_totale: $("#").val(),
                    prodotti: prodottiArray
                };

                $.ajax({
                    url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_articoli.php?action=insert_articolo',
                    type: 'POST',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        "Authorization": "Bearer <?php echo $token; ?>"
                    },
                    contentType: "application/json",
                    data: JSON.stringify(data),
                    success: function(result) {
                        console.log(result);
                        $("#response").html(result.Success);
                    },
                    error: function(xhr, status, error) {
                        console.error('Errore durante l\'inserimento dell\'articolo :', status, error);
                        $("#response").html("Errore durante l'inserimento dell\'articolo ");
                    }
                });
            });

            //Ritorna le cpu
            const cpuInput = $('#cpu_text');
            const cpuList = $('#cpu_risultati');

            cpuInput.on('input focus', function() {
                const searchTerm = cpuInput.val().toLowerCase();

                $.ajax({
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_prodotti.php?action=search_product&search=' + encodeURIComponent(searchTerm) + '&cat=1', // Replace with your actual URL
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>' 
                },
                success: function(data) {
                    cpuList.empty();
                    // console.log(data);
                    if(data.status) {
                        if(data.status == 'empty') {
                            // console.log("vuoto");
                            cpuList.append("Nessun risultato");
                            $('#cpu_risultati').show();
                        }
                    } else {
                        // In futuro una divisione Intel AMD
                        data.forEach(function(componente) {
                            const listItem = $('<li>');
                            listItem.html(`<a href="#" data-value="${componente.id_prodotto}"> ${componente.id_immagine ? `<img class="d-block" src="http://localhost/mydreambuild/capolavoro/app/webservices/ws_immagini.php?id=${componente.id_immagine}" width="60" alt="Immagine">` : ''}  ${componente.marca} ${componente.modello} - ${componente.prezzo}</a>`);
                            listItem.on('click', function() {
                                cpuInput.val(componente.marca + ' ' + componente.modello);
                                cpuList.empty(); // Hide results list after selection
                                $('#cpu_risultati').hide();
                            });
                            cpuList.append(listItem);
                            $('#cpu_risultati').show();
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
                }
                });
            }); 

            // Hide CPU list when clicking outside the input or list
            $(document).on('click', function(event) {
                if (!$(event.target).closest('.cpu').length) {
                    cpuList.hide();
                }
            });

            return false;
        });
    </script>
</head>
<body>
<div class="container-fluid">
    <header id="header" role="banner">
        <?php echo $navbar->getNavBar(); ?>
    </header>
    <form id="configurazione" enctype="multipart/form-data">
        <label for="nome">Nome:</label>
        <input class="form-control" type="text" name="nome" id="nome" >
        <br>
        <label for="descrizione">Descrizione:</label><br>
        <textarea class="form-control" type="text" name="descrizione" id="descrizione" ></textarea>
        <br>
        <div class="cpu">
            <label for="cpu">Cpu:</label>
            <input type="text" id="cpu_text" name="cpu_text" placeholder="Cpu">
            <ul id="cpu_risultati"></ul>
        </div><br>
        <div class="ram">
            <label for="ram">Ram:</label>
            <input type="text" id="ram_text" name="ram_text" placeholder="ram">
            <ul id="ram_risultati"></ul>
        </div>

        <br>
        <input type="submit" id="submit" name="submit" class="btn btn-outline-info" value="Salva">
        <input type="reset" class="btn btn-outline-info" value="Annulla">
    </form>
    <div id="response"></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>

<!-- 

<h4>Cerca clienti dal nome mentre si digita</h4>
    <input type="text" id="search_input" placeholder="Cerca..."><br><br>
    <div id="customer"></div>

//GET filtrato scrivendo
        $("#search_input").on("input", function() {
            var string = $(this).val();
            // La barra di ricerca contiene del testo
            if (string.trim().length > 0) {
                $.ajax({
                    url: 'http://10.25.0.15/~s_bttkvn05l18d488f/tps/webservice/webservice_officina/webservice.php?action=get_customerbyname&search=' + encodeURIComponent(string),
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json'
                    },
                    success: function(data) {
                        // Stampa i dati ricevuti
                        if(data.length === 0) {
                            $("#search_input").empty();
                            $("#customer").html("Nessun risultato");
                        }
                        else {
                            //buildTableMeccanico(data, "get_bymodel");
                            $("#customer").empty();
                            buildTableCustomer(data, "customer");
                            console.log(data);
                        }
                        $(".button").removeAttr("disabled");
                    },
                    error: function(xhr, status, error) {
                        console.error('Errore durante la richiesta:', status, error, '\ndata: ');
                        $(".button").removeAttr("disabled");
                    }

                });
            }
            else {
                $("#results").empty();
            }
        });

 -->