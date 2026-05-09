<?php
session_start();
include('Fetch_Manolo.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    header("Location: index.php");
    exit();
}

$rider_name = $_SESSION['name'];
$user_id = $_SESSION['user_id'];


$user_query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);


$active_query = "SELECT * FROM orders WHERE assigned_rider = '$rider_name' 
                  AND (status = 'Accepted' OR status = 'Picked Up') LIMIT 1";
$active_result = mysqli_query($conn, $active_query);
$active_job = mysqli_fetch_assoc($active_result);

$history_query = "SELECT * FROM orders WHERE assigned_rider = '$rider_name' 
                  AND status = 'Completed' ORDER BY id DESC"; 
$history_result = mysqli_query($conn, $history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rider Profile | Fetch Manolo</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; margin: 0; padding: 20px; }
        .wrapper { max-width: 900px; margin: 0 auto; }
        
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 25px; }
        .profile-header { display: flex; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .avatar { width: 60px; height: 60px; background: #4dabf7; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; margin-right: 20px; }
        
        .info-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .info-item label { display: block; font-size: 0.75rem; color: #888; text-transform: uppercase; font-weight: bold; }
        .info-item p { margin: 5px 0; font-weight: 600; color: #333; font-size: 0.95rem; }

        .status-badge { background: #ebfbee; color: #2b8a3e; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; }
        .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block; border: none; cursor: pointer; }
        .btn-blue { background: #4dabf7; color: white; }
        .btn-red { background: #fa5252; color: white; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; background: #f8f9fa; padding: 12px; color: #666; font-size: 0.85rem; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
    </style>
</head>
<body>

<div class="wrapper">
    <h1 style="text-align: center; color: #333;">Welcome, <?php echo htmlspecialchars($rider_name); ?></h1>
    
    <div class="card">
        <div class="profile-header">
            <div class="avatar"><?php echo substr($rider_name, 0, 1); ?></div>
            <h2 style="margin: 0;">Rider Profile</h2>
        </div>
        
        <div class="info-grid">
            <div class="info-item">
                <label>Full Name</label>
                <p><?php echo htmlspecialchars($user_data['name']); ?></p>
            </div>
            <div class="info-item">
                <label>Email Address</label>
                <p><?php echo htmlspecialchars($user_data['email']); ?></p>
            </div>
            <div class="info-item">
                <label>Phone Number</label>
                <p><?php echo htmlspecialchars($user_data['phone']); ?></p>
            </div>
            <div class="info-item">
                <label>Driver License</label>
                <p><?php echo htmlspecialchars($user_data['license'] ?: 'N/A'); ?></p>
            </div>
            <div class="info-item">
                <label>Vehicle Type</label>
                <p><?php echo htmlspecialchars($user_data['vehicle'] ?: 'N/A'); ?></p>
            </div>
            <div class="info-item">
                <label>Plate Number</label>
                <p><?php echo htmlspecialchars($user_data['plate'] ?: 'N/A'); ?></p>
            </div>
        </div>
        <div style="margin-top: 25px; display: flex; gap: 10px;">
            <a href="rider_page.php" class="btn btn-blue">Back to Dashboard</a>
            <a href="logout.php" class="btn btn-red">Logout</a>
        </div>
    </div>

    <div class="card">
        <h3 style="margin: 0; color: #4dabf7; margin-bottom: 15px;">📦 Current Delivery</h3>
        <?php if ($active_job): ?>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 5px solid #4dabf7;">
                <p><strong>Customer:</strong> <?php echo htmlspecialchars($active_job['customer_name']); ?></p>
                
                <p><strong>Note:</strong> <i style="color: #555;"><?php echo htmlspecialchars($active_job['description'] ?? 'No special instructions'); ?></i></p>
                
                <p><strong>Drop-off:</strong> <?php echo htmlspecialchars($active_job['dropoff_location']); ?></p>
                
                <div style="margin-top: 15px;">
                    <?php if ($active_job['status'] == 'Accepted'): ?>
                        <a href="update_status.php?id=<?php echo $active_job['id']; ?>&new_status=Picked Up" class="btn" style="background: #fcc419;">📦 Pick Up Item</a>
                    <?php elseif ($active_job['status'] == 'Picked Up'): ?>
                        <a href="update_status.php?id=<?php echo $active_job['id']; ?>&new_status=Completed" class="btn" style="background: #4fab4f; color: white;">✅ Confirm Delivery</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            <p style="color: #888; text-align: center;">No active deliveries right now.</p>
        <?php endif; ?>
    </div>

    <div class="card">
        <h3 style="margin: 0;">📜 Recent Deliveries</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Location</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($history_result)): ?>
                    <tr>
                        <td><?php echo date('M d', strtotime($row['created_at'])); ?></td>
                        <td><strong><?php echo htmlspecialchars($row['customer_name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($row['dropoff_location']); ?></td>
                        <td><span class="status-badge">Completed</span></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>