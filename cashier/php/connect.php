<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cupnplay_db"; // Use your actual DB name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
