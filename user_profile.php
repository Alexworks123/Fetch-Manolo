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


$history_query = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY id DESC";
$history_result = mysqli_query($conn, $history_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile | Fetch Manolo</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; margin: 0; padding: 20px; }
        .profile-wrapper { max-width: 900px; margin: 0 auto; }
        
    
        .profile-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .profile-header { display: flex; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
        .avatar { width: 60px; height: 60px; background: #748ffc; color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: bold; margin-right: 20px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; text-align: left; }
        .info-item label { display: block; font-size: 0.8rem; color: #888; text-transform: uppercase; }
        .info-item p { margin: 5px 0; font-weight: 500; color: #333; }

       
        .history-card { background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { text-align: left; background: #f8f9fa; padding: 12px; color: #666; font-size: 0.9rem; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        
       
        .badge { padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: bold; text-transform: uppercase; }
        .pending { background: #fff4e6; color: #fd7e14; }
        .accepted { background: #e7f5ff; color: #228be6; }
        .pickedup { background: #fff9db; color: #f59f00; }
        .completed { background: #ebfbee; color: #2b8a3e; }

        .btn-row { margin-top: 20px; display: flex; gap: 10px; }
        .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: bold; font-size: 0.9rem; }
        .btn-blue { background: #4dabf7; color: white; }
        .btn-red { background: #fa5252; color: white; }
    </style>
</head>
<body>

<div class="profile-wrapper">
    <h1 style="text-align: center; color: #333;">Welcome, <?php echo htmlspecialchars($user_data['name']); ?></h1>
    <p style="text-align: center; color: #666; margin-bottom: 40px;">Manage your account and view order history</p>

    <div class="profile-card">
        <div class="profile-header">
            <div class="avatar"><?php echo substr($user_data['name'], 0, 1); ?></div>
            <h2 style="margin: 0;">User Profile</h2>
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
                <p><?php echo htmlspecialchars($user_data['phone'] ?? 'Not Set'); ?></p>
            </div>
            <div class="info-item">
                <label>Location</label>
                <p><?php echo htmlspecialchars($user_data['address'] ?? 'Not Set'); ?></p>
            </div>
        </div>

        <div class="btn-row">
            <a href="user_page.php" class="btn btn-blue">Back to Dashboard</a>
            <a href="logout.php" class="btn btn-red">Logout</a>
        </div>
    </div>

    <div class="history-card">
        <h3 style="margin-top: 0; color: #333;">📋 Your Order History</h3>
        <table>
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Pick-up</th>
                    <th>Drop-off</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($history_result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($history_result)): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['service_type']); ?></strong></td>
                            <td style="color: #666;"><?php echo htmlspecialchars($row['pickup_location']); ?></td>
                            <td style="color: #666;"><?php echo htmlspecialchars($row['dropoff_location']); ?></td>
                            <td>₱<?php echo number_format($row['price'], 2); ?></td>
                            <td>
                                <span class="badge <?php echo strtolower(str_replace(' ', '', $row['status'])); ?>">
                                    <?php echo htmlspecialchars($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #999;">
                            No order history found. Start by requesting a rider!
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>