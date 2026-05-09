<?php
session_start();
include('Fetch_Manolo.php');

$order_id = $_GET['id'];
$order_query = mysqli_query($conn, "SELECT * FROM orders WHERE id = '$order_id'");
$order = mysqli_fetch_assoc($order_query);

if ($order['status'] !== 'PENDING') {
    die("This order is already being processed and cannot be edited.");
}

if (isset($_POST['update_order'])) {
    $service = $_POST['service_type'];
    $location = $_POST['dropoff_location'];
    
    mysqli_query($conn, "UPDATE orders SET service_type='$service', dropoff_location='$location' WHERE id='$order_id'");
    header("Location: user_page.php?msg=updated");
}
?>

<div class="card" style="max-width: 400px; margin: 50px auto; font-family: sans-serif; padding: 20px; border: 1px solid #ccc; border-radius: 10px;">
    <h2>Edit Your Order</h2>
    <form method="POST">
        <label>Service Type</label><br>
        <select name="service_type" style="width:100%; padding:10px; margin-bottom:15px;">
            <option value="Angkas" <?php if($order['service_type'] == 'Angkas') echo 'selected'; ?>>Angkas</option>
            <option value="Pabili" <?php if($order['service_type'] == 'Pabili') echo 'selected'; ?>>Pabili</option>
            <option value="Padala" <?php if($order['service_type'] == 'Padala') echo 'selected'; ?>>Padala</option>
        </select>
        
        <label>Drop-off Location</label><br>
        <input type="text" name="dropoff_location" value="<?php echo $order['dropoff_location']; ?>" style="width:100%; padding:10px; margin-bottom:15px;">
        
        <button type="submit" name="update_order" style="background:#4dabf7; color:white; border:none; padding:10px; width:100%; border-radius:5px; cursor:pointer;">Save Changes</button>
        <br><br>
        <a href="user_page.php" style="display:block; text-align:center; color:#666;">Cancel</a>
    </form>
</div>