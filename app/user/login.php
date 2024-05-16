<?php 
session_start();
$token = $_SESSION['jwt'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <style>
        /* Custom styles for a more visually appealing login page */
        body {
        background-color: #f5f5f5; /* Light gray background */
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh; /* Ensure form is centered vertically */
        }

        .login-form {
        background-color: #fff; /* White background for the form */
        padding: 30px;
        border-radius: 5px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Subtle shadow effect */
        max-width: 400px; /* Limit form width for responsiveness */
        position: absolute; /* Posiziona il form in modo assoluto */
        top: 50%; /* Posiziona il form al 50% dall'alto */
        left: 50%; /* Posiziona il form al 50% da sinistra */
        transform: translate(-50%, -50%);
        }

        .login-form h2 {
        text-align: center;
        margin-bottom: 20px;
        }

        .form-group {
        margin-bottom: 15px;
        }

        .btn-primary {
        background-color: #388e3c; /* Green button color */
        border-color: #388e3c; /* Green border for consistency */
        }

        .btn-primary:hover {
        background-color: #2e7d32; /* Darker green on hover */
        border-color: #2e7d32;
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
    $(document).ready(function(){
        $("#loginForm").submit(function(){
            // Dati da inviare al server
            var formData = {
                username: $("#username").val(),
                password: $("#password").val()
            };
            $.ajax({
                url: 'http://localhost/mydreambuild/capolavoro/app/webservices/ws_accesso.php?action=login',
                // url: 'http://10.25.0.15/~s_bttkvn05l18d488f/capolavoro-main/app/webservices/ws_accesso.php?action=login',
                type: 'POST',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    "Authorization": "Bearer <?php echo $token; ?>"
                },
                contentType: "application/json",
                data: JSON.stringify(formData),
                success: function(data) {
                    // console.log(data);
                    <?php
                    $_SESSION['LogedIn'] = true;
                    ?>
                    $("#response").html("Login avvenuto con successo");
                    window.location.href = "../main/home.php?ruolo=" + data.ruolo + "&username=" + data.username;
                },
                error: function(xhr, status, error) {
                    // console.error('Errore durante la richiesta:', status, error);
                    $("#response").html("Username o password errati");
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
        <div class="login-form">
          <h2>Login - MyDreamBuild</h2>
          <form id="loginForm">
            <div class="form-group">
              <label for="username">Username:</label>
              <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
              <label for="password">Password:</label>
              <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
              <input type="submit" class="btn btn-primary" value="Login">
            </div>
          </form>
          <div id="response"></div>
          <br>
          <p>Sei nuovo su mydreambuild?</p>
          <a class="btn btn-outline-secondary" href="signup.php" role="button">Crea il tuo account</a>
        </div>
      </div>
    </div>
  </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>