<?php
<<<<<<< HEAD

$host = "localhost";
$user = "root";
$password = "";
$database = "fetch_manolo_db";
=======
$host = "localhost";
$user = "root";
$password = "";
$database = "polexa_db";
>>>>>>> 110cb44 (Fixing the the connection to database and Successfull)

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
<<<<<<< HEAD

=======
>>>>>>> 110cb44 (Fixing the the connection to database and Successfull)
?>