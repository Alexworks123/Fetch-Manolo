<?php
session_start();
include('Fetch_Manolo.php'); 

if (isset($_POST['submit_order'])) {
    // Ensure the user is logged in to get the ID for the relationship
    if (!isset($_SESSION['user_id'])) {
        die("Error: You must be logged in to place an order.");
    }

    $user_id = $_SESSION['user_id']; 
    $rider = mysqli_real_escape_string($conn, $_POST['assigned_rider']);
    $customer = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $pickup = mysqli_real_escape_string($conn, $_POST['pickup_location']);
    $dropoff = mysqli_real_escape_string($conn, $_POST['dropoff_location']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $service = mysqli_real_escape_string($conn, $_POST['service_type']);

    // SQL using exact columns from your screenshot
    $sql = "INSERT INTO orders (user_id, assigned_rider, customer_name, phone, pickup_location, dropoff_location, price, description, service_type, status) 
            VALUES ('$user_id', '$rider', '$customer', '$phone', '$pickup', '$dropoff', '$price', '$desc', '$service', 'Pending')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Order placed successfully!'); window.location.href='user_page.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>