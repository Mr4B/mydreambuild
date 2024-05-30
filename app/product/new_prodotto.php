<?php
session_start();
include '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'db_connect.php';
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
                url: '<?php echo $url; ?>app/webservices/ws_prodotti.php?action=get_categorie',
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
                var actionUrl = '<?php echo $url; ?>app/webservices/ws_prodotti.php?action=';


                switch (categoriaTrovata.definizione.toLowerCase()) {
                    case 'cpu':
                        data.frequenza_base = $("#frequenza_base").val();
                        data.frequenza_boost = $("#frequenza_boost").val();
                        data.n_core = $("#n_core").val();
                        data.n_thread = $("#n_thread").val();
                        data.consumo_energetico = $("#consumo_energetico").val();
                        data.dim_cache = $("#dim_cache").val();
                        data.socket = $("#c_socket").val();
                        actionUrl += 'post_cpu';
                        // console.log(actionUrl);
                        break;

                    case 'gpu':
                        data.g_memoria = $("#g_memoria").val();
                        data.g_tipo_memoria = $("#tipo_memoria").val();
                        data.frequenza_base = $("#g_frequenza_base").val();
                        data.dimensioni = $("#g_dimensioni").val();
                        actionUrl += 'post_gpu';
                        break;

                    case 'ram':
                        data.r_dimensione = $("#r_dimensione").val();
                        data.r_velocita = $("#r_velocita").val();
                        data.r_tipo = $("#r_tipo").val();
                        actionUrl += 'post_ram';
                        break;
                        
                    case 'hdd':
                        data.capacita_gb = $("#h_capacita").val();
                        data.fattore_di_forma = $("#h_fattore_forma").val();
                        data.a_velocita_rotazione = $("#h_velocita").val();
                        data.a_cache_mb = $("#h_cache").val();
                        data.a_velocita_lettura_mb_s = $("#h_lettura").val();
                        data.a_velocita_scrittura_mb_s = $("#h_scrittura").val();
                        actionUrl += 'post_hdd';
                        break;
                    
                    case 'ssd':
                        data.capacita_gb = $("#s_capacita").val();
                        data.fattore_di_forma = $("#s_fattore_forma").val();
                        data.a_interfaccia = $("#interfaccia").val();
                        data.a_velocita_lettura_mb_s = $("#s_lettura").val();
                        data.a_velocita_scrittura_mb_s = $("#s_scrittura").val();
                        actionUrl += 'post_ssd';
                        break;

                    case 'case':
                        data.cs_colore = $("#cs_colore").val();
                        data.dimensioni = $("#dimensioni").val();
                        data.cs_peso = $("#peso").val();
                        data.fattore_di_forma = $("#fattore_di_forma").val();
                        data.cs_finestra_laterale = $("#vetro").is(":checked") ? 1 : 0;
                        actionUrl += 'post_case';
                        break;

                    case 'scheda madre':
                        data.m_formato = $("#formato").val();
                        data.socket = $("#m_socket").val();
                        data.m_chipset = $("#chipset").val();
                        data.m_numero_slot_ram = $("#n_ram").val();
                        data.m_tipologia_ram = $("#tipo_ram").val();
                        data.m_version_pcie = $("#pcie").val();
                        actionUrl += 'post_motherboard';
                        break;

                    case 'psu':
                        data.fattore_di_forma = $("#p_fattore_di_forma").val(),
                        data.p_watt = $("#watt").val();
                        data.p_schema_alimentazione = $("#p_schema_alimentazione").val();
                        actionUrl += 'post_psu';
                        break;

                    }
                    // console.log(data);

                var imageFile = $('#image')[0].files[0];

                if (imageFile) {
                    console.log("Sono dentro imageFile");
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
                            window.location.href = "gestione_prodotti.php";
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
        <input type="number" step="0.01" name="prezzo" id="prezzo" required>€
        <br>
        <label for="link">Link d'acquisto:</label><br>
        <input type="text" name="link" id="link">
        <br>
        
        <div id="campi_cpu" style="display: none;">
            <label for="c_socket">Socket:</label><br>
            <input type="text" name="c_socket" id="c_socket">
            <br>
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
            <label for="m_socket">Socket:</label> <br>
            <input type="text" name="m_socket" id="m_socket">
            <br>
            <label for="chipset">Chipset:</label> <br>
            <input type="text" name="chipset" id="chipset">
            <br>
            <label for="n_ram">Numero slot Ram:</label> <br>
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
            <label for="p_fattore_di_forma">Fattore di forma:</label><br>
            <select name="p_fattore_di_forma" id="p_fattore_di_forma">
                <option value="ATX">ATX</option>
                <option value="SFX">SFX</option>
                <option value="TFX">TFX</option>
                <option value="CFX">CFX</option>
                <option value="EPS">EPS</option>
            </select>
            <br>
            <label for="watt">Watt:</label> <br>
            <input type="number" name="watt" id="watt">
            <br>
            <label for="p_schema_alimentazione">Schema di cablaggio:</label> <br>
            <select name="p_schema_alimentazione" id="p_schema_alimentazione">
                <option value="Non modulare">Non modulare</option>
                <option value="Semi-modulare">Semi-modulare</option>
                <option value="Modulare">Modulare</option>
            </select>
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
