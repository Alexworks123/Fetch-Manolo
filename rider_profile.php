<?php
session_start();
include('Fetch_Manolo.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$rider_name = $_SESSION['name'];

// --- UPDATE LOGIC ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_rider'])) {
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $vehicle = mysqli_real_escape_string($conn, $_POST['vehicle']);
    $plate = mysqli_real_escape_string($conn, $_POST['plate_number']);
    
    mysqli_query($conn, "UPDATE users SET phone='$phone' WHERE id='$user_id'");
    mysqli_query($conn, "UPDATE rider_details SET vehicle='$vehicle', plate='$plate' WHERE user_id='$user_id'");

    header("Location: rider_profile.php?msg=updated");
    exit();
}

// --- FETCH DATA ---
$user_query = "SELECT u.*, r.license, r.vehicle, r.plate 
               FROM users u 
               LEFT JOIN rider_details r ON u.id = r.user_id 
               WHERE u.id = '$user_id'";
$user_data = mysqli_fetch_assoc(mysqli_query($conn, $user_query));

$active_query = "SELECT * FROM orders WHERE assigned_rider = '$rider_name' AND (status = 'Accepted' OR status = 'Picked Up') LIMIT 1";
$active_order = mysqli_fetch_assoc(mysqli_query($conn, $active_query));

// Updated query to ensure price and service_type are included
$history_query = "SELECT orders.*, users.name AS customer_name FROM orders 
                  INNER JOIN users ON orders.user_id = users.id 
                  WHERE orders.assigned_rider = '$rider_name' 
                  AND orders.status IN ('DELIVERED', 'CANCELLED')
                  ORDER BY orders.id DESC";
$history_result = mysqli_query($conn, $history_query);

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
        .wrapper { max-width: 900px; margin: 0 auto; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 25px; }
        .profile-header { display: flex; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .avatar { width: 60px; height: 60px; background: #748ffc; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; margin-right: 20px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .info-item label { display: block; font-size: 0.75rem; color: #888; text-transform: uppercase; font-weight: bold; }
        .info-item p { margin: 5px 0; font-weight: 600; color: #333; }
        .edit-input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { padding: 12px; border-bottom: 1px solid #eee; text-align: left; font-size: 0.75rem; color: #888; text-transform: uppercase; }
        td { padding: 15px 12px; border-bottom: 1px solid #eee; text-align: left; font-size: 0.9rem; }
        
        /* Matching the user profile badges */
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
        .delivered { background: #ebfbee; color: #40c057; }
        .cancelled { background: #fff5f5; color: #fa5252; }
        
        .chart-container { max-width: 350px; margin: 0 auto 20px auto; }
        #searchInput { padding: 10px; width: 100%; border: 1px solid #ddd; border-radius: 8px; margin-top: 15px; box-sizing: border-box; }
        .btn-save { background: #4dabf7; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px; }
        .active-card { border-left: 8px solid #4dabf7; background: #e7f5ff; border: 1px solid #a5d8ff; }
    </style>
</head>
<body>

<div class="wrapper">
   

    <h1 style="text-align: center; color: #333;">Welcome, Rider <?php echo htmlspecialchars($user_data['name']); ?></h1>

    <div class="card chart-container">
        <h3 style="text-align: center; color: #748ffc;">Delivery Analysis</h3>
        <canvas id="orderChart"></canvas>
    </div>

    <div class="card">
        <div class="profile-header">
            <div class="avatar"><?php echo strtoupper(substr($user_data['name'], 0, 1)); ?></div>
            <div>
                <h2 style="margin: 0;">Professional Information</h2>
                <p style="margin:0; color:#888; font-size: 0.8rem;">ID: #<?php echo $user_id; ?> | Status: Verified Rider</p>
            </div>
        </div>
        <form method="POST">
            <div class="info-grid">
                <div class="info-item"><label>Full Name</label><p><?php echo htmlspecialchars($user_data['name']); ?></p></div>
                <div class="info-item"><label>License Number</label><p><?php echo htmlspecialchars($user_data['license'] ?? 'N/A'); ?></p></div>
                <div class="info-item"><label>Phone Number</label><input type="text" name="phone" class="edit-input" value="<?php echo htmlspecialchars($user_data['phone']); ?>"></div>
                <div class="info-item"><label>Vehicle Model</label><input type="text" name="vehicle" class="edit-input" value="<?php echo htmlspecialchars($user_data['vehicle'] ?? ''); ?>"></div>
                <div class="info-item"><label>Plate Number</label><input type="text" name="plate_number" class="edit-input" value="<?php echo htmlspecialchars($user_data['plate'] ?? ''); ?>"></div>
            </div>
            <div style="margin-top: 20px; display: flex; gap: 10px; align-items: center;">
                <button type="submit" name="update_rider" class="btn-save">Update Profile</button>
                <a href="logout.php" style="background: #ff6b6b; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 10px;">Logout</a>
                <a href="rider_page.php" style="margin-top: 10px; text-decoration:none; color:#888;">Back to Dashboard</a>
            </div>
        </form>
        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search delivery history by service or customer...">
    </div>
    
     <?php if ($active_order): ?>
    <div class="card active-card">
        <h3 style="margin-top:0; color: #1c7ed6;">🚀 Current Active Task</h3>
        <p><strong>Customer:</strong> <?php echo htmlspecialchars($active_order['customer_name'] ?? 'Finding...'); ?> | <strong>Status:</strong> <?php echo $active_order['status']; ?></p>
        <div style="margin-top: 10px;">
            <?php if ($active_order['status'] == 'Accepted'): ?>
                <a href="update_status.php?id=<?php echo $active_order['id']; ?>&status=Picked Up" style="background: #51cf66; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; color: white;">Confirm Pick-up</a>
            <?php elseif ($active_order['status'] == 'Picked Up'): ?>
                <a href="update_status.php?id=<?php echo $active_order['id']; ?>&status=Delivered" style="background: #7950f2; padding: 10px 15px; border-radius: 6px; text-decoration: none; font-weight: bold; color: white;">Mark Delivered</a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="card">
        <h3>Delivery History</h3>
        <table>
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="border-radius: 8px 0 0 0;">Service</th>
                    <th>Customer</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th style="border-radius: 0 8px 0 0;">Action</th>
                </tr>
            </thead>
            <tbody id="historyTable">
                <?php if (mysqli_num_rows($history_result) > 0): ?>
                    <?php while ($row = mysqli_fetch_assoc($history_result)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['service_type']); ?></strong></td>
                            <td>
                                <div><strong><?php echo htmlspecialchars($row['customer_name']); ?></strong></div>
                                <small style="color: #888;">Customer</small>
                            </td>
                            <td>₱<?php echo number_format($row['price'], 2); ?></td>
                            <td><span class="badge <?php echo strtolower($row['status']); ?>"><?php echo $row['status']; ?></span></td>
                            <td>
                                <a href="delete_order.php?id=<?php echo $row['id']; ?>" 
                                   onclick="return confirm('Delete this record?')" 
                                   style="color: #ff6b6b; text-decoration: none; font-weight: bold;">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" style="text-align:center; padding: 30px; color:#ccc;">No completed tasks yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const ctx = document.getElementById('orderChart').getContext('2d');
new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            data: <?php echo json_encode($counts); ?>,
            backgroundColor: ['#748ffc', '#ff8787', '#63e6be', '#ffd43b', '#da77f2']
        }]
    },
    options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
});

function filterTable() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toLowerCase();
    let rows = document.getElementById("historyTable").getElementsByTagName("tr");
    for (let row of rows) {
        row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
    }
}
</script>
</body>
</html>