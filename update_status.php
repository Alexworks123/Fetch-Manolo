<?php
session_start();
include('Fetch_Manolo.php');

if (isset($_GET['id']) && isset($_GET['new_status'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['id']);
    $new_status = mysqli_real_escape_string($conn, $_GET['new_status']);

    $sql = "UPDATE orders SET status = '$new_status' WHERE id = '$order_id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: rider_profile.php?msg=StatusUpdated");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>