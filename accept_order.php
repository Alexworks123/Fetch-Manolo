<?php
session_start();
include('Fetch_Manolo.php'); // Your database connection

// 1. Check if the user is a logged-in rider
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    header("Location: index.php");
    exit();
}

// 2. Check if the Order ID is provided in the URL
if (isset($_GET['id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['id']);
    $rider_name = $_SESSION['name'];

    // 3. Update the database
    // We set status to 'Accepted' and assign the rider's name
    $sql = "UPDATE orders 
            SET status = 'Accepted', assigned_rider = '$rider_name' 
            WHERE id = '$order_id' AND status = 'Pending'";

    if (mysqli_query($conn, $sql)) {
        // Success! Go back to the dashboard
        header("Location: rider_page.php?msg=OrderAccepted");
    } else {
        // Error handling
        echo "Error updating record: " . mysqli_error($conn);
    }
} else {
    // No ID found, just go back
    header("Location: rider_page.php");
}
?>