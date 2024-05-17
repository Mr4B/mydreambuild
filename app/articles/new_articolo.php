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
            $("#articolo").submit(function(event){
                event.preventDefault();

                // var pubblicato = $("#pubblica").val();
                var pubblicato = $("input[name='pubblica']").is(":checked") ? true : false;
                // console.log(pubblicato);

                var data = {
                    titolo: $("#titolo").val(),
                    summary: $("#summary").val(),
                    corpo: $("#corpo").val(),
                    pubblicato: pubblicato,
                    redattore: '<?php echo $_SESSION['username']; ?>'
                };

                if(pubblicato) {
                    var today = new Date();
                    var dd = String(today.getDate()).padStart(2, '0');
                    var mm = String(today.getMonth() + 1).padStart(2, '0'); // Gennaio è 0!
                    var yyyy = today.getFullYear();
                    data.data_pubblicazione = yyyy + '-' + mm + '-' + dd;
                }else
                    data_pubblicazione = null;

                // Cambia l'url al webservices desiderato
                var actionUrl = 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_articoli.php?action=insert_articolo';
                // var actionUrl = 'http://10.25.0.15/~s_bttkvn05l18d488f/capolavoro-main/app/webservices/ws_prodotti.php?action=';

                var imageFile = $('#image')[0].files[0];

                // Se l'immagine esiste allora la carica
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
                    console.log(data);
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
                            console.error('Errore durante l\'inserimento dell\'articolo :', status, error);
                            $("#response").html("Errore durante l'inserimento dell\'articolo ");
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
    <h2>Scrivi un articolo</h2>
    <form id="articolo" enctype="multipart/form-data">
        <label for="titolo">Titolo:</label><br>
        <input type="text" name="titolo" id="titolo" >
        <br>
        <label for="summary">Sottotitolo:</label><br>
        <input type="text" name="summary" id="summary" >
        <br>
        <label for="corpo">Corpo:</label><br>
        <textarea type="text" name="corpo" id="corpo" ></textarea>
        <br><br>
        <label for="image">Seleziona immagine:</label><br>
        <input type="file" id="image" name="image" accept="image/*" >
        <br><br>
        <label for="pubblica">Pubblica l'articolo:</label>
        <input type="checkbox" name="pubblica" value="1">
        <!-- <input type="hidden" name="redattore" value=""> -->
        <br>
        <br>

        <br>
        <input type="submit" id="submit" name="submit" class="btn btn-outline-info" value="Inserisci prodotto">
    </form>
    <div id="response"></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
