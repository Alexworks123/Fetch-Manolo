<?php
session_start();
include('Fetch_Manolo.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];


$user_query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// SQL JOIN INTEGRATION 

$history_query = "SELECT orders.*, users.name AS rider_name, users.vehicle AS rider_vehicle 
                  FROM orders 
                  LEFT JOIN users ON orders.assigned_rider = users.name 
                  WHERE orders.user_id = '$user_id' 
                  ORDER BY orders.id DESC";
$history_result = mysqli_query($conn, $history_query);


$chart_query = "SELECT service_type, COUNT(*) as count FROM orders WHERE user_id = '$user_id' GROUP BY service_type";
$chart_result = mysqli_query($conn, $chart_query);
$labels = [];
$counts = [];
while($chart_row = mysqli_fetch_assoc($chart_result)) {
    $labels[] = $chart_row['service_type'];
    $counts[] = $chart_row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile | Fetch Manolo</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; margin: 0; padding: 20px; }
        .profile-wrapper { max-width: 1000px; margin: 0 auto; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .profile-header { display: flex; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .avatar { width: 60px; height: 60px; background: #748ffc; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; margin-right: 20px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .info-item label { display: block; font-size: 0.8rem; color: #888; text-transform: uppercase; }
        .info-item p { margin: 5px 0; font-weight: 500; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; background: #f8f9fa; padding: 12px; color: #666; font-size: 0.9rem; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
        .completed { background: #ebfbee; color: #2b8a3e; }
        .btn-blue { background: #4dabf7; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; }
        .btn-delete { color: #fa5252; text-decoration: none; font-weight: bold; }
        .chart-container { max-width: 350px; margin: 0 auto 20px auto; }
    </style>
</head>
<body>

<div class="profile-wrapper">
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div style="background: #ebfbee; color: #2b8a3e; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
            Order successfully removed from history.
        </div>
    <?php endif; ?>

    <h1 style="text-align: center; color: #333;">Welcome, <?php echo htmlspecialchars($user_data['name']); ?></h1>

    <div class="card">
        <h3 style="text-align: center; color: #748ffc;">My Service Usage</h3>
        <div class="chart-container">
            <canvas id="userOrderChart"></canvas>
        </div>
    </div>

    <div class="card">
        <div class="profile-header">
            <div class="avatar"><?php echo substr($user_data['name'], 0, 1); ?></div>
            <h2 style="margin: 0;">User Profile</h2>
        </div>
        <div class="info-grid">
            <div class="info-item"><label>Full Name</label><p><?php echo htmlspecialchars($user_data['name']); ?></p></div>
            <div class="info-item"><label>Email Address</label><p><?php echo htmlspecialchars($user_data['email']); ?></p></div>
        </div>
        <div style="margin-top: 20px; display: flex; gap: 10px;">
            <a href="user_page.php" class="btn-blue">Back to Dashboard</a>
            <a href="logout.php" style="color: #fa5252; text-decoration: none; font-weight: bold; padding-top: 10px;">Logout</a>
        </div>
    </div>

    <div class="card">
        <h3 style="margin-top: 0; color: #333;">📋 Your Order History (SQL JOIN)</h3>
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Rider Info</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($history_result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($history_result)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['service_type']); ?></strong></td>
                            <td>
                                <div><?php echo htmlspecialchars($row['rider_name'] ?? 'Assigning...'); ?></div>
                                <small style="color: #888;"><?php echo htmlspecialchars($row['rider_vehicle'] ?? ''); ?></small>
                            </td>
                            <td>₱<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <span class="badge <?php echo strtolower(str_replace(' ', '', $row['status'])); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="delete_order.php?id=<?php echo $row['id']; ?>&from=user_profile" 
                                   class="btn-delete" 
                                   onclick="return confirm('Delete this record?')">Delete 🗑️</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align: center; padding: 40px; color: #999;">No order history found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const ctx = document.getElementById('userOrderChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut', // Users get a Doughnut chart for variety!
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            data: <?php echo json_encode($counts); ?>,
            backgroundColor: ['#748ffc', '#ff8787', '#63e6be', '#ffd43b', '#da77f2']
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
    }
});
</script>

</body>
</html>