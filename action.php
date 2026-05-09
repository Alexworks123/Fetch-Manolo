<?php
session_start();
include('Fetch_Manolo.php'); // Your database connection file

if (isset($_POST['submit_order'])) {
    // Capture the data from the form
    $rider = mysqli_real_escape_string($conn, $_POST['assigned_rider']);
    $customer = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $pickup = mysqli_real_escape_string($conn, $_POST['pickup_location']);
    $dropoff = mysqli_real_escape_string($conn, $_POST['dropoff_location']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $service = mysqli_real_escape_string($conn, $_POST['service_type']);
    $status = "Pending"; // Set default status

    // The SQL Query to save the order
    $sql = "INSERT INTO orders (assigned_rider, customer_name, phone, pickup, dropoff, price, description, service_type, status) 
            VALUES ('$rider', '$customer', '$phone', '$pickup', '$dropoff', '$price', '$desc', '$service', '$status')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Order submitted successfully!'); window.location.href='user_page.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>