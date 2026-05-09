<?php
session_start();
include('Fetch_Manolo.php'); 


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    header("Location: index.php");
    exit();
}


if (isset($_GET['id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['id']);
    $rider_name = $_SESSION['name'];


    $sql = "UPDATE orders 
            SET status = 'Accepted', assigned_rider = '$rider_name' 
            WHERE id = '$order_id' AND status = 'Pending'";


if (mysqli_query($conn, $sql)) {
    // Redirect to the Rider's personal profile/active tasks page
    header("Location: rider_profile.php?status=success");
    exit();

    } else {
   
        echo "Error updating record: " . mysqli_error($conn);
    }
} else {

    header("Location: rider_page.php");
}
?>