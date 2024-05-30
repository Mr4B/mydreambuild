<!-- Pagina che consente la gestione degli utenti -->
<?php
session_start();

/* if (!isset($_SESSION['isLoged']) || $_SESSION['isLoged'] !== true) {
    header('Location: ../user/login.php'); // Reindirizza alla pagina di login
    exit; // Termina l'esecuzione dello script
} */

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
    <title>Configurazioni</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Script jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>    
    <style>
        .image {
            max-width: 110px;
            height: auto;
        }

        .btn-new-config {
            margin-bottom: 20px;
        }
    </style>
    <script type="text/javascript">
        $(document).ready(function(){
            $.ajax({
                url: '<?php echo $url; ?>app/webservices/ws_accesso.php?action=get_users',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer <?php echo $token; ?>'
                },
                success: function(data) {
                    console.log(data);
                    var table = '<table class="table table-striped"><thead><tr><th>Username</th><th>Ruolo</th><th>Username</th><th>Nome</th><th>Cognome</th><th></th></tr></thead><tbody>';
                    $.each(data, function(index, user) {
                        table += '<tr>';
                        table += '<td class="id">' + user.username + '</td>';
                        table += '<td>';
                        switch (user.ruolo) {
                            case 1:
                                table += 'Admin';
                                break;
                            case 2:
                                table += 'Moderator';
                                break;
                            case 3:
                                table += 'User';
                                break;
                        }
                        table += '</td>';
                        table += '<td>' + user.email + '</td>';
                        table += '<td>' + user.nome + '</td>';
                        table += '<td>' + user.cognome + '</td>';
                        if(user.ruolo!= 1) table += '<td><a href="change_role.php?id=' + user.username + '">cambia ruolo</a></td>';
                        else table += '<td></td>';
                        table += '</tr>';
                    });
                    table += '</tbody></table>';
                    $("#table").html(table);
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
                    $("#table").html("Errore");
                }
            });            
        });
    </script>
</head>
<body>
    <!--Esempio di header -->
    <div class="container-fluid">
        <header id="header" class role="banner">
            <?php echo $navbar->getNavBar(); ?>
        </header>
        <!-- Corpo della pagina -->
        <div id="table"></div>
    </div>
    
    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
