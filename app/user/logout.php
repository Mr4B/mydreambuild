<?php
session_start();

// Elimina tutte le variabili di sessione
session_unset();

// Distrugge la sessione
session_destroy();

// Reindirizza l'utente alla pagina desiderata
header("Location: ../main/home.php");
exit;
?>
