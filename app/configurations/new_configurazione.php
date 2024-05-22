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
    <link rel="stylesheet" type="text/css" href="stile.css">  
    <script type="text/javascript">
        $(document).ready(function() {
            // Funzione generica per la ricerca delle componenti
            var totale = 0;

            function searchComponent(componentType, inputSelector, resultListSelector, category) {
                const input = $(inputSelector);
                const resultList = $(resultListSelector);

                input.on('input focus', function() {
                    const searchTerm = input.val().toLowerCase();

                    $.ajax({
                        url: `http://localhost/mydreambuild/capolavoro/app/webservices/ws_prodotti.php?action=search_product&search=${encodeURIComponent(searchTerm)}&cat=${category}`,
                        type: 'GET',
                        dataType: 'json',
                        headers: {
                            'Accept': 'application/json',
                            'Authorization': 'Bearer <?php echo $token; ?>'
                        },
                        success: function(data) {
                            resultList.empty();
                            if (data.status && data.status === 'empty') {
                                resultList.append("<li>Nessun risultato</li>");
                                resultList.show();
                            } else {
                                data.forEach(function(componente) {
                                    const listItem = $('<li>');
                                    listItem.html(`<a href="#" data-value="${componente.id_prodotto}">${componente.id_immagine ? `<img class="d-block" src="http://localhost/mydreambuild/capolavoro/app/webservices/ws_immagini.php?id=${componente.id_immagine}" width="60" alt="Immagine">` : ''} ${componente.marca} ${componente.modello} - ${componente.prezzo}€</a>`);
                                    listItem.on('click', function() {
                                        input.val(`${componente.marca} ${componente.modello} - ${componente.prezzo}€`);
                                        input.data('value', componente.id_prodotto);
                                        totale += parseFloat(componente.prezzo);
                                        resultList.empty(); // Nasconde la lista dei risultati dopo la selezione
                                        resultList.hide();
                                    });
                                    resultList.append(listItem);
                                    resultList.show();
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Errore durante la richiesta:', status, error);
                        }
                    });
                });

                // Nasconde la lista dei risultati quando si clicca fuori dall'input o dalla lista
                $(document).on('click', function(event) {
                    if (!$(event.target).closest(componentType).length) {
                        resultList.hide();
                    }
                });
            }

            // Chiama la funzione per ciascuna componente
            searchComponent('.cpu', '#cpu_text', '#cpu_risultati', 1);
            searchComponent('.gpu', '#gpu_text', '#gpu_risultati', 2);
            searchComponent('.ram', '#ram_text', '#ram_risultati', 3);
            searchComponent('.hdd', '#hdd_text', '#hdd_risultati', 6);
            searchComponent('.ssd', '#ssd_text', '#ssd_risultati', 7);
            searchComponent('.case', '#case_text', '#case_risultati', 8);
            // Scheda madre - PSU

            // Logiche per il form
            $("#configurazione").submit(function(event) {
                event.preventDefault();

                // Verifica che tutti i campi obbligatori siano compilati
                let isValid = true;
                if ($("#cpu_text").data('value') === undefined) {
                    isValid = false;
                    alert("La CPU è obbligatoria.");
                }
                if ($("#ram_text").data('value') === undefined) {
                    isValid = false;
                    alert("La RAM è obbligatoria.");
                }
                if ($("#gpu_text").data('value') === undefined) {
                    isValid = false;
                    alert("La GPU è obbligatoria.");
                }
                if ($("#hdd_text").data('value') === undefined && $("#ssd_text").data('value') === undefined) {
                    isValid = false;
                    alert("Almeno un HDD o un SSD è obbligatorio.");
                }

                if (!isValid) {
                    return; // Interrompe l'invio del form se i campi obbligatori non sono compilati
                }

                var prodottiArray = [];
                if ($("#cpu_text").data('value') !== undefined) prodottiArray.push($("#cpu_text").data('value'));
                if ($("#ram_text").data('value') !== undefined) prodottiArray.push($("#ram_text").data('value'));
                if ($("#gpu_text").data('value') !== undefined) prodottiArray.push($("#gpu_text").data('value'));
                if ($("#hdd_text").data('value') !== undefined) prodottiArray.push($("#hdd_text").data('value'));
                if ($("#ssd_text").data('value') !== undefined) prodottiArray.push($("#ssd_text").data('value'));
                if ($("#case_text").data('value') !== undefined) prodottiArray.push($("#case_text").data('value'));

                var data = {
                    denominazione: $("#nome").val(),
                    descrizione: $("#descrizione").val(),
                    id_utente: "<?php echo $_SESSION['username']; ?>",
                    prezzo_totale: totale,
                    prodotti: prodottiArray
                };
                console.log(data)

                $.ajax({
                    url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_configurazioni.php?action=post_configurazione',
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
                        $("#response").html("Errore durante l'inserimento dell'articolo");
                    }
                });
            });
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
        <input class="form-control" type="text" name="nome" id="nome" required>
        <br>
        <label for="descrizione">Descrizione:</label><br>
        <textarea class="form-control" type="text" name="descrizione" id="descrizione"></textarea>
        <br>
        <div class="cpu">
            <label for="cpu">CPU:</label>
            <input type="text" id="cpu_text" name="cpu_text" placeholder="Nessuna cpu" required>
            <ul id="cpu_risultati"></ul>
        </div>
        <div class="ram">
            <label for="ram">RAM:</label>
            <input type="text" id="ram_text" name="ram_text" placeholder="Nessuna ram" required>
            <ul id="ram_risultati"></ul>
        </div>
        <div class="gpu">
            <label for="gpu">GPU:</label>
            <input type="text" id="gpu_text" name="gpu_text" placeholder="Nessuna gpu" required>
            <ul id="gpu_risultati"></ul>
        </div>
        <div class="hdd">
            <label for="hdd">HDD:</label>
            <input type="text" id="hdd_text" name="hdd_text" placeholder="Nessun hdd">
            <ul id="hdd_risultati"></ul>
        </div>
        <div class="ssd">
            <label for="ssd">SSD:</label>
            <input type="text" id="ssd_text" name="ssd_text" placeholder="Nessuna ssd">
            <ul id="ssd_risultati"></ul>
        </div>
        <div class="case">
            <label for="case">CASE:</label>
            <input type="text" id="case_text" name="case_text" placeholder="Nessuna case" required>
            <ul id="case_risultati"></ul>
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