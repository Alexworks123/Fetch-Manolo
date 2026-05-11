<?php
session_start();
include('Fetch_Manolo.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    // Capture the location from the form input
    $new_location = mysqli_real_escape_string($conn, $_POST['current_location']);

    // Update the rider's specific record
    $query = "UPDATE rider_details SET current_location = '$new_location' WHERE user_id = '$user_id'";

    if (mysqli_query($conn, $query)) {
        // Redirect back with the success flag used in your rider_page.php logic
        header("Location: rider_page.php?update=success");
    } else {
        echo "Database Error: " . mysqli_error($conn);
    }
} else {
    header("Location: rider_page.php");
}
exit();
?>