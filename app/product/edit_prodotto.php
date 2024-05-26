<?php
session_start();
include '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'db_connect.php';
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>  
    <style>
        body {
            background-color: #f8f9fa;
        }
        .form-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
        }
        .form-group img {
            max-width: 100%;
            height: 200px;
        }
        .btn-custom {
            width: 100%;
        }
        .hidden {
            display: none;
        }
    </style>  
    <script type="text/javascript">
        $(document).ready(function(){
            var currentImage = '';
            var categoria = 'none';
            var categorie = [];

            // Funzione per ottenere le categorie
            function getCategorie() {
                return $.ajax({
                    url: '<?php echo $url; ?>app/webservices/ws_prodotti.php?action=get_categorie',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer <?php echo $token; ?>'
                    },
                    success: function(data) {
                        categorie = data;
                    },
                    error: function(xhr, status, error) {
                        console.error('Errore durante il recupero delle categorie: ', status, error);
                        $("#table").html("Errore");
                    }
                });
            }

            // Funzione per ottenere i valori del prodotto da modificare
            function getProdotto() {
                return $.ajax({
                    url: '<?php echo $url; ?>app/webservices/ws_prodotti.php?action=get_byID&id=<?php echo $id; ?>',
                    type: 'GET',
                    dataType: 'json',
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer <?php echo $token; ?>'
                    },
                    success: function(data) {
                        // Ritornare i dati per utilizzarli dopo
                        return data;
                    },
                    error: function(xhr, status, error) {
                        console.error('Errore durante il recupero delle categorie: ', status, error);
                        $("#table").html("Errore");
                    }
                });
            }

            // Esecuzione delle chiamate AJAX in sequenza
            getCategorie().then(function() {
                return getProdotto();
            }).then(function(data) {
                categoria = getDefinizioneCategoria(categorie, data[0].id_categoria);
                showDiv(categoria); // Mostra i div per modificare quella specifica categoria
                popolaDati(data[0]);
            }).catch(function(error) {
                console.error('Errore durante il processo: ', error);
            });

            function popolaDati(data) {
                // console.log(data);
                if(data.id_immagine) {
                    currentImage = data.id_immagine;
                    $("#immagine").html(`<img src="<?php echo $url; ?>app/webservices/ws_immagini.php?id=${data.id_immagine}">`); // Stampo l'immagine se è presente
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
                        $("#formato").val(data.m_formato);
                        $("#m_socket").val(data.socket);
                        $("#chipset").val(data.m_chipset);
                        $("#n_ram").val(data.m_numero_slot_ram);
                        $("#tipo_ram").val(data.m_tipologia_ram);
                        $("#pcie").val(data.m_version_pcie);
                        break;

                    case 'psu':
                        $("#p_fattore_di_forma").val(data.fattore_di_forma);
                        $("#watt").val(data.p_watt);
                        $("#p_schema_alimentazione").val(data.p_schema_alimentazione);
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
                var actionUrl = '<?php echo $url; ?>app/webservices/ws_prodotti.php?action=';


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
                        data.m_formato = $("#formato").val();
                        data.socket = $("#m_socket").val();
                        data.m_chipset = $("#chipset").val();
                        data.m_numero_slot_ram = $("#n_ram").val();
                        data.m_tipologia_ram = $("#tipo_ram").val();
                        data.m_version_pcie = $("#pcie").val();
                        actionUrl += 'put_motherboard';
                        break;

                    case 'psu':
                        data.fattore_di_forma = $("#p_fattore_di_forma").val(),
                        data.p_watt = $("#watt").val();
                        data.p_schema_alimentazione = $("#p_schema_alimentazione").val();
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
                        url: '<?php echo $url; ?>app/webservices/ws_immagini.php?id=' + currentImage,
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


            $("#delete").click(function(){
                if (confirm('Sei sicuro di voler eliminare questo record?')) {
                    $.ajax({
                        url: '<?php echo $url; ?>app/webservices/ws_prodotti.php?id=<?php echo $id; ?>',
                        type: 'DELETE',
                        headers: {
                            'Authorization': 'Bearer <?php echo $token; ?>'
                        },
                        success: function(result) {
                            alert(result.message);
                            // Ricarica la pagina o esegui altre azioni necessarie dopo l'eliminazione
                            // window.location.reload();
                        },
                        error: function(xhr, status, error) {
                            alert('Errore durante l\'eliminazione del record: ' + xhr.responseText);
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
    <div class="row justify-content-center">
    <div class="col-lg-8 col-md-10 form-container form-group">
    <h2 class="text-center mb-4">Modifica prodotto</h2>
    <form id="dati_prodotto" enctype="multipart/form-data">
        <div id="immagine"></div>
        <label class="form-label" for="image">Seleziona immagine per modificarla:</label>
        <input class="form-control" type="file" id="image" name="image" accept="image/*">
        <br>
        <label class="form-label" for="marca">Marca:</label><br>
        <input class="form-control" type="text" name="marca" id="marca" required>
        <br>
        <label class="form-label" for="modello">Modello:</label><br>
        <input class="form-control" type="text" name="modello" id="modello" required>
        <br>
        <label class="form-label" for="descrizione">Descrizione:</label><br>
        <textarea class="form-control" type="text" name="descrizione" id="descrizione"></textarea>
        <br>
        <label class="form-label" for="prezzo">Prezzo:</label><br>
        <input class="form-control" type="number" step="0.01" name="prezzo" id="prezzo" required>
        <br>
        <label class="form-label" for="link">Link d'acquisto:</label><br>
        <input class="form-control" type="text" name="link" id="link">
        <br>

        <div id="campi_cpu" style="display: none;">
            <label class="form-label" for="frequenza_base">Frequenza base:</label> <br>
            <input class="form-control" type="number" step="0.01" name="frequenza_base" id="frequenza_base">
            <br>
            <label class="form-label" for="frequenza_boost">Frequenza boost:</label> <br>
            <input class="form-control" type="number" step="0.01" name="frequenza_boost" id="frequenza_boost">
            <br>
            <label class="form-label" for="n_core">Numero core:</label> <br>
            <input class="form-control" type="number" name="n_core" id="n_core">
            <br>
            <label class="form-label" for="n_thread">Numero thread:</label> <br>
            <input class="form-control" type="number" name="n_thread" id="n_thread">
            <br>
            <label class="form-label" for="consumo_energetico">Consumo energetico (W):</label> <br>
            <input class="form-control" type="number" name="consumo_energetico" id="consumo_energetico">
            <br>
            <label class="form-label" for="dim_cache">Dimensione cache (MB):</label> <br>
            <input class="form-control" type="number" name="dim_cache" id="dim_cache">
            <br>
        </div>

        <div id="campi_ram" style="display: none;">
            <label class="form-label" for="r_dimensione">Dimensione (GB):</label> <br>
            <input class="form-control" type="number" name="r_dimensione" id="r_dimensione">
            <br>
            <label class="form-label" for="r_velocita">Velocità (MHz):</label> <br>
            <input class="form-control" type="number" step="0.01" name="r_velocita" id="r_velocita">
            <br>
            <label class="form-label" for="r_tipo">Tipologia:</label> <br>
            <input class="form-control" type="text" name="r_tipo" id="r_tipo">
            <br>
        </div>

        <div id="campi_gpu" style="display: none;">
            <label class="form-label" for="g_memoria">Memoria (GB):</label> <br>
            <input class="form-control" type="number" name="g_memoria" id="g_memoria">
            <br>
            <label class="form-label" for="tipo_memoria">Tipologia memoria:</label> <br>
            <input class="form-control" type="text" name="tipo_memoria" id="tipo_memoria">
            <br>
            <label class="form-label" for="g_frequenza_base">Frequenza di clock (MHz):</label> <br>
            <input class="form-control" type="number" step="0.01" name="g_frequenza_base" id="g_frequenza_base">
            <br>
            <label class="form-label" for="g_dimensioni">Dimensioni:</label> <br>
            <input class="form-control" type="text" name="g_dimensioni" id="g_dimensioni">
            <br>
        </div>

        
        <div id="campi_hdd" style="display: none;">
            <label class="form-label" for="h_capacita">Capacità (GB):</label>
            <input class="form-control" type="number" name="h_capacita" id="h_capacita">
            <br>
            <label class="form-label" for="h_fattore_forma">Fattore di forma:</label>
            <select class="form-select" name="h_fattore_forma" id="h_fattore_forma">
                <option>-- seleziona valore --</option>
                <option value="2,5">2,5 pollici</option>
                <option value="3,5">3,5 pollici</option>
            </select>
            <br>
            <label class="form-label" for="h_velocita">Velocità di rotazione disco rigido/m:</label>
            <input class="form-control" type="number" name="h_velocita" id="h_velocita">
            <br>
            <label class="form-label" for="h_cache">Cache (MB):</label>
            <input class="form-control" type="number" name="h_cache" id="h_cache">
            <br>
            <label class="form-label" for="h_lettura">Velocità di lettura (MB/s):</label>
            <input class="form-control" type="number" name="h_lettura" id="h_lettura">
            <br>
            <label class="form-label" for="h_scrittura">Velocità di scrittura (MB/s):</label>
            <input class="form-control" type="number" name="h_scrittura" id="h_scrittura">
            <br>
        </div>
        
        <div id="campi_ssd" style="display: none;">
            <label class="form-label" for="s_capacita">Capacità (GB):</label>
            <input class="form-control" type="number" name="s_capacita" id="s_capacita">
            <br>
            <label class="form-label" for="s_fattore_forma">Fattore di forma:</label>
            <select class="form-select" name="s_fattore_forma" id="s_fattore_forma">
                <option value="2,5">2,5 pollici</option>
                <option value="M.2">M.2</option>
                <option value="mSATA">mSATA</option>
                <option value="U.2">U.2</option>
            </select>
            <br>
            <label class="form-label" for="interfaccia">Interfaccia:</label>
            <select class="form-select" name="interfaccia" id="interfaccia">
                <option value="sata">SATA</option>
                <option value="NVMe">NVMe PCIe</option>
            </select>
            <br>
            <label class="form-label" for="s_lettura">Velocità di lettura (MB/s):</label>
            <input class="form-control" type="number" name="s_lettura" id="s_lettura">
            <br>
            <label class="form-label" for="s_scrittura">Velocità di scrittura (MB/s):</label>
            <input class="form-control" type="number" name="s_scrittura" id="s_scrittura">
            <br>

        </div>
            
        <div id="campi_case" style="display: none;">
            <label class="form-label" for="cs_colore">Colore:</label>
            <input class="form-control" type="text" name="cs_colore" id="cs_colore">
            <br>
            <label class="form-label" for="dimensioni">Dimensioni:</label>
            <input class="form-control" type="text" name="dimensioni" id="dimensioni">
            <br>
            <label class="form-label" for="peso">Peso:</label>
            <input class="form-control" type="number" name="peso" id="peso">
            <br>
            <label class="form-label" for="fattore_di_forma">Fattore di forma scheda madre:</label>
            <select class="form-select" name="fattore_di_forma" id="fattore_di_forma">
                <option value="E-ATX">E-ATX</option>
                <option value="ATX">ATX</option>
                <option value="microATX">microATX</option>
                <option value="Mini-ITX">Mini-ITX</option>
            </select>
            <br>
            <label class="form-label" for="vetro">Vetro laterale:</label>
            <input class="form-control" type="checkbox" name="vetro" value="1">

        </div>

        <div id="campi_scheda madre" style="display: none;">
            <label class="form-label" for="formato">Formato:</label> <br>
            <input class="form-control" type="text" name="formato" id="formato">
            <br>
            <label class="form-label" for="m_socket">Socket:</label> <br>
            <input class="form-control" type="text" name="m_socket" id="m_socket">
            <br>
            <label class="form-label" for="chipset">Chipset:</label> <br>
            <input class="form-control" type="text" name="chipset" id="chipset">
            <br>
            <label class="form-label" for="n_ram">Numero slot Ram:</label> <br>
            <input class="form-control" type="number" name="n_ram" id="n_ram">
            <br>
            <label class="form-label" for="tipo_ram">Tipologia ram:</label> <br>
            <input class="form-control" type="text" name="tipo_ram" id="tipo_ram">
            <br>
            <label class="form-label" for="pcie">Versione PCIe:</label> <br>
            <input class="form-control" type="text" name="pcie" id="pcie">
            <br>
        </div>

        <div id="campi_psu" style="display: none;">
            <label class="form-label" for="p_fattore_di_forma">Fattore di forma scheda madre:</label><br>
            <select class="form-select" name="p_fattore_di_forma" id="p_fattore_di_forma">
                <option value="ATX">ATX</option>
                <option value="SFX">SFX</option>
                <option value="TFX">TFX</option>
                <option value="CFX">CFX</option>
                <option value="EPS">EPS</option>
            </select>
            <br>
            <label class="form-label" for="watt">Watt:</label> <br>
            <input class="form-control" type="number" name="watt" id="watt">
            <br>
            <label class="form-label" for="p_schema_alimentazione">Schema di cablaggio:</label> <br>
            <select class="form-select" name="p_schema_alimentazione" id="p_schema_alimentazione">
                <option value="Non modulare">Non modulare</option>
                <option value="Semi-modulare">Semi-modulare</option>
                <option value="Modulare">Modulare</option>
            </select>
            <br>
        </div>
        
        <br>
        <input type="submit" id="submit" name="submit" class="btn btn-outline-secondary btn-custom mb-3" value="Salva modifiche" disabled><br>
        <input type="button" id="delete" name="delete" class="btn btn-danger btn-custom" value="Elimina">
    </form>
    </div>
    </div>
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
</body>
</html>
