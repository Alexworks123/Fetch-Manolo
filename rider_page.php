<?php
session_start();
include('Fetch_Manolo.php'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    header("Location: index.php");
    exit();
}


$current_rider_name = $_SESSION['name']; 

$query = "SELECT * FROM orders 
          WHERE status = 'Pending' 
          AND (assigned_rider = '' OR assigned_rider IS NULL OR assigned_rider = '$current_rider_name') 
          ORDER BY created_at DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Page</title>
    
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="dashboard">
    <h1>FETCH MANOLO</h1>
    <h2>Welcome, <span><?= htmlspecialchars($_SESSION['name']); ?></span></h2>
    <p>Rider Service Dashboard</p>

   <div class="header-nav" style="display: flex; justify-content: space-between; align-items: center; max-width: 800px; margin: 0 auto 20px;">
    <div class="left-buttons">
        <button onclick="window.location.href='rider_profile.php'" class="btn-profile" style="background: #4dabf7; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">👤 My Profile</button>
        <button onclick="window.location.reload();" class="btn-refresh" style="background: #748ffc; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; margin-left: 5px;">🔄 Refresh</button>
    </div>
    
    <div class="right-buttons">
        <button onclick="window.location.href='logout.php'" class="btn-logout" style="background: #fa5252; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;">Logout 🚪</button>
    </div>
</div>


    <div class="table-container">
        <h3>Available Bookings</h3>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Pick-up</th>
                    <th>Drop-off</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $count = 1;
                if (mysqli_num_rows($result) > 0):
                   while ($row = mysqli_fetch_assoc($result)) {
    $orderType = (empty($row['assigned_rider'])) ? "Global" : "Direct";
    $typeColor = ($orderType == "Direct") ? "#ff6b6b" : "#748ffc"; // Red for Direct, Blue for Global
    ?>
    <tr>
        <td><?php echo $count++; ?></td>
        <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
        <td><span style="color: <?php echo $typeColor; ?>; font-weight: bold;"><?php echo $orderType; ?></span></td>
        <td><?php echo htmlspecialchars($row['pickup_location']); ?></td>
        <td><?php echo htmlspecialchars($row['dropoff_location']); ?></td>
        <td><span class="badge"><?php echo $row['status']; ?></span></td>
        <td>
            <a href="accept_order.php?id=<?php echo $row['id']; ?>" class="accept-btn">Accept</a>
        </td>
        
    </tr>


                <?php 
                    }
                else:
                ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 20px;">No available bookings at the moment.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>