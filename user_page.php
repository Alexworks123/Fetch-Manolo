<?php
session_start();
include('Fetch_Manolo.php'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$user_email = $_SESSION['email']; 
$user_id = $_SESSION['user_id'];
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE email = '$user_email'"));

// Note that we use u.status because status is in the users table
$query = "SELECT u.name, u.status, r.vehicle_model, r.current_location 
          FROM users u 
          JOIN rider_details r ON u.id = r.user_id 
          WHERE u.role = 'rider' AND u.status = 'Online'";
// Check for active orders
$check_pending = mysqli_query($conn, "SELECT id, status FROM orders WHERE user_id = '$user_id' AND status NOT IN ('Delivered', 'Cancelled') LIMIT 1");

$has_active_order = false;
$order_id = null;
$order_status = '';

if (mysqli_num_rows($check_pending) > 0) {
    $has_active_order = true;
    $pending_data = mysqli_fetch_assoc($check_pending);
    $order_id = $pending_data['id'];
    $order_status = $pending_data['status']; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard | Fetch Manolo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body style="background-color: #f0f2f5; margin: 0; padding: 20px;">
<div class="container" style="text-align: center; font-family: sans-serif; max-width: 1000px; margin: auto;">
    
    <h1 style="color: #333; margin-bottom: 5px;">Welcome, <span style="color: #4c6ef5;"><?php echo htmlspecialchars($user['name']); ?></span></h1>
    <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">Find a rider and get things done.</p>

    <div style="margin-bottom: 30px; display: flex; justify-content: center; gap: 10px;">
        <a href="user_profile.php" style="background: #748ffc; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: bold;">
            👤 My Profile
        </a>
         <button onclick="window.location.reload();" class="btn-refresh" style="background: #748ffc; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">🔄 Refresh</button>
    </div>

    <div style="text-align: center; margin-top: 20px; margin-bottom: 40px;">
        <?php if ($has_active_order): ?>
            <div style="background: #fff9db; padding: 15px; border-radius: 12px; border: 1px solid #fab005; display: inline-block; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
                <p style="color: #856404; margin: 0 0 10px 0; font-weight: 500;">
                    ⚠️ Order Status: <strong style="color: #d9480f;"><?php echo $order_status; ?></strong>
                </p>
                <?php if ($order_status === 'PENDING'): ?>
                    <a href="edit_order.php?id=<?php echo $order_id; ?>" style="background: #fab005; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">
                         Edit Recent Order
                    </a>
                <?php else: ?>
                    <a href="order_status.php" style="background: #228be6; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">
                         Track Progress
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <a href="add.php" style="background: #4c6ef5; color: white; padding: 15px 30px; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 1.1rem; box-shadow: 0 4px 10px rgba(76, 110, 245, 0.3);">
                 Request a Rider
            </a>
        <?php endif; ?>
    </div>

   <div class="card" style="padding: 20px; background: white; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
    <h3 style="margin-bottom: 20px; text-align: left; color: #333;">Available Riders Nearby</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; border-bottom: 2px solid #eee;">
                <th style="padding: 15px 12px; text-align: left; font-size: 0.85rem; color: #666;">No.</th>
                <th style="padding: 15px 12px; text-align: left; font-size: 0.85rem; color: #666;">Rider Name</th>
                <th style="padding: 15px 12px; text-align: left; font-size: 0.85rem; color: #666;">Current Location</th>
                <th style="padding: 15px 12px; text-align: left; font-size: 0.85rem; color: #666;">Vehicle</th>
                <th style="padding: 15px 12px; text-align: left; font-size: 0.85rem; color: #666;">Status</th>
                <th style="padding: 15px 12px; text-align: center; font-size: 0.85rem; color: #666;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Combined Query
            $query = "SELECT u.name, r.vehicle, r.current_location, u.id 
                      FROM users u 
                      JOIN rider_details r ON u.id = r.user_id 
                      WHERE u.role = 'rider' AND u.status = 'Online'";
            $result = mysqli_query($conn, $query);
            $no = 1;

            if(mysqli_num_rows($result) > 0):
                while($row = mysqli_fetch_assoc($result)): 
                    // Check if rider is busy with another order
                    $r_name = $row['name'];
                    $check_job = mysqli_query($conn, "SELECT id FROM orders WHERE assigned_rider = '$r_name' AND (status = 'Accepted' OR status = 'Picked Up')");
                    $is_rider_busy = (mysqli_num_rows($check_job) > 0);
            ?>
            <tr style="border-bottom: 1px solid #f1f1f1;">
                <td style="padding: 15px 12px; color: #888;"><?php echo $no++; ?></td>
                <td style="padding: 15px 12px;"><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                <td style="padding: 15px 12px; color: #4dabf7; font-weight: bold;">
                    📍 <?php echo htmlspecialchars($row['current_location'] ?: 'Stationary'); ?>
                </td>
                <td style="padding: 15px 12px; color: #555;"><?php echo htmlspecialchars($row['vehicle']); ?></td>
                <td style="padding: 15px 12px;">
                    <span style="color: <?php echo $is_rider_busy ? "#fd7e14" : "#40c057"; ?>; font-weight: 600;">
                        <?php echo $is_rider_busy ? "Busy" : "Online"; ?>
                    </span>
                </td>
                <td style="padding: 15px 12px; text-align: center;">
                    <?php if ($is_rider_busy || $has_active_order): ?>
                        <button disabled style="background: #e9ecef; color: #adb5bd; border: none; padding: 8px 16px; border-radius: 8px; cursor: not-allowed; font-weight: bold;">
                            <?php echo $has_active_order ? "Finish Current Order" : "Rider Busy"; ?>
                        </button>
                    <?php else: ?>
                        <a href="add.php?rider_name=<?php echo urlencode($row['name']); ?>" 
                           style="background: #748ffc; color: white; padding: 8px 16px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block; transition: 0.3s;">
                           Direct Order
                        </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php 
                endwhile; 
            else:
            ?>
            <tr><td colspan="6" style="padding: 30px; color: #999;">No riders currently online.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
   </div>
</div>
</body>
</html>