<?php
session_start();

$selected_rider = isset($_GET['rider_name']) ? $_GET['rider_name'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="wrapper">
        <div class="form-wrapper">
            <h1>Service Request</h1>
            <?php if($selected_rider): ?>
                <p style="text-align: center; color: #4c6ef5;">Booking Rider: <strong><?php echo $selected_rider; ?></strong></p>
            <?php endif; ?>
<form method="POST" action="action.php">
    <input type="hidden" name="assigned_rider" value="<?php echo htmlspecialchars($selected_rider); ?>">
    
    <input type="text" name="customer_name" placeholder="Full Name" required>
    <input type="text" name="phone" placeholder="Phone" required>
    <input type="text" name="pickup_location" placeholder="Pick-up Location" required>
    <input type="text" name="dropoff_location" placeholder="Drop-off Location" required>
    <input type="number" name="price" placeholder="Price" required>
    
    <textarea name="description" placeholder="Description" required><?php 
        if($selected_rider) echo "Requesting service from rider: " . htmlspecialchars($selected_rider); 
    ?></textarea>

    <select name="service_type" required>
        <option value="">--Select Service--</option>
        <option value="Food Delivery">Food Delivery</option>
         <option value="Parcel Delivery">Parcel Delivery</option>
        <option value="Pabili">Pabili</option>
        <option value="Angkas">Angkas</option>
        <option value="Padala">Padala</option>
    </select>

    <div class="btn-box">
        <button type="submit" name="submit_order">Submit</button>
        <button type="button" onclick="window.location.href='user_page.php'">Cancel</button>
    </div>
</form>
        </div>
    </div>
</body>
</html>