<?php
session_start();
require_once('../shared/navbar.php'); 
require_once('../shared/footer.php');
  
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
                                        resultList.empty();
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

                $(document).on('click', function(event) {
                    if (!$(event.target).closest(componentType).length) {
                        resultList.hide();
                    }
                });
            }

            searchComponent('.cpu', '#cpu_text', '#cpu_risultati', 1);
            searchComponent('.gpu', '#gpu_text', '#gpu_risultati', 2);
            searchComponent('.ram', '#ram_text', '#ram_risultati', 3);
            searchComponent('.motherboard', '#motherboard_text', '#motherboard_risultati', 4);
            searchComponent('.psu', '#psu_text', '#psu_risultati', 5);
            searchComponent('.hdd', '#hdd_text', '#hdd_risultati', 6);
            searchComponent('.ssd', '#ssd_text', '#ssd_risultati', 7);
            searchComponent('.case', '#case_text', '#case_risultati', 8);

            $("#configurazione").submit(function(event) {
                event.preventDefault();

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
                if ($("#motherboard_text").data('value') === undefined) {
                    isValid = false;
                    alert("La Scheda madre è obbligatoria.");
                }
                if ($("#hdd_text").data('value') === undefined && $("#ssd_text").data('value') === undefined) {
                    isValid = false;
                    alert("Almeno un HDD o un SSD è obbligatorio.");
                }

                if (!isValid) {
                    return;
                }

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
                        // console.log(result);
                        $("#response").html(result.Success);
                        window.location.href = "configurazione.php";
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
        <div class="mb-3">
            <label for="nome" class="form-label">Nome:</label>
            <input class="form-control" type="text" name="nome" id="nome" required>
        </div>
        <div class="mb-3">
            <label for="descrizione" class="form-label">Descrizione:</label>
            <textarea class="form-control" type="text" name="descrizione" id="descrizione"></textarea>
        </div>
        <div class="mb-3 component-group cpu">
            <label for="cpu" class="form-label">CPU:</label>
            <input class="form-control" type="text" id="cpu_text" name="cpu_text" placeholder="Nessuna cpu" required>
            <ul id="cpu_risultati"></ul>
        </div>
        <div class="mb-3 component-group motherboard">
            <label for="motherboard" class="form-label">Scheda madre:</label>
            <input class="form-control" type="text" id="motherboard_text" name="motherboard_text" placeholder="Nessuna scheda madre" required>
            <ul id="motherboard_risultati"></ul>
        </div>
        <div class="mb-3 component-group ram">
            <label for="ram" class="form-label">RAM:</label>
            <input class="form-control" type="text" id="ram_text" name="ram_text" placeholder="Nessuna ram" required>
            <ul id="ram_risultati"></ul>
        </div>
        <div class="mb-3 component-group gpu">
            <label for="gpu" class="form-label">GPU:</label>
            <input class="form-control" type="text" id="gpu_text" name="gpu_text" placeholder="Nessuna gpu" required>
            <ul id="gpu_risultati"></ul>
        </div>
        <div class="mb-3 component-group hdd">
            <label for="hdd" class="form-label">HDD:</label>
            <input class="form-control" type="text" id="hdd_text" name="hdd_text" placeholder="Nessun hdd">
            <ul id="hdd_risultati"></ul>
        </div>
        <div class="mb-3 component-group ssd">
            <label for="ssd" class="form-label">SSD:</label>
            <input class="form-control" type="text" id="ssd_text" name="ssd_text" placeholder="Nessuna ssd">
            <ul id="ssd_risultati"></ul>
        </div>
        <div class="mb-3 component-group case">
            <label for="case" class="form-label">CASE:</label>
            <input class="form-control" type="text" id="case_text" name="case_text" placeholder="Nessuna case">
            <ul id="case_risultati"></ul>
        </div>
        <div class="mb-3 component-group psu">
            <label for="psu" class="form-label">Alimentatore:</label>
            <input class="form-control" type="text" id="psu_text" name="psu_text" placeholder="Nessun alimentatore">
            <ul id="psu_risultati"></ul>
        </div>
        <div class="mb-3">
            <input type="submit" id="submit" name="submit" class="btn btn-primary" value="Salva">
            <input type="reset" class="btn btn-secondary" value="Annulla">
        </div>
    </form>
    <div id="response" class="mb-4"></div>
    <?php $footer = new Footer(); echo $footer->getFooter(); ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>

