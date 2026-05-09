<?php
session_start();
include('Fetch_Manolo.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$current_user_name = $_SESSION['name'];


$query = "SELECT * FROM orders 
          WHERE customer_name = '$current_user_name' 
          ORDER BY id DESC LIMIT 1";

$result = mysqli_query($conn, $query);
$order = mysqli_fetch_assoc($result);

if ($order) {
    $status = $order['status'];
    $order_id = $order['id'];
} else {
    $status = 'None';
    $order_id = null;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Track My Order | Fetch Manolo</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .status-container { background: white; padding: 40px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center; width: 100%; max-width: 450px; }
        .header-row { display: flex; justify-content: center; align-items: center; gap: 15px; margin-bottom: 20px; position: relative; }
        .status-card { margin-top: 10px; padding: 20px; border-radius: 10px; background: #fafafa; border: 1px solid #eee; }
        .badge { display: inline-block; padding: 5px 15px; border-radius: 20px; font-size: 0.8rem; font-weight: bold; text-transform: uppercase; margin-top: 10px; }
        .pending { background: #fff4e6; color: #fd7e14; }
        .accepted { background: #e7f5ff; color: #228be6; }
        .pickedup { background: #fff9db; color: #f59f00; }
        .completed { background: #ebfbee; color: #2b8a3e; }
        .refresh-btn { background: #f1f3f5; border: 1px solid #ddd; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.75rem; }
        .nav-links { margin-top: 25px; display: block; color: #748ffc; text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="status-container">
    <div class="header-row">
        <h1 style="margin: 0; font-size: 1.8rem; color: #333;">Order Tracking</h1>
        <button onclick="window.location.reload();" class="refresh-btn">🔄 Refresh</button>
    </div>

    <?php if ($order): ?>
        <div class="status-card">
            <div style="font-weight: bold; color: #555;">Current Status:</div>
            <span class="badge <?php echo strtolower(str_replace(' ', '', $order['status'])); ?>">
                <?php echo htmlspecialchars($order['status']); ?>
            </span>

            <div style="margin-top: 20px;">
                <?php if ($order['status'] == 'Pending'): ?>
                    <p style="color: #666;">Looking for a rider nearby... ⏳</p>
                <?php elseif ($order['status'] == 'Accepted'): ?>
                    <p style="color: #1971c2;">🚀 <b><?php echo htmlspecialchars($order['assigned_rider']); ?></b> is heading to pick up your item!</p>
                <?php elseif ($order['status'] == 'Picked Up'): ?>
                    <p style="color: #856404;">📦 Item secured! Heading to: <br><b><?php echo htmlspecialchars($order['dropoff_location']); ?></b></p>
                <?php elseif ($order['status'] == 'Completed'): ?>
                    <p style="color: #2b8a3e;">✅ Order Delivered successfully!</p>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <div style="padding: 20px;">
            <p style="color: #888;">No orders found in your history.</p>
            <a href="user_page.php" style="color: #4c6ef5; font-weight: bold; text-decoration: none;">Request a Rider Now</a>
        </div>
    <?php endif; ?>
    
    <div style="margin-top: 20px;">
    <?php if ($status == 'Pending'): ?>
       <a href="delete_order.php?id=<?php echo $order['id']; ?>"
           onclick="return confirm('Are you sure you want to cancel this order?')" 
           style="color: #ff6b6b; text-decoration: none; font-size: 0.9rem; font-weight: bold; border: 1px solid #ff6b6b; padding: 5px 10px; border-radius: 5px;">
           ❌ Cancel Order
        </a>
    <?php endif; ?>
</div>

    <a href="user_page.php" class="nav-links">← Back to Dashboard</a>
    <a href="logout.php" class="nav-links" style="color: #fa5252; font-size: 0.8rem; margin-top: 15px;">Logout</a>
</div>

</body>
</html>