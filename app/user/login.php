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
                type: 'POST',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    "Authorization": "Bearer <?php echo $token; ?>"
                },
                contentType: "application/json",
                data: JSON.stringify(formData),
                success: function(data) {
                    console.log(response);
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
                }
            });            
            return false;
        });
    });
    </script>

</head>
<body>
    <h2>Login</h2>
    <form id="loginForm">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required><br><br>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>