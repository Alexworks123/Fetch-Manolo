<?php


session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
 <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="box">

    <h1>Welcome, <span><?= $_SESSION['name']; ?></span></h1>

    <p>Manage your bookings and delivery requests easily.</p>

    <div class="top-buttons">
        <button onclick="window.location.href='add.php'"> + Add Service </button>

        <button onclick="window.location.href='logout.php'"> Logout</button>

    </div>

   

</body>
</html>