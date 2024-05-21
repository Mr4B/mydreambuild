<?php
/* $servername = "10.25.0.14";
$port = 3306;
$username = "5cbattistelli";
$password = "5cbattistelli";
$dbname = "5cbattistelli_mydreambuild"; */

$servername = "localhost";
// $port = 3306;
$username = "prova";
$password = "Account12!";
$dbname = "mydreambuild";

/* $servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "mydreambuild"; */


function getConnection() {
    global $servername, $username, $password, $dbname, $port;
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo '<script>console.log("Errore");</script>';
        die("Connection failed: " . $conn->connect_error);
    }

    // echo "<br> Connession: " . mysqli_get_host_info($conn);
    return $conn;
}
?>
