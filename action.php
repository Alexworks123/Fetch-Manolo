<?php
session_start();
include('Fetch_Manolo.php'); 

if (isset($_POST['submit_order'])) {
    // 1. Security Check
    if (!isset($_SESSION['user_id'])) {
        die("Error: You must be logged in to place an order.");
    }

    // 2. Data Preparation
    $user_id = $_SESSION['user_id']; 
    
    // CRITICAL: Use the session name to ensure order_status.php can find it
    $customer = mysqli_real_escape_string($conn, $_SESSION['name']); 
    
    $rider = mysqli_real_escape_string($conn, $_POST['assigned_rider']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $pickup = mysqli_real_escape_string($conn, $_POST['pickup_location']);
    $dropoff = mysqli_real_escape_string($conn, $_POST['dropoff_location']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $service = mysqli_real_escape_string($conn, $_POST['service_type']);

    // 3. Insert into Database
    $sql = "INSERT INTO orders (user_id, assigned_rider, customer_name, phone, pickup_location, dropoff_location, price, description, service_type, status) 
            VALUES ('$user_id', '$rider', '$customer', '$phone', '$pickup', '$dropoff', '$price', '$desc', '$service', 'Pending')";

    if (mysqli_query($conn, $sql)) {
        // 4. Redirect straight to tracking page so they see their order
        header("Location: order_status.php");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>