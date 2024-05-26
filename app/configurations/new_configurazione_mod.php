<?php
session_start();
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
    <title>Nuovo prodotto</title>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
        }
        form {
            background-color: #fff;
            padding: 2rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-top: 2rem;
        }
        .form-control {
            margin-bottom: 1rem;
        }
        .component-group {
            position: relative;
        }
        .component-group ul {
            list-style-type: none;
            padding-left: 0;
            position: absolute;
            background-color: #fff;
            border: 1px solid #ddd;
            max-height: 200px;
            overflow-y: auto;
            width: 100%;
            display: none;
            z-index: 1000;
            margin-top: 0.25rem;
        }
        .component-group ul li {
            padding: 0.5rem;
            cursor: pointer;
        }
        .component-group ul li:hover {
            background-color: #f1f1f1;
        }
        .component-group ul li a {
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
        }
        .component-group ul li a img {
            margin-right: 0.5rem;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function() {

            var tipologie = [];
            $.ajax({
                url: '<?php echo $url; ?>app/webservices/ws_configurazioni.php?action=get_tipologie',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    tipologie = data;
                    $.each(data, function(index, tipologia) {
                        $("#tipologia").append('<option value="' + tipologia.denominazione + '" >' + tipologia.denominazione + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante il recupero delle categorie: ', status, error);
                    $("#table").html("Errore");
                }
            });     


            // Funzione generica per la ricerca delle componenti
            var totale = 0;

            function searchComponent(componentType, inputSelector, resultListSelector, category) {
                const input = $(inputSelector);
                const resultList = $(resultListSelector);

                input.on('input focus', function() {
                    const searchTerm = input.val().toLowerCase();

                    $.ajax({
                        url: `<?php echo $url; ?>app/webservices/ws_prodotti.php?action=search_product&search=${encodeURIComponent(searchTerm)}&cat=${category}`,
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
                                    listItem.html(`<a href="#" data-value="${componente.id_prodotto}">${componente.id_immagine ? `<img class="d-block" src="<?php echo $url; ?>app/webservices/ws_immagini.php?id=${componente.id_immagine}" width="60" alt="Immagine">` : ''} ${componente.marca} ${componente.modello} - ${componente.prezzo}€</a>`);
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
            searchComponent('.motherboard', '#motherboard_text', '#motherboard_risultati', 4);
            searchComponent('.psu', '#psu_text', '#psu_risultati', 5);
            searchComponent('.hdd', '#hdd_text', '#hdd_risultati', 6);
            searchComponent('.ssd', '#ssd_text', '#ssd_risultati', 7);
            searchComponent('.case', '#case_text', '#case_risultati', 8);
            // Scheda madre - PSU

            // Logiche per il form
            $("#configurazione").submit(function(event) {
                event.preventDefault();

                var prodottiArray = [];
                if ($("#cpu_text").data('value') !== undefined) prodottiArray.push($("#cpu_text").data('value'));
                if ($("#ram_text").data('value') !== undefined) prodottiArray.push($("#ram_text").data('value'));
                if ($("#gpu_text").data('value') !== undefined) prodottiArray.push($("#gpu_text").data('value'));
                if ($("#hdd_text").data('value') !== undefined) prodottiArray.push($("#hdd_text").data('value'));
                if ($("#ssd_text").data('value') !== undefined) prodottiArray.push($("#ssd_text").data('value'));
                if ($("#case_text").data('value') !== undefined) prodottiArray.push($("#case_text").data('value'));
                if ($("#motherboard_text").data('value') !== undefined) prodottiArray.push($("#motherboard_text").data('value'));
                if ($("#psu_text").data('value') !== undefined) prodottiArray.push($("#psu_text").data('value'));

                var data = {
                    denominazione: $("#nome").val(),
                    descrizione: $("#descrizione").val(),
                    id_utente: "<?php echo $_SESSION['username']; ?>",
                    prezzo_totale: totale,
                    prodotti: prodottiArray,
                    tipologia: $("#tipologia").val(),
                };
                // console.log(data)

                // Per l'eventuale caricamento dell'immagine
                var imageFile = $('#image')[0].files[0];

                if (imageFile) {
                    var formData = new FormData();
                    formData.append('image', imageFile);

                    $.ajax({
                        url: '<?php echo $url; ?>app/webservices/ws_immagini.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            "Authorization": "Bearer <?php echo $token; ?>"
                        },
                        success: function(response) {
                            if (response.id_immagine) {
                                data.id_immagine = response.id_immagine;
                                // console.log(data);
                                inviaProdotto(data);
                            } else {
                                console.error('Errore durante il caricamento dell\'immagine:', response.errore);
                                $("#response").html("Errore durante il caricamento dell'immagine");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Errore durante il caricamento dell\'immagine:', status, error);
                            $("#response").html("Errore durante il caricamento dell'immagine");
                        }
                    });
                } else {
                    data.id_immagine = '';
                    inviaProdotto(data);
                }
                // 

                function inviaProdotto(data) {
                    $.ajax({
                        url: '<?php echo $url; ?>app/webservices/ws_configurazioni.php?action=post_configurazione_mod',
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
                }
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
        <label for="tipologia">Tipologia:</label><br>
        <select name="tipologia" id="tipologia">
            <option selected>-- seleziona tipologia --</option>
        </select><br><br>

        <label for="image">Seleziona immagine:</label>
        <input type="file" id="image" name="image" accept="image/*">
        <br><br>
        <div class="mb-3 component-group cpu">
            <label for="cpu">CPU:</label>
            <input class="form-control" type="text" id="cpu_text" name="cpu_text" placeholder="Nessuna cpu" required>
            <ul id="cpu_risultati"></ul>
        </div>
        <div class="mb-3 component-group ram">
            <label for="ram">RAM:</label>
            <input class="form-control" type="text" id="ram_text" name="ram_text" placeholder="Nessuna ram" required>
            <ul id="ram_risultati"></ul>
        </div>
        <div class="mb-3 component-group gpu">
            <label for="gpu">GPU:</label>
            <input class="form-control" type="text" id="gpu_text" name="gpu_text" placeholder="Nessuna gpu" required>
            <ul id="gpu_risultati"></ul>
        </div>
        <div class="mb-3 component-group hdd">
            <label for="hdd">HDD:</label>
            <input class="form-control" type="text" id="hdd_text" name="hdd_text" placeholder="Nessun hdd">
            <ul id="hdd_risultati"></ul>
        </div>
        <div class="mb-3 component-group ssd">
            <label for="ssd">SSD:</label>
            <input class="form-control" type="text" id="ssd_text" name="ssd_text" placeholder="Nessuna ssd">
            <ul id="ssd_risultati"></ul>
        </div>
        <div class="mb-3 component-group case">
            <label for="case">CASE:</label>
            <input class="form-control" type="text" id="case_text" name="case_text" placeholder="Nessuna case" required>
            <ul id="case_risultati"></ul>
        </div>
        <div class="mb-3 component-group motherboard">
            <label for="motherboard" class="form-label">Scheda madre:</label>
            <input class="form-control" type="text" id="motherboard_text" name="motherboard_text" placeholder="Nessuna scheda madre" required>
            <ul id="motherboard_risultati"></ul>
        </div>
        <div class="mb-3 component-group psu">
            <label for="psu" class="form-label">Alimentatore:</label>
            <input class="form-control" type="text" id="psu_text" name="psu_text" placeholder="Nessun alimentatore" required>
            <ul id="psu_risultati"></ul>
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
