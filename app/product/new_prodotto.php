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
    <!-- Script jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>    
    <script type="text/javascript">
        $(document).ready(function(){
            $.ajax({
                // url: 'http://10.25.0.15/~s_bttkvn05l18d488f/capolavoro-main/app/webservices/ws_prodotti.php?action=get_products',
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_prodotti.php?action=get_categorie',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    // console.log(data);
                    $.each(data, function(index, categoria) {
                        $("#categoria").append('<option value="' + categoria.id + '" id= "' + categoria.definizione + '">' + categoria.definizione + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante il recupero delle categorie: ', status, error);
                    $("#table").html("Errore");
                }
            });            
            return false;
        });
    </script>
</head>
<body>
<div class="container-fluid">

    <header id="header" class role="banner">
        <?php echo $navbar->getNavBar(); ?>
    </header>
    <h2>Registra un nuovo prodotto</h2>
    <form id="dati_prodotto">
    <label for="categoria">Categoria:</label>
        <select class="form-select mt-2" name="categoria" id="categoria">
            <option selected>-- seleziona categoria --</option>
        </select>
        <hr>

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
        <!-- Campi di input per la CPU -->
        <div id="campi_1" style="display: none;">
            <!-- Sono da gestire i socket che sono in n/n 
                <label for="socket_cpu">Socket:</label>
            <input type="text" name="socket_cpu" id="socket_cpu">
            <br> -->
            <label for="frequenza_base">Frequenza base:</label> <br>
            <input type="text" name="frequenza_base" id="frequenza_base">
            <br>
            <label for="frequenza_boost">Frequenza boost:</label> <br>
            <input type="text" name="frequenza_boost" id="frequenza_boost">
            <br>
            <label for="n_core">Numero core:</label> <br>
            <input type="number" name="n_core" id="n_core">
            <br>
            <label for="n_thread">Numero thread:</label> <br>
            <input type="text" name="n_thread" id="n_thread">
            <br>
            <label for="consumo_energetico">Consumo energetico:</label> <br>
            <input type="text" name="consumo_energetico" id="consumo_energetico">
            <br>
            <label for="dim_cache">Dimensione cache:</label> <br>
            <input type="text" name="dim_cache" id="dim_cache">
            <br>
        </div>

    </form>

</div>
<script>
    // Funzione per mostrare i campi di input in base alla categoria selezionata
    document.getElementById("categoria").addEventListener("change", function() {
        var categoria = this.value;
        // console.log(categoria);
        // Nascondi tutti i campi di input
        var campi = document.querySelectorAll("[id^='campi_']");
        campi.forEach(function(campo) {
            campo.style.display = "none";
        });
        // Mostra solo i campi di input per la categoria selezionata
        var campiCategoria = document.getElementById("campi_" + categoria.toLowerCase());
        if (campiCategoria) {
            campiCategoria.style.display = "block";
        }
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>