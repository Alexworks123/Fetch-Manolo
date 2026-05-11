<?php
session_start();
include('Fetch_Manolo.php'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    header("Location: index.php");
    exit();
}

if(isset($_GET['id']) && isset($_SESSION['name'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['id']);
    $rider_name = $_SESSION['name'];

    // Update status to Accepted and assign the rider only if it's still Pending
    $sql = "UPDATE orders 
            SET status = 'Accepted', assigned_rider = '$rider_name' 
            WHERE id = '$order_id' AND status = 'Pending'";
    
    if(mysqli_query($conn, $sql)) {
        // Redirect to rider page so they can see their active job
        header("Location: rider_page.php?status=accepted");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: rider_page.php");
}
?>