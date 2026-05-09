<?php
session_start();
include('Fetch_Manolo.php');

if (isset($_GET['id'])) {
    $order_id = mysqli_real_escape_string($conn, $_GET['id']);

    $query = "DELETE FROM orders WHERE id = '$order_id'";
    


    if (mysqli_query($conn, $query)) {
        header("Location: rider_page.php?msg=declined");
        exit();
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}
?>