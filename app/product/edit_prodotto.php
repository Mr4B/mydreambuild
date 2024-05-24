<?php
session_start();
require_once('../shared/navbar.php');   
$navbar = new NavBar();
$navbar->setLogin($_SESSION['username'], $_SESSION['ruolo']);
$token = $_SESSION['jwt'];
$id = isset($_GET['id']) ? $_GET['id'] : '';
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
            var currentImage = '';
            var categoria = 'none';
            var categorie = [];

// Riprende tutte le categorie perchè mi serve il nome
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
                    // console.log(data);
                    categorie = data;
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante il recupero delle categorie: ', status, error);
                    $("#table").html("Errore");
                }
            }); 

// Riprende i valori del prodotto da modificare
            $.ajax({
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_prodotti.php?action=get_byID&id=<?php echo $id; ?>',
                // url: 'http://10.25.0.15/~s_bttkvn05l18d488f/capolavoro-main/app/webservices/ws_prodotti.php?action=get_categorie',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    categoria = getDefinizioneCategoria(categorie, data[0].id_categoria);
                    showDiv(categoria); // Mostra i div per modificare quella specifica categoria
                    popolaDati(data[0]);
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante il recupero delle categorie: ', status, error);
                    $("#table").html("Errore");
                }
            });   


            function popolaDati(data) {
                // console.log(data);
                if(data.id_immagine) {
                    currentImage = data.id_immagine;
                    $("#immagine").html(`<img src="http://localhost/mydreambuild/capolavoro/app/webservices/ws_immagini.php?id=${data.id_immagine}">`); // Stampo l'immagine se è presente
                }

                $("#marca").val(data.marca);
                $("#modello").val(data.modello);
                $("#descrizione").val(data.descrizione);
                $("#prezzo").val(data.prezzo);
                $("#link").val(data.link);

                switch (categoria.toLowerCase()) {
                    case 'cpu':
                        $("#frequenza_base").val(data.frequenza_base);
                        $("#frequenza_boost").val(data.c_frequenza_boost);
                        $("#n_core").val(data.c_n_core);
                        $("#n_thread").val(data.c_n_thread);
                        $("#consumo_energetico").val(data.c_consumo_energetico);
                        $("#dim_cache").val(data.c_dim_cache);
                        break;

                    case 'gpu':
                        $("#g_memoria").val(data.g_memoria);
                        $("#tipo_memoria").val(data.g_tipo_memoria);
                        $("#g_frequenza_base").val(data.frequenza_base);
                        $("#g_dimensioni").val(data.dimensioni);
                        break;

                    case 'ram':
                        $("#r_dimensione").val(data.r_dimensione);
                        $("#r_velocita").val(data.r_velocita);
                        $("#r_tipo").val(data.r_tipo);
                        break;
                        
                    case 'hdd':
                        $("#h_capacita").val(data.capacita_gb);
                        $("#h_fattore_forma").val(data.fattore_di_forma);
                        $("#h_velocita").val(data.a_velocita_rotazione);
                        $("#h_cache").val(data.a_cache_mb);
                        $("#h_lettura").val(data.a_velocita_lettura_mb_s);
                        $("#h_scrittura").val(data.a_velocita_scrittura_mb_s);
                        break;
                    
                    case 'ssd':
                        $("#s_capacita").val(data.capacita_gb);
                        $("#s_fattore_forma").val(data.fattore_di_forma);
                        $("#interfaccia").val(data.a_interfaccia);
                        $("#s_lettura").val(data.a_velocita_lettura_mb_s);
                        $("#s_scrittura").val(data.a_velocita_scrittura_mb_s);
                        break;

                    case 'case':
                        $("#cs_colore").val(data.cs_colore);
                        $("#dimensioni").val(data.dimensioni);
                        $("#peso").val(data.cs_peso);
                        $("#fattore_di_forma").val(data.fattore_di_forma);
                        $("#vetro").prop("checked", data.cs_finestra_laterale === 1);
                        break;

                    case 'scheda madre':

                        break;

                    case 'psu':

                        break;

                    }
            }



// Invio dei dati modificati al db (uguale al new)
            $("#dati_prodotto").submit(function(event){
                event.preventDefault();

                var data = {
                    id_prodotto: <?php echo $id; ?>,
                    marca: $("#marca").val(),
                    modello: $("#modello").val(),
                    descrizione: $("#descrizione").val(),
                    prezzo: $("#prezzo").val(),
                    link: $("#link").val()
                };

                // Cambia l'url al webservices desiderato
                var actionUrl = 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_prodotti.php?action=';
                // var actionUrl = 'http://10.25.0.15/~s_bttkvn05l18d488f/capolavoro-main/app/webservices/ws_prodotti.php?action=';


                switch (categoria.toLowerCase()) {
                    case 'cpu':
                        data.frequenza_base = $("#frequenza_base").val();
                        data.frequenza_boost = $("#frequenza_boost").val();
                        data.n_core = $("#n_core").val();
                        data.n_thread = $("#n_thread").val();
                        data.consumo_energetico = $("#consumo_energetico").val();
                        data.dim_cache = $("#dim_cache").val();
                        actionUrl += 'put_cpu';
                        // console.log(actionUrl);
                        break;

                    case 'gpu':
                        data.g_memoria = $("#g_memoria").val();
                        data.g_tipo_memoria = $("#tipo_memoria").val();
                        data.frequenza_base = $("#g_frequenza_base").val();
                        data.dimensioni = $("#g_dimensioni").val();
                        actionUrl += 'put_gpu';
                        break;

                    case 'ram':
                        data.r_dimensione = $("#r_dimensione").val();
                        data.r_velocita = $("#r_velocita").val();
                        data.r_tipo = $("#r_tipo").val();
                        actionUrl += 'put_ram';
                        break;
                        
                    case 'hdd':
                        data.capacita_gb = $("#h_capacita").val();
                        data.fattore_di_forma = $("#h_fattore_forma").val();
                        data.a_velocita_rotazione = $("#h_velocita").val();
                        data.a_cache_mb = $("#h_cache").val();
                        data.a_velocita_lettura_mb_s = $("#h_lettura").val();
                        data.a_velocita_scrittura_mb_s = $("#h_scrittura").val();
                        actionUrl += 'put_hdd';
                        break;
                    
                    case 'ssd':
                        data.capacita_gb = $("#s_capacita").val();
                        data.fattore_di_forma = $("#s_fattore_forma").val();
                        data.a_interfaccia = $("#interfaccia").val();
                        data.a_velocita_lettura_mb_s = $("#s_lettura").val();
                        data.a_velocita_scrittura_mb_s = $("#s_scrittura").val();
                        actionUrl += 'put_ssd';
                        break;

                    case 'case':
                        data.cs_colore = $("#cs_colore").val();
                        data.dimensioni = $("#dimensioni").val();
                        data.cs_peso = $("#peso").val();
                        data.fattore_di_forma = $("#fattore_di_forma").val();
                        data.cs_finestra_laterale = $("#vetro").is(":checked") ? 1 : 0;
                        actionUrl += 'put_case';
                        break;

                    case 'scheda madre':

                        actionUrl += 'put_motherboard';
                        break;

                    case 'psu':

                        actionUrl += 'put_psu';
                        break;

                    }
                    // console.log(data);

                var imageFile = $('#image')[0].files[0];

                if (imageFile) {
                    // Se entra qui significa che è stata inserita un immagine
                    var formData = new FormData();
                    formData.append('image', imageFile);

                    $.ajax({
                        url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_immagini.php?id=' + currentImage,
                        type: 'PUT',
                        data: formData,
                        processData: false,
                        contentType: false,
                        headers: {
                            "Authorization": "Bearer <?php echo $token; ?>"
                        },
                        success: function(response) {
                            if (response.id_immagine) {
                                // data.id_immagine = response.id_immagine;
                                // console.log(data);
                                modificaProdotto(data);
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
                    // data.id_immagine = currentImage;
                    modificaProdotto(data);
                }

                // In teoria non glielo devo passare per niente l'id dell'immagine perché la cambia nello stesso record, non ne aggiunge una nuova

                function modificaProdotto(data) {

                    $.ajax({
                        url: actionUrl,
                        type: 'PUT',
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
    <h2>Modifica prodotto</h2>
    <form id="dati_prodotto" enctype="multipart/form-data">
        <div id="immagine"></div>
        <label for="image">Seleziona immagine per modificarla:</label>
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
        <input type="number" step="0.01" name="prezzo" id="prezzo" required>€
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
            <label for="r_dimensione">Dimensione (GB):</label> <br>
            <input type="number" name="r_dimensione" id="r_dimensione">
            <br>
            <label for="r_velocita">Velocità (MHz):</label> <br>
            <input type="number" step="0.01" name="r_velocita" id="r_velocita">
            <br>
            <label for="r_tipo">Tipologia:</label> <br>
            <input type="text" name="r_tipo" id="r_tipo">
            <br>
        </div>

        <div id="campi_gpu" style="display: none;">
            <label for="g_memoria">Memoria (GB):</label> <br>
            <input type="number" name="g_memoria" id="g_memoria">
            <br>
            <label for="tipo_memoria">Tipologia memoria:</label> <br>
            <input type="text" name="tipo_memoria" id="tipo_memoria">
            <br>
            <label for="g_frequenza_base">Frequenza di clock (MHz):</label> <br>
            <input type="number" step="0.01" name="g_frequenza_base" id="g_frequenza_base">
            <br>
            <label for="g_dimensioni">Dimensioni:</label> <br>
            <input type="text" name="g_dimensioni" id="g_dimensioni">
            <br>
        </div>

        
        <div id="campi_hdd" style="display: none;">
            <label for="h_capacita">Capacità (GB)</label>
            <input type="number" name="h_capacita" id="h_capacita">
            <br>
            <label for="h_fattore_forma">Fattore di forma</label>
            <select name="h_fattore_forma" id="h_fattore_forma">
                <option>-- seleziona valore --</option>
                <option value="2,5">2,5 pollici</option>
                <option value="3,5">3,5 pollici</option>
            </select>
            <br>
            <label for="h_velocita">Velocità di rotazione disco rigido</label>
            <input type="number" name="h_velocita" id="h_velocita">/m
            <br>
            <label for="h_cache">Cache (MB)</label>
            <input type="number" name="h_cache" id="h_cache">
            <br>
            <label for="h_lettura">Velocità di lettura (MB/s)</label>
            <input type="number" name="h_lettura" id="h_lettura">
            <br>
            <label for="h_scrittura">Velocità di scrittura (MB/s)</label>
            <input type="number" name="h_scrittura" id="h_scrittura">
            <br>
        </div>
        
        <div id="campi_ssd" style="display: none;">
            <label for="s_capacita">Capacità (GB)</label>
            <input type="number" name="s_capacita" id="s_capacita">
            <br>
            <label for="s_fattore_forma">Fattore di forma</label>
            <select name="s_fattore_forma" id="s_fattore_forma">
                <option value="2,5">2,5 pollici</option>
                <option value="M.2">M.2</option>
                <option value="mSATA">mSATA</option>
                <option value="U.2">U.2</option>
            </select>
            <br>
            <label for="interfaccia">Interfaccia</label>
            <select name="interfaccia" id="interfaccia">
                <option value="sata">SATA</option>
                <option value="NVMe">NVMe PCIe</option>
            </select>
            <br>
            <label for="s_lettura">Velocità di lettura (MB/s)</label>
            <input type="number" name="s_lettura" id="s_lettura">
            <br>
            <label for="s_scrittura">Velocità di scrittura (MB/s)</label>
            <input type="number" name="s_scrittura" id="s_scrittura">
            <br>

        </div>
            
        <div id="campi_case" style="display: none;">
            <label for="cs_colore">Colore:</label>
            <input type="text" name="cs_colore" id="cs_colore">
            <br>
            <label for="dimensioni">Dimensioni:</label>
            <input type="text" name="dimensioni" id="dimensioni">
            <br>
            <label for="peso">Peso:</label>
            <input type="number" name="peso" id="peso">
            <br>
            <label for="fattore_di_forma">Fattore di forma scheda madre:</label>
            <select name="fattore_di_forma" id="fattore_di_forma">
                <option value="E-ATX">E-ATX</option>
                <option value="ATX">ATX</option>
                <option value="microATX">microATX</option>
                <option value="Mini-ITX">Mini-ITX</option>
            </select>
            <br>
            <label for="vetro">Vetro laterale:</label>
            <input type="checkbox" name="vetro" value="1">

        </div>

        <div id="campi_scheda madre" style="display: none;">
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

        <div id="campi_psu" style="display: none;">

        </div>

        
        
        
        <br>
        <input type="submit" id="submit" name="submit" class="btn btn-outline-info" value="Salva" disabled>
        <input type="reset" id="reset" name="reset" class="btn btn-outline-secondary" value="Annulla modifiche">
    </form>
    <div id="response"></div>
</div>
<script>
    function getDefinizioneCategoria(categorie, id) {
        const categoria = categorie.find(cat => cat.id === id);
        return categoria ? categoria.definizione : "Categoria non trovata";
    }

    function showDiv(categoria) {
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
    } 

    

</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
