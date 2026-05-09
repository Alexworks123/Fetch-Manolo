<?php
session_start();
include('Fetch_Manolo.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$rider_name = $_SESSION['name'];


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_rider'])) {
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $vehicle = mysqli_real_escape_string($conn, $_POST['vehicle']);
    $plate = mysqli_real_escape_string($conn, $_POST['plate_number']);
    
    $update_sql = "UPDATE users SET phone='$phone', vehicle='$vehicle', plate_number='$plate' WHERE id='$user_id'";
    mysqli_query($conn, $update_sql);
    header("Location: rider_profile.php?msg=updated");
    exit();
}


$user_query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// SQL JOIN
$history_query = "SELECT orders.*, users.name AS customer_name, users.phone AS customer_phone 
                  FROM orders 
                  INNER JOIN users ON orders.user_id = users.id 
                  WHERE orders.assigned_rider = '$rider_name' 
                  ORDER BY orders.id DESC";
$history_result = mysqli_query($conn, $history_query);

// CHART DATA pie
$chart_query = "SELECT service_type, COUNT(*) as count FROM orders WHERE assigned_rider = '$rider_name' GROUP BY service_type";
$chart_result = mysqli_query($conn, $chart_query);
$labels = []; $counts = [];
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
        .wrapper { max-width: 1000px; margin: 0 auto; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 25px; }
        .profile-header { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; border-bottom: 1px solid #eee; padding-bottom: 20px; }
        .profile-icon { width: 70px; height: 70px; background: linear-gradient(135deg, #4c6ef5, #748ffc); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; font-weight: bold; }
        .info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 20px; text-align: left; }
        .info-item label { display: block; font-size: 0.7rem; color: #888; text-transform: uppercase; font-weight: bold; margin-bottom: 5px; }
        .info-item p, .info-item span { font-weight: 600; color: #333; font-size: 0.95rem; margin: 0; }
        .edit-input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; font-size: 0.9rem; box-sizing: border-box; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; }
        .completed { background: #ebfbee; color: #2b8a3e; }
        .btn-save { background: #4dabf7; color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: bold; cursor: pointer; transition: 0.3s; }
        .btn-save:hover { background: #339af0; }
        .chart-container { max-width: 400px; margin: 0 auto 25px auto; }
        .delete-btn { background: #ff6b6b; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: bold; }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="card chart-container">
        <h3 style="text-align: center; color: #4dabf7; margin-bottom: 20px;">Performance Overview</h3>
        <canvas id="orderChart"></canvas>
    </div>

    <div class="card">
        <div class="profile-header">
            <div class="profile-icon">
                <?php echo strtoupper(substr($user_data['name'], 0, 1)); ?>
            </div>
            <div>
                <h2 style="margin: 0;">Rider Professional Profile</h2>
                <p style="margin: 5px 0 0; color: #666;">ID: #<?php echo $user_id; ?> | Status: Verified</p>
            </div>
        </div>

        <form method="POST">
            <div class="info-grid">
                <div class="info-item"><label>Full Name</label><p><?php echo htmlspecialchars($user_data['name']); ?></p></div>
                <div class="info-item"><label>Email Address</label><p><?php echo htmlspecialchars($user_data['email']); ?></p></div>
                <div class="info-item">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="edit-input" value="<?php echo htmlspecialchars($user_data['phone']); ?>">
                </div>

                <div class="info-item">
                    <label>License Number</label>
                    <span><?php echo htmlspecialchars($user_data['license'] ?? 'N/A'); ?></span>
                </div>
                <div class="info-item">
                    <label>Vehicle Type / Model</label>
                    <input type="text" name="vehicle" class="edit-input" value="<?php echo htmlspecialchars($user_data['vehicle'] ?? ''); ?>">
                </div>
                <div class="info-item">
                    <label>Plate Number</label>
                    <input type="text" name="plate_number" class="edit-input" value="<?php echo htmlspecialchars($user_data['plate'] ?? ''); ?>">
                </div>
                
                <div class="info-item">
                    <label>Barangay Address</label>
                    <p><?php echo htmlspecialchars($user_data['address'] ?? 'N/A'); ?></p>
                </div>
            </div>
            
            <div style="margin-top: 20px;">
                <button type="submit" name="update_rider" class="btn-save">Update Profile Details</button>
                   <a href="logout.php" style="background: #ff6b6b; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; display: inline-flex; align-items: center; gap: 8px; margin-top: 10px;"> Logout</a>
                <a href="rider_page.php" style="margin-left:15px; text-decoration:none; color:#666;">Return to Dashboard</a>
                                   
        </form>
    </div>

    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 style="margin: 0;">Delivery History</h3>
            <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="🔍 Search history..." style="width:250px; padding:10px; border-radius:8px; border:1px solid #ddd;">
        </div>
        
        <table>
            <thead>
                <tr style="background: #f8f9fa;">
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($history_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($history_result)): ?>
                        <tr>
                            <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['customer_name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['dropoff_location']); ?></td>
                            <td><span class="badge completed"><?php echo $row['status']; ?></span></td>
                            <td><a href="delete_order.php?id=<?php echo $row['id']; ?>&from=rider_profile" class="delete-btn" onclick="return confirm('Delete this record?')">Delete</a></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; padding: 20px; color: #888;">No completed deliveries yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Chart.js Configuration
const ctx = document.getElementById('orderChart').getContext('2d');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            data: <?php echo json_encode($counts); ?>,
            backgroundColor: ['#4dabf7', '#ff6b6b', '#51cf66', '#fcc419', '#cc5de8'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});


function filterTable() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toLowerCase();
    let tr = document.querySelectorAll("table tbody tr");
    tr.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? "" : "none";
    });
}
</script>
</body>
</html>