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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>    
    <script type="text/javascript">
        $(document).ready(function(){
            var categorie = [];
            $.ajax({
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_prodotti.php?action=get_categorie',
                // url: 'http://10.25.0.15/~s_bttkvn05l18d488f/capolavoro-main/app/webservices/ws_prodotti.php?action=get_categorie',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    categorie = data;
                    $.each(data, function(index, categoria) {
                        $("#categoria").append('<option value="' + categoria.definizione + '" id= "' + categoria.id + '">' + categoria.definizione + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante il recupero delle categorie: ', status, error);
                    $("#table").html("Errore");
                }
            });            

            $("#dati_prodotto").submit(function(event){
                event.preventDefault();
                var denominazione = $("#categoria").val();
                
                var categoriaTrovata = categorie.find(function(categoria) {
                    return categoria.definizione === denominazione;
                });

                var data = {
                    id_categoria: categoriaTrovata.id,
                    marca: $("#marca").val(),
                    modello: $("#modello").val(),
                    descrizione: $("#descrizione").val(),
                    prezzo: $("#prezzo").val(),
                    link: $("#link").val()
                };

                // Cambia l'url al webservices desiderato
                var actionUrl = 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_prodotti.php?action=';
                // var actionUrl = 'http://10.25.0.15/~s_bttkvn05l18d488f/capolavoro-main/app/webservices/ws_prodotti.php?action=';


                switch (categoriaTrovata.definizione.toLowerCase()) {
                    case 'cpu':
                        data.frequenza_base = $("#frequenza_base").val();
                        data.frequenza_boost = $("#frequenza_boost").val();
                        data.n_core = $("#n_core").val();
                        data.n_thread = $("#n_thread").val();
                        data.consumo_energetico = $("#consumo_energetico").val();
                        data.dim_cache = $("#dim_cache").val();
                        actionUrl += 'post_cpu';
                        console.log(actionUrl);
                        break;
                }

                var imageFile = $('#image')[0].files[0];

                if (imageFile) {
                    var formData = new FormData();
                    formData.append('image', imageFile);

                    $.ajax({
                        url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_immagini.php',
                        // url: 'http://10.25.0.15/~s_bttkvn05l18d488f/capolavoro-main/app/webservices/ws_immagini.php',
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
                    data.id_immagine = null;
                    inviaProdotto(data);
                }

                function inviaProdotto(data) {

                    $.ajax({
                        url: actionUrl,
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
                            console.error('Errore durante l\'inserimento del prodotto:', status, error);
                            $("#response").html("Errore durante l'inserimento del prodotto");
                        }
                    });
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
    <h2>Registra un nuovo prodotto</h2>
    <form id="dati_prodotto" enctype="multipart/form-data">
        <label for="categoria">Categoria:</label>
        <select class="form-select mt-2" name="categoria" id="categoria">
            <option selected>-- seleziona categoria --</option>
        </select>
        <hr>

        <label for="image">Seleziona immagine:</label>
        <input type="file" id="image" name="image" accept="image/*">
        <br>
        <label for="marca">Marca:</label><br>
        <input type="text" name="marca" id="marca" required>
        <br>
        <label for="modello">Modello:</label><br>
        <input type="text" name="modello" id="modello" required>
        <br>
        <label for="descrizione">Descrizione:</label><br>
        <textarea type="text" name="descrizione" id="descrizione"></textarea>
        <br>
        <label for="prezzo">Prezzo:</label><br>
        <input type="text" name="prezzo" id="prezzo" required>
        <br>
        <label for="link">Link d'acquisto:</label><br>
        <input type="text" name="link" id="link">
        <br>

        <div id="campi_cpu" style="display: none;">
            <label for="frequenza_base">Frequenza base:</label> <br>
            <input type="number" step="0.01" name="frequenza_base" id="frequenza_base">
            <br>
            <label for="frequenza_boost">Frequenza boost:</label> <br>
            <input type="number" step="0.01" name="frequenza_boost" id="frequenza_boost">
            <br>
            <label for="n_core">Numero core:</label> <br>
            <input type="number" name="n_core" id="n_core">
            <br>
            <label for="n_thread">Numero thread:</label> <br>
            <input type="number" name="n_thread" id="n_thread">
            <br>
            <label for="consumo_energetico">Consumo energetico (W):</label> <br>
            <input type="number" name="consumo_energetico" id="consumo_energetico">
            <br>
            <label for="dim_cache">Dimensione cache (MB):</label> <br>
            <input type="number" name="dim_cache" id="dim_cache">
            <br>
        </div>

        <div id="campi_ram" style="display: none;">
            <label for="dimensione">Dimensione (GB):</label> <br>
            <input type="text" name="dimensione" id="dimensione">
            <br>
            <label for="velocita">Velocità (MHz):</label> <br>
            <input type="text" name="velocita" id="velocita">
            <br>
            <label for="tipo">Tipologia:</label> <br>
            <input type="text" name="tipo" id="tipo">
            <br>
        </div>

        <div id="campi_gpu" style="display: none;">
            <label for="memoria">Memoria:</label> <br>
            <input type="text" name="memoria" id="memoria">
            <br>
            <label for="tipo_memoria">Tipologia memoria:</label> <br>
            <input type="text" name="tipo_memoria" id="tipo_memoria">
            <br>
            <label for="velocita">Velocità:</label> <br>
            <input type="text" name="velocita" id="velocita">
            <br>
            <label for="dimensioni">Dimensioni:</label> <br>
            <input type="text" name="dimensioni" id="dimensioni">
            <br>
        </div>

        <div id="campi_scheda_madre" style="display: none;">
            <label for="formato">Formato:</label> <br>
            <input type="text" name="formato" id="formato">
            <br>
            <label for="chipset">Chipset:</label> <br>
            <input type="text" name="chipset" id="chipset">
            <br>
            <label for="n_ram">Slot Ram:</label> <br>
            <input type="number" name="n_ram" id="n_ram">
            <br>
            <label for="tipo_ram">Tipologia ram:</label> <br>
            <input type="text" name="tipo_ram" id="tipo_ram">
            <br>
            <label for="pcie">Versione PCIe:</label> <br>
            <input type="text" name="pcie" id="pcie">
            <br>
        </div>

        <br>
        <input type="submit" id="submit" name="submit" class="btn btn-outline-info" value="Inserisci prodotto" disabled>
    </form>
    <div id="response"></div>
</div>
<script>
    document.getElementById("categoria").addEventListener("change", function() {
        var categoria = this.value;

        var campi = document.querySelectorAll("[id^='campi_']");
        campi.forEach(function(campo) {
            campo.style.display = "none";
        });

        const submitButton = $("#submit");

        var campiCategoria = document.getElementById("campi_" + categoria.toLowerCase());
        if (campiCategoria) {
            campiCategoria.style.display = "block";
            submitButton.prop("disabled", false);
        } else {
            submitButton.prop("disabled", true);
        }

        var inputId = ["marca", "modello", "descrizione", "prezzo", "link"];
        inputId.forEach(function(id) {
            var input = document.getElementById(id);
            if (input) {
                input.value = "";
            }
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
