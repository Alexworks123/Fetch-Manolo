<?php
session_start();
include('Fetch_Manolo.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    header("Location: index.php");
    exit();
}

$rider_name = $_SESSION['name'];


$active_query = "SELECT * FROM orders 
                 WHERE assigned_rider = '$rider_name' 
                 AND (status = 'Accepted' OR status = 'Picked Up') 
                 LIMIT 1";
$active_result = mysqli_query($conn, $active_query);
$active_job = mysqli_fetch_assoc($active_result);


$history_query = "SELECT * FROM orders 
                  WHERE assigned_rider = '$rider_name' 
                  AND status = 'Completed' 
                  ORDER BY id DESC"; 
$history_result = mysqli_query($conn, $history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rider Profile | Fetch Manolo</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-container { max-width: 800px; margin: 20px auto; padding: 20px; font-family: sans-serif; }
        .job-card { background: #fff; border: 2px solid #748ffc; padding: 20px; border-radius: 10px; margin-bottom: 30px; }
        .history-table { width: 100%; border-collapse: collapse; }
        .history-table th, .history-table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        .status-badge { background: #ebfbee; color: #2b8a3e; padding: 4px 8px; border-radius: 4px; font-size: 12px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="profile-container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1>Rider Profile: <?php echo htmlspecialchars($rider_name); ?></h1>
            <button onclick="window.location.href='logout.php'" style="background: #fa5252; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer;">Logout</button>
        </div>
        <div class="header-nav" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <a href="rider_page.php" style="text-decoration: none; color: #748ffc; font-weight: bold;">← Back to Available Jobs</a>
    
</div>

        <div class="active-delivery">
            <h2 style="color: #4dabf7;">📦 Current Delivery</h2>
            <?php if ($active_job): ?>
                <div class="job-card">
                    <p><strong>Status:</strong> <span style="color: #e67e22;"><?php echo htmlspecialchars($active_job['status']); ?></span></p>
                    <p><strong>Customer:</strong> <?php echo htmlspecialchars($active_job['customer_name']); ?></p>
                    <p><strong>Drop-off:</strong> <?php echo htmlspecialchars($active_job['dropoff_location']); ?></p>
                    <br>
                    <?php if ($active_job['status'] == 'Accepted'): ?>
                        <a href="update_status.php?id=<?php echo $active_job['id']; ?>&new_status=Picked Up" style="background: #fcc419; color: black; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">📦 Pick Up Item</a>
                    <?php elseif ($active_job['status'] == 'Picked Up'): ?>
                        <a href="update_status.php?id=<?php echo $active_job['id']; ?>&new_status=Completed" style="background: #4fab4f; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">✅ Confirm Delivery</a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p style="color: #888; background: #f8f9fa; padding: 20px; border-radius: 8px;">You don't have any active deliveries right now.</p>
            <?php endif; ?>
        </div>

        <hr style="margin: 40px 0; border: 0; border-top: 1px solid #eee;">

        <div class="delivery-history">
            <h2>📜 Recent Deliveries</h2>
            <table class="history-table">
                <thead style="background: #f8f9fa;">
                    <tr>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Location</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($history_result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($history_result)): ?>
                            <tr>
                                <td><?php echo date('M d', strtotime($row['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['dropoff_location']); ?></td>
                                <td><span class="status-badge">Completed</span></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align: center; color: #aaa;">No delivery history found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>