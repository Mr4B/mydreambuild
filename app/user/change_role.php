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

// Otteniamo l'ID utente dalla query string
$userId = $_GET['id'];
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifica Ruolo Utente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <!-- Script jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var userId = '<?php echo $userId; ?>';
            var token = '<?php echo $token; ?>';
            var roles = [];

            // Recupera i dettagli dell'utente
            $.ajax({
                url: '<?php echo $url; ?>app/webservices/ws_accesso.php?action=user&username=' + userId,
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                success: function(data) {
                    $('#username').val(data[0].username);
                    $('#email').val(data[0].email);
                    $('#nome').val(data[0].nome);
                    $('#cognome').val(data[0].cognome);
                    $('#ruolo').val(data[0].ruolo);
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
                    $('#error').text('Errore durante il recupero dei dati utente');
                }
            });

            // Recupera i ruoli disponibili
            $.ajax({
                url: '<?php echo $url; ?>app/webservices/ws_accesso.php?action=get_roles',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token
                },
                success: function(data) {
                    roles = data;
                    // console.log(roles)
                    var roleSelect = $('#ruolo');
                    $.each(roles, function(index, role) {
                        if(role.id != 1 && role.id != 4)
                        roleSelect.append('<option value="' + role.id + '">' + role.denominazione + '</option>');
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Errore durante la richiesta:', status, error);
                    $('#error').text('Errore durante il recupero dei ruoli');
                }
            });

            // Aggiorna il ruolo dell'utente
            $('#updateRoleForm').submit(function(e) {
                e.preventDefault();
                var newRole = $('#ruolo').val();

                $.ajax({
                    url: '<?php echo $url; ?>app/webservices/ws_accesso.php?action=update_role',
                    type: 'PUT',
                    contentType: 'application/json',
                    data: JSON.stringify({
                        username: userId,
                        ruolo: newRole
                    }),
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + token
                    },
                    success: function(data) {
                        $('#success').text('Ruolo aggiornato con successo');
                        window.location.href = `user_management.php`;
                    },
                    error: function(xhr, status, error) {
                        console.error('Errore durante la richiesta:', status, error);
                        $('#error').text('Errore durante l\'aggiornamento del ruolo');
                    }
                });
            });
        });
    </script>
</head>
<body>
    <div class="container-fluid">
        <header>
            <?php echo $navbar->getNavBar(); ?>
        </header>
        <div class="container">
        <h1>Modifica Ruolo Utente</h1>
        <div id="error" class="text-danger"></div>
        <div id="success" class="text-success"></div>
        <form id="updateRoleForm">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" readonly>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="text" class="form-control" id="email" name="email" readonly>
            </div>
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" readonly>
            </div>
            <div class="mb-3">
                <label for="cognome" class="form-label">Cognome</label>
                <input type="text" class="form-control" id="cognome" name="cognome" readonly>
            </div>
            <div class="mb-3">
                <label for="ruolo" class="form-label">Ruolo</label>
                <select class="form-select" id="ruolo" name="ruolo">
                    <!-- Options will be populated by AJAX -->
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Aggiorna Ruolo</button>
        </form>
        </div>
    </div>

    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
