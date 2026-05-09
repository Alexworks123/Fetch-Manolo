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

$check_pending = mysqli_query($conn, "SELECT id FROM orders WHERE user_id = '$user_id' AND status = 'PENDING'");

if (mysqli_num_rows($check_pending) > 0) {
    $has_pending = true;
    $pending_data = mysqli_fetch_assoc($check_pending);
    $order_id = $pending_data['id'];
} else {
    $has_pending = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard | Fetch Manolo</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container" style="text-align: center; font-family: sans-serif;">
    
    <h1 style="color: #333; margin-bottom: 5px;">Welcome, <span style="color: #4c6ef5;"><?php echo htmlspecialchars($user['name']); ?></span></h1>
    <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">Find a rider and get things done.</p>

    <div style="margin-bottom: 30px; display: flex; justify-content: center; gap: 10px;">
        <a href="user_profile.php" style="background: #748ffc; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 5px;">
            👤 My Profile
        </a>
        
        <a href="order_status.php" style="background: #748ffc; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-size: 0.9rem;">
            Track My Order
        </a>
        
    </div>

    <div style="text-align: center; margin-top: 20px; margin-bottom: 40px;">
        <?php if ($has_pending): ?>
            <div style="background: #fff9db; padding: 15px; border-radius: 8px; border: 1px solid #fab005; display: inline-block;">
                <p style="color: #856404; margin: 0 0 10px 0; font-weight: 500;">⚠️ You have an active pending order.</p>
                <a href="edit_order.php?id=<?php echo $order_id; ?>" style="background: #fab005; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block;">
                     Edit Recent Order
                </a>
            </div>
        <?php else: ?>
            <a href="add.php" style="background: #4c6ef5; color: white; padding: 15px 30px; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 1.1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: inline-block;">
                 Request a Rider
            </a>
        <?php endif; ?>
    </div>

    <div class="table-container" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 20px;">
        <h3 style="text-align: left; color: #333; margin-bottom: 15px; font-family: sans-serif;">Available Riders</h3>
        
        <table style="width: 100%; border-collapse: collapse; font-family: sans-serif;">
            <thead>
                <tr style="border-bottom: 2px solid #eee;">
                    <th style="padding: 12px; text-align: left; color: #555; font-size: 0.9rem;">No.</th>
                    <th style="padding: 12px; text-align: left; color: #555; font-size: 0.9rem;">Rider Name</th>
                    <th style="padding: 12px; text-align: left; color: #555; font-size: 0.9rem;">Location</th>
                    <th style="padding: 12px; text-align: left; color: #555; font-size: 0.9rem;">Status</th>
                    <th style="padding: 12px; text-align: left; color: #555; font-size: 0.9rem;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rider_query = "SELECT name, address FROM users WHERE role = 'rider'";
                $rider_result = mysqli_query($conn, $rider_query);
                $count = 1;

                if (mysqli_num_rows($rider_result) > 0) {
                    while ($rider = mysqli_fetch_assoc($rider_result)) {
                        $r_name = $rider['name'];
                        $check_job = mysqli_query($conn, "SELECT id FROM orders WHERE assigned_rider = '$r_name' AND (status = 'Accepted' OR status = 'Picked Up')");
                        
                        if (mysqli_num_rows($check_job) > 0) {
                            $display_status = "Busy";
                            $status_color = "#fd7e14"; 
                            $is_busy = true;
                        } else {
                            $display_status = "Online";
                            $status_color = "#748ffc"; 
                            $is_busy = false;
                        }
                ?>
                    <tr>
                        <td style="padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 0.9rem;"><?php echo $count++; ?></td>
                        <td style="padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 0.9rem; font-weight: 500;"><?php echo htmlspecialchars($r_name); ?></td>
                        <td style="padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 0.9rem; color: #666;"><?php echo htmlspecialchars($rider['address']); ?></td>
                        <td style="padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 0.9rem;">
                            <span style="color: <?php echo $status_color; ?>; font-weight: 500;"><?php echo $display_status; ?></span>
                        </td>
                        <td style="padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 0.9rem;">
                            <?php if (!$is_busy): ?>
                                <a href="add.php?rider_name=<?php echo urlencode($r_name); ?>" 
                                style="background-color: #748ffc; color: white !important; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; text-decoration: none; font-weight: 500; display: inline-block;">
                                Direct Order
                                </a>
                            <?php else: ?>
                                <button disabled style="background-color: #dee2e6; color: #adb5bd; border: none; padding: 8px 16px; border-radius: 6px; cursor: not-allowed; font-weight: 500;">
                                    Busy
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='5' style='text-align:center; padding: 20px; color: #888;'>No riders available at the moment.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>