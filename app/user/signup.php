<?php
session_start();
$token = $_SESSION['jwt'];
?>


<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        body {
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .registration-form {
            background-color: #fff;
            padding: 20px; /* Ridotto da 30px a 20px */
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            position: absolute; 
            top: 50%; 
            left: 50%; 
            transform: translate(-50%, -50%);
        }

        .registration-form h2 {
            text-align: center;
            margin-bottom: 10px; /* Ridotto da 20px a 10px */
        }

        .form-group {
            margin-bottom: 10px; /* Ridotto da 15px a 10px */
        }

        #response {
          color: red;
        }

        /* Aggiunto per ridurre il padding degli input */
        .form-control {
            padding: 5px 10px;
        }

        /* Aggiunto per ridurre il margine inferiore delle etichette */
        .form-label {
            margin-bottom: 5px;
        }

        .btn {
            margin-right: 5px;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
        $("#registrationForm").submit(function(){
            var formData = {
                username: $("#username").val(),
                password: $("#password").val(),
                email: $("#email").val(),
                nome: $("#nome").val(),
                cognome: $("#cognome").val(),
                ruolo : 3
            };
            $.ajax({
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_accesso.php?action=signup',
                type: 'POST',
                dataType: 'json',
                headers: {
                    "Accept": "application/json",
                    "Authorization": "Bearer <?php echo $token; ?>"
                },
                contentType: "application/json",
                data: JSON.stringify(formData),
                success: function(data) {
                    console.log(data);
                    $("#response").html("Registrazione avvenuta con successo");
                    window.location.href = "login.php";
                },
                error: function(xhr, status, error) {
                    $("#response").html(xhr.responseJSON.Error);
                }
            });            
            return false;
        });
    });
    </script>
</head>
<body>
<div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="registration-form">
            <h2>Registrazione</h2>
            <form id="registrationForm">
                <div class="row g-3">  
                    <div class="col-md-12">
                        <label for="username" class="form-label">Username:</label>
                        <input type="text" id="username" name="username" class="form-control" required><br>
                    </div>
                    <div class="col-md-12">
                        <label for="password" class="form-label">Password:</label>
                        <input type="password" id="password" name="password" class="form-control" required><br>
                    </div>
                    <div class="col-md-12">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" id="email" name="email" class="form-control"><br>
                    </div>
                    <div class="col-md-6">
                        <label for="nome" class="form-label">Nome:</label>
                        <input type="text" id="nome" name="nome" class="form-control"><br>
                    </div>
                    <div class="col-md-6">
                        <label for="cognome" class="form-label">Cognome:</label>
                        <input type="text" id="cognome" name="cognome" class="form-control"><br>
                    </div>
                </div>
                <input type="submit" class="btn btn-success" value="Registrati">
                <input type="reset" class="btn btn-secondary" value="Reset">
            </form>
            <hr>
            <div id="response"></div><br>
            <p>Hai già un account? <a href="login.php">Accedi</a></p>
        </div>
      </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>