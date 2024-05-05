<?php
$servername = "10.25.0.14";
$port = 3306;
$username = "5cbattistelli";
$password = "5cbattistelli";
$dbname = "5cbattistelli_ClienteMeccanico";

function getConnection() {
    global $servername, $username, $password, $dbname, $port;
    $conn = new mysqli($servername, $username, $password, $dbname, $port);

    if ($conn->connect_error) {
        echo '<script>console.log("Errore");</script>';
        die("Connection failed: " . $conn->connect_error);
    }

    // echo "<br> Connession: " . mysqli_get_host_info($conn);
    return $conn;
}
?>
