<?php
session_start();
include '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'db_connect.php';
require_once('../shared/navbar.php');   
$navbar = new NavBar();
if (isset($_SESSION['LogedIn']) && $_SESSION['LogedIn'] === true) {
    // Esegue quest'azione solo se l'utente è loggato
    $navbar->setLogin($_SESSION['username'], $_SESSION['ruolo']);
}
$token = $_SESSION['jwt'];
$id = isset($_GET['id']) ? $_GET['id'] : '';
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizza prodotto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
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
        .hidden {
            display: none;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            var categoria = 'none';
            var categorie = [];

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
                        return data;
                    },
                    error: function(xhr, status, error) {
                        console.error('Errore durante il recupero delle categorie: ', status, error);
                        $("#table").html("Errore");
                    }
                });
            }

            getCategorie().then(function() {
                return getProdotto();
            }).then(function(data) {
                categoria = getDefinizioneCategoria(categorie, data[0].id_categoria);
                showDiv(categoria);
                popolaDati(data[0]);
            }).catch(function(error) {
                console.error('Errore durante il processo: ', error);
            });

            function popolaDati(data) {
                if(data.id_immagine) {
                    $("#immagine").html(`<img src="<?php echo $url; ?>app/webservices/ws_immagini.php?id=${data.id_immagine}">`);
                }

                $("#marca").text(data.marca);
                $("#modello").text(data.modello);
                $("#descrizione").text(data.descrizione);
                $("#prezzo").text(data.prezzo);
                $("#link").text(data.link);

                switch (categoria.toLowerCase()) {
                    case 'cpu':
                        $("#frequenza_base").text(data.frequenza_base);
                        $("#frequenza_boost").text(data.c_frequenza_boost);
                        $("#n_core").text(data.c_n_core);
                        $("#n_thread").text(data.c_n_thread);
                        $("#consumo_energetico").text(data.c_consumo_energetico);
                        $("#dim_cache").text(data.c_dim_cache);
                        break;

                    case 'gpu':
                        $("#g_memoria").text(data.g_memoria);
                        $("#tipo_memoria").text(data.g_tipo_memoria);
                        $("#g_frequenza_base").text(data.frequenza_base);
                        $("#g_dimensioni").text(data.dimensioni);
                        break;

                    case 'ram':
                        $("#r_dimensione").text(data.r_dimensione);
                        $("#r_velocita").text(data.r_velocita);
                        $("#r_tipo").text(data.r_tipo);
                        break;

                    case 'hdd':
                        $("#h_capacita").text(data.capacita_gb);
                        $("#h_fattore_forma").text(data.fattore_di_forma);
                        $("#h_velocita").text(data.a_velocita_rotazione);
                        $("#h_cache").text(data.a_cache_mb);
                        $("#h_lettura").text(data.a_velocita_lettura_mb_s);
                        $("#h_scrittura").text(data.a_velocita_scrittura_mb_s);
                        break;

                    case 'ssd':
                        $("#s_capacita").text(data.capacita_gb);
                        $("#s_fattore_forma").text(data.fattore_di_forma);
                        $("#interfaccia").text(data.a_interfaccia);
                        $("#s_lettura").text(data.a_velocita_lettura_mb_s);
                        $("#s_scrittura").text(data.a_velocita_scrittura_mb_s);
                        break;

                    case 'case':
                        $("#cs_colore").text(data.cs_colore);
                        $("#dimensioni").text(data.dimensioni);
                        $("#peso").text(data.cs_peso);
                        $("#fattore_di_forma").text(data.fattore_di_forma);
                        $("#vetro").text(data.cs_finestra_laterale === 1 ? 'Sì' : 'No');
                        break;

                    case 'scheda madre':
                        $("#formato").text(data.m_formato);
                        $("#m_socket").text(data.socket);
                        $("#chipset").text(data.m_chipset);
                        $("#n_ram").text(data.m_numero_slot_ram);
                        $("#tipo_ram").text(data.m_tipologia_ram);
                        $("#pcie").text(data.m_version_pcie);
                        break;

                    case 'psu':
                        $("#p_fattore_di_forma").text(data.fattore_di_forma);
                        $("#watt").text(data.p_watt);
                        $("#p_schema_alimentazione").text(data.p_schema_alimentazione);
                        break;
                }
            }
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
            <h2 class="text-center mb-4">Visualizza prodotto</h2>
            <div id="immagine"></div>
            <br>
            <label class="form-label" for="marca">Marca:</label><br>
            <p id="marca"></p>
            <br>
            <label class="form-label" for="modello">Modello:</label><br>
            <p id="modello"></p>
            <br>
            <label class="form-label" for="descrizione">Descrizione:</label><br>
            <p id="descrizione"></p>
            <br>
            <label class="form-label" for="prezzo">Prezzo:</label><br>
            <p id="prezzo"></p>
            <br>
            <label class="form-label" for="link">Link d'acquisto:</label><br>
            <p id="link"></p>
            <br>
            <div id="campi_cpu" style="display: none;">
                <label class="form-label" for="frequenza_base">Frequenza base:</label><br>
                <p id="frequenza_base"></p>
                <br>
                <label class="form-label" for="frequenza_boost">Frequenza boost:</label><br>
                <p id="frequenza_boost"></p>
                <br>
                <label class="form-label" for="n_core">Numero core:</label><br>
                <p id="n_core"></p>
                <br>
                <label class="form-label" for="n_thread">Numero thread:</label><br>
                <p id="n_thread"></p>
                <br>
                <label class="form-label" for="consumo_energetico">Consumo energetico (W):</label><br>
                <p id="consumo_energetico"></p>
                <br>
                <label class="form-label" for="dim_cache">Dimensione cache (MB):</label><br>
                <p id="dim_cache"></p>
                <br>
            </div>
            <div id="campi_ram" style="display: none;">
                <label class="form-label" for="r_dimensione">Dimensione (GB):</label><br>
                <p id="r_dimensione"></p>
                <br>
                <label class="form-label" for="r_velocita">Velocità (MHz):</label><br>
                <p id="r_velocita"></p>
                <br>
                <label class="form-label" for="r_tipo">Tipologia:</label><br>
                <p id="r_tipo"></p>
                <br>
            </div>
            <div id="campi_hdd" style="display: none;">
                <label class="form-label" for="h_capacita">Capacità (GB):</label><br>
                <p id="h_capacita"></p>
                <br>
                <label class="form-label" for="h_fattore_forma">Fattore di forma:</label><br>
                <p id="h_fattore_forma"></p>
                <br>
                <label class="form-label" for="h_velocita">Velocità di rotazione (RPM):</label><br>
                <p id="h_velocita"></p>
                <br>
                <label class="form-label" for="h_cache">Cache (MB):</label><br>
                <p id="h_cache"></p>
                <br>
                <label class="form-label" for="h_lettura">Velocità di lettura (MB/s):</label><br>
                <p id="h_lettura"></p>
                <br>
                <label class="form-label" for="h_scrittura">Velocità di scrittura (MB/s):</label><br>
                <p id="h_scrittura"></p>
                <br>
            </div>
            <div id="campi_ssd" style="display: none;">
                <label class="form-label" for="s_capacita">Capacità (GB):</label><br>
                <p id="s_capacita"></p>
                <br>
                <label class="form-label" for="s_fattore_forma">Fattore di forma:</label><br>
                <p id="s_fattore_forma"></p>
                <br>
                <label class="form-label" for="interfaccia">Interfaccia:</label><br>
                <p id="interfaccia"></p>
                <br>
                <label class="form-label" for="s_lettura">Velocità di lettura (MB/s):</label><br>
                <p id="s_lettura"></p>
                <br>
                <label class="form-label" for="s_scrittura">Velocità di scrittura (MB/s):</label><br>
                <p id="s_scrittura"></p>
                <br>
            </div>
            <div id="campi_case" style="display: none;">
                <label class="form-label" for="cs_colore">Colore:</label><br>
                <p id="cs_colore"></p>
                <br>
                <label class="form-label" for="dimensioni">Dimensioni (mm):</label><br>
                <p id="dimensioni"></p>
                <br>
                <label class="form-label" for="peso">Peso (Kg):</label><br>
                <p id="peso"></p>
                <br>
                <label class="form-label" for="fattore_di_forma">Fattore di forma:</label><br>
                <p id="fattore_di_forma"></p>
                <br>
                <label class="form-label" for="vetro">Finestra laterale in vetro temperato:</label><br>
                <p id="vetro"></p>
                <br>
            </div>
            <div id="campi_mobo" style="display: none;">
                <label class="form-label" for="formato">Formato:</label><br>
                <p id="formato"></p>
                <br>
                <label class="form-label" for="m_socket">Socket:</label><br>
                <p id="m_socket"></p>
                <br>
                <label class="form-label" for="chipset">Chipset:</label><br>
                <p id="chipset"></p>
                <br>
                <label class="form-label" for="n_ram">Numero slot RAM:</label><br>
                <p id="n_ram"></p>
                <br>
                <label class="form-label" for="tipo_ram">Tipologia RAM:</label><br>
                <p id="tipo_ram"></p>
                <br>
                <label class="form-label" for="pcie">Versione PCIe:</label><br>
                <p id="pcie"></p>
                <br>
            </div>
            <div id="campi_gpu" style="display: none;">
                <label class="form-label" for="g_memoria">Memoria (GB):</label><br>
                <p id="g_memoria"></p>
                <br>
                <label class="form-label" for="tipo_memoria">Tipologia memoria:</label><br>
                <p id="tipo_memoria"></p>
                <br>
                <label class="form-label" for="g_frequenza_base">Frequenza base (MHz):</label><br>
                <p id="g_frequenza_base"></p>
                <br>
                <label class="form-label" for="g_dimensioni">Dimensioni:</label><br>
                <p id="g_dimensioni"></p>
                <br>
            </div>
            <div id="campi_psu" style="display: none;">
                <label class="form-label" for="p_fattore_di_forma">Fattore di forma:</label><br>
                <p id="p_fattore_di_forma"></p>
                <br>
                <label class="form-label" for="watt">Watt:</label><br>
                <p id="watt"></p>
                <br>
                <label class="form-label" for="p_schema_alimentazione">Schema di alimentazione:</label><br>
                <p id="p_schema_alimentazione"></p>
                <br>
            </div>
        </div>
    </div>
</div>
</body>
<script type="text/javascript">
    function getDefinizioneCategoria(categorie, id_categoria) {
        for (var i = 0; i < categorie.length; i++) {
            if (categorie[i].id === id_categoria) {
                return categorie[i].definizione;
            }
        }
        return null;
    }

    function showDiv(categoria) {
        switch (categoria.toLowerCase()) {
            case 'cpu':
                $("#campi_cpu").show();
                break;
            case 'ram':
                $("#campi_ram").show();
                break;
            case 'hdd':
                $("#campi_hdd").show();
                break;
            case 'ssd':
                $("#campi_ssd").show();
                break;
            case 'case':
                $("#campi_case").show();
                break;
            case 'scheda madre':
                $("#campi_mobo").show();
                break;
            case 'gpu':
                $("#campi_gpu").show();
                break;
            case 'psu':
                $("#campi_psu").show();
                break;
        }
    }
</script>
</html>

