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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }
        form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        form label {
            font-weight: bold;
            margin-top: 10px;
        }
        form input[type="text"],
        form textarea,
        form input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-check {
            margin-top: 10px;
        }
        .btn {
            margin-top: 20px;
        }
        #response {
            margin-top: 20px;
            color: red;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>    
    <script type="text/javascript">
        $(document).ready(function(){
            $("#articolo").submit(function(event){
                event.preventDefault();

                var pubblicato = $("input[name='pubblica']").is(":checked");
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
                    var mm = String(today.getMonth() + 1).padStart(2, '0');
                    var yyyy = today.getFullYear();
                    data.data_pubblicazione = yyyy + '-' + mm + '-' + dd;
                } else {
                    data.data_pubblicazione = null;
                }

                var actionUrl = '<?php echo $url; ?>app/webservices/ws_articoli.php?action=insert_articolo';
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
                                inviaProdotto(data);
                            } else {
                                $("#response").html("Errore durante il caricamento dell'immagine");
                            }
                        },
                        error: function(xhr, status, error) {
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
                            $("#response").html(result.Success);
                        },
                        error: function(xhr, status, error) {
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
    <h2>Scrivi un articolo</h2>
    <form id="articolo" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="titolo">Titolo:</label>
            <input type="text" class="form-control" name="titolo" id="titolo" required>
        </div>
        <div class="mb-3">
            <label for="summary">Sottotitolo:</label>
            <input type="text" class="form-control" name="summary" id="summary" required>
        </div>
        <div class="mb-3">
            <label for="corpo">Corpo:</label>
            <textarea class="form-control" name="corpo" id="corpo" required></textarea>
        </div>
        <div class="mb-3">
            <label for="image">Seleziona immagine:</label>
            <input type="file" class="form-control" id="image" name="image" accept="image/*">
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="pubblica" value="1">
            <label class="form-check-label" for="pubblica">Pubblica l'articolo</label>
        </div>
        <button type="submit" id="submit" name="submit" class="btn btn-outline-info">Inserisci articolo</button>
    </form>
    <div id="response"></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>
