<?php
session_start();
include('Fetch_Manolo.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// --- PROFILE UPDATE LOGIC ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $new_phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $new_address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $update_sql = "UPDATE users SET phone='$new_phone', address='$new_address' WHERE id='$user_id'";
    if (mysqli_query($conn, $update_sql)) {
        header("Location: user_profile.php?msg=updated");
        exit();
    }
}

// Fetch current user details
$user_query = "SELECT * FROM users WHERE id = '$user_id'";
$user_result = mysqli_query($conn, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// --- ORDER HISTORY QUERY ---
// Joins orders with users and rider_details to get the name and vehicle type
$history_query = "SELECT orders.*, users.name as rider_name, rider_details.vehicle as rider_vehicle 
                  FROM orders 
                  LEFT JOIN users ON orders.assigned_rider = users.name 
                  LEFT JOIN rider_details ON users.id = rider_details.user_id
                  WHERE orders.user_id = '$user_id' 
                  AND orders.status IN ('DELIVERED', 'CANCELLED')
                  ORDER BY orders.id DESC";

$history_result = mysqli_query($conn, $history_query);

// --- CHART DATA QUERY ---
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
        .wrapper { max-width: 900px; margin: 0 auto; }
        .card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 25px; }
        .profile-header { display: flex; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .avatar { width: 60px; height: 60px; background: #748ffc; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; margin-right: 20px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .info-item label { display: block; font-size: 0.75rem; color: #888; text-transform: uppercase; font-weight: bold; }
        .info-item p { margin: 5px 0; font-weight: 600; color: #333; }
        .edit-input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 5px; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; font-size: 0.9rem; }
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
        
        /* Badge Colors */
        .delivered { background: #ebfbee; color: #2b8a3e; }
        .cancelled { background: #fff5f5; color: #fa5252; }
        
        .chart-container { max-width: 350px; margin: 0 auto 20px auto; }
        #searchInput { padding: 10px; width: 100%; border: 1px solid #ddd; border-radius: 8px; margin-top: 15px; box-sizing: border-box; }
        .btn-save { background: #51cf66; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: bold; cursor: pointer; margin-top: 10px; }
    </style>
</head>
<body>

<div class="wrapper">
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'updated'): ?>
        <div style="background: #ebfbee; color: #2b8a3e; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
            Profile updated successfully!
        </div>
    <?php endif; ?>

    <h1 style="text-align: center; color: #333;">Welcome, <?php echo htmlspecialchars($user_data['name']); ?></h1>

    <div class="card chart-container">
        <h3 style="text-align: center; color: #748ffc;">Service Usage Analysis</h3>
        <canvas id="userOrderChart"></canvas>
    </div>

    <div class="card">
        <div class="profile-header">
            <div class="avatar"><?php echo substr($user_data['name'], 0, 1); ?></div>
            <h2 style="margin: 0;">Account Information</h2>
        </div>
        <form method="POST">
            <div class="info-grid">
                <div class="info-item"><label>Full Name</label><p><?php echo htmlspecialchars($user_data['name']); ?></p></div>
                <div class="info-item"><label>Email</label><p><?php echo htmlspecialchars($user_data['email']); ?></p></div>
                <div class="info-item">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="edit-input" value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>">
                </div>
                <div class="info-item">
                    <label>Address</label>
                    <input type="text" name="address" class="edit-input" value="<?php echo htmlspecialchars($user_data['address'] ?? ''); ?>">
                </div>
            </div>
            <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                <button type="submit" name="update_profile" class="btn-save">Update Profile</button>
                <a href="logout.php" style="background: #ff6b6b; color: white; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; margin-top: 10px;">Logout</a>
                <a href="user_page.php" style="margin-top: 10px; text-decoration:none; color:#888;">Back to Dashboard</a>
            </div>
        </form>
        <input type="text" id="searchInput" onkeyup="filterTable()" placeholder="Search order history by service or rider...">
    </div>

    <div class="card">
        <h3>Order History</h3>
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
            <tbody id="historyTable">
                <?php if(mysqli_num_rows($history_result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($history_result)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['service_type']); ?></strong></td>
                            <td>
                                <div><?php echo htmlspecialchars($row['rider_name'] ?? 'N/A'); ?></div>
                                <small style="color: #888;"><?php echo htmlspecialchars($row['rider_vehicle'] ?? ''); ?></small>
                            </td>
                            <td>₱<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <span class="badge <?php echo strtolower($row['status']); ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="delete_order.php?id=<?php echo $row['id']; ?>&from=user_profile" 
                                   onclick="return confirm('Delete this record from history?')" 
                                   style="color: #fa5252; text-decoration: none; font-weight: bold;">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; color: #888; padding: 20px;">No history records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// --- Chart.js Logic ---
const ctx = document.getElementById('userOrderChart').getContext('2d');
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

// --- Real-time Search Logic ---
function filterTable() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toLowerCase();
    let table = document.getElementById("historyTable");
    let tr = table.getElementsByTagName("tr");
    
    for (let i = 0; i < tr.length; i++) {
        let textContent = tr[i].innerText.toLowerCase();
        tr[i].style.display = textContent.includes(filter) ? "" : "none";
    }
}
</script>

</body>
</html>