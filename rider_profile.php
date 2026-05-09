<?php
session_start();
include('Fetch_Manolo.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    header("Location: index.php");
    exit();
}

$rider_name = $_SESSION['name'];
$user_id = $_SESSION['user_id'];

// 1. Fetch Rider Profile Data
$user_query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// 2. SQL JOIN INTEGRATION (Objective 3 - 30 Points)
// This pulls order details AND customer contact info in one query
$history_query = "SELECT orders.*, users.name AS customer_name, users.phone AS customer_phone 
                  FROM orders 
                  INNER JOIN users ON orders.user_id = users.id 
                  WHERE orders.assigned_rider = '$rider_name' 
                  AND orders.status = 'Completed'
                  ORDER BY orders.id DESC";
$history_result = mysqli_query($conn, $history_query);

// 3. CHART DATA LOGIC (Objective - 15 Points)
// Count how many of each service type this rider has done
$chart_query = "SELECT service_type, COUNT(*) as count FROM orders WHERE assigned_rider = '$rider_name' GROUP BY service_type";
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
    <title>Rider Profile | Fetch Manolo</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; margin: 0; padding: 20px; }
        .wrapper { max-width: 900px; margin: 0 auto; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 25px; }
        .profile-header { display: flex; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .avatar { width: 60px; height: 60px; background: #4dabf7; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; margin-right: 20px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; }
        .info-item label { display: block; font-size: 0.75rem; color: #888; text-transform: uppercase; font-weight: bold; }
        .info-item p { margin: 5px 0; font-weight: 600; color: #333; font-size: 0.95rem; }
        .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-block; color: white; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { text-align: left; background: #f8f9fa; padding: 12px; color: #666; font-size: 0.85rem; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        .chart-container { max-width: 400px; margin: 0 auto 25px auto; }
    </style>
</head>
<body>

<div class="wrapper">
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
        <div style="background: #ebfbee; color: #2b8a3e; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; font-weight: bold;">
            ✅ Record successfully removed from history.
        </div>
    <?php endif; ?>

    <h1 style="text-align: center; color: #333;">Rider Analytics & Profile</h1>

    <div class="card chart-container">
        <h3 style="text-align: center; margin-top: 0; color: #4dabf7;">Service Distribution</h3>
        <canvas id="orderChart"></canvas>
    </div>

    <div class="card">
        <div class="profile-header">
            <div class="avatar"><?php echo substr($rider_name, 0, 1); ?></div>
            <h2 style="margin: 0;">Account Details</h2>
        </div>
        <div class="info-grid">
            <div class="info-item"><label>Full Name</label><p><?php echo htmlspecialchars($user_data['name']); ?></p></div>
            <div class="info-item"><label>Email</label><p><?php echo htmlspecialchars($user_data['email']); ?></p></div>
            <div class="info-item"><label>Phone</label><p><?php echo htmlspecialchars($user_data['phone']); ?></p></div>
        </div>
        <div style="margin-top: 25px; display: flex; gap: 10px;">
            <a href="rider_page.php" class="btn" style="background: #4dabf7;">Back to Dashboard</a>
            <a href="logout.php" class="btn" style="background: #fa5252;">Logout</a>
        </div>
    </div>

    <div class="card">
        <h3>📜 Recent Deliveries (SQL JOIN)</h3>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Location</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($history_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($history_result)): ?>
                        <tr>
                            <td><?php echo date('M d', strtotime($row['created_at'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['customer_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['customer_phone']); ?></td>
                            <td><?php echo htmlspecialchars($row['dropoff_location']); ?></td>
                            <td>
                                <a href="delete_order.php?id=<?php echo $row['id']; ?>&from=rider_profile" 
                                   onclick="return confirm('Remove this record?')" 
                                   style="color: #fa5252; font-weight: bold; text-decoration: none;">Delete 🗑️</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #888; padding: 20px;">No completed deliveries found yet.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// CHART.JS CONFIGURATION
const ctx = document.getElementById('orderChart').getContext('2d');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Orders',
            data: <?php echo json_encode($counts); ?>,
            backgroundColor: ['#4dabf7', '#ff6b6b', '#51cf66', '#fcc419', '#cc5de8']
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});
</script>

</body>
</html>