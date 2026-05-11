<?php
session_start();
include('Fetch_Manolo.php'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    header("Location: index.php");
    exit();
}

$current_rider_name = $_SESSION['name']; 

// Check if rider is currently on a job
$check_busy = mysqli_query($conn, "SELECT id FROM orders WHERE assigned_rider = '$current_rider_name' AND (status = 'Accepted' OR status = 'Picked Up')");
$is_busy = mysqli_num_rows($check_busy) > 0;

// Fetching Available Bookings
$query = "SELECT * FROM orders 
          WHERE status = 'Pending' 
          AND (assigned_rider = '' OR assigned_rider IS NULL OR assigned_rider = '$current_rider_name') 
          ORDER BY created_at DESC";

$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Page | Fetch Manolo</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .service-tag {
            background: #e7f5ff;
            color: #228be6;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.8rem;
            border: 1px solid #74c0fc;
            display: inline-block;
            text-transform: uppercase;
        }
        .update-card {
            max-width: 1000px; 
            margin: 0 auto 20px; 
            padding: 20px; 
            background: white; 
            border-radius: 12px; 
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<div class="dashboard" style="padding: 20px;">
    <div style="text-align: center;">
        <h1>FETCH MANOLO</h1>
        <h2>Welcome, <span><?= htmlspecialchars($_SESSION['name']); ?></span></h2>
        <p>Rider Service Dashboard</p>
    </div>

    <?php if(isset($_GET['update']) && $_GET['update'] == 'success'): ?>
        <div style="max-width: 1000px; margin: 10px auto; background: #ebfbee; color: #2b8a3e; padding: 10px; border-radius: 8px; text-align: center; font-weight: bold; border: 1px solid #c3fae8;">
            ✅ Location updated successfully!
        </div>
    <?php endif; ?>

    <div class="header-nav" style="display: flex; justify-content: flex-start; gap: 10px; max-width: 1000px; margin: 20px auto;">
        <button onclick="window.location.href='rider_profile.php'" class="btn-profile" style="background: #4dabf7; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">👤 My Profile</button>
        <button onclick="window.location.reload();" class="btn-refresh" style="background: #748ffc; color: white; padding: 12px 20px; border: none; border-radius: 8px; cursor: pointer; font-weight: bold;">🔄 Refresh</button>
        <a href="logout.php" style="background: #ff6b6b; color: white; padding: 12px 20px; border-radius: 8px; text-decoration: none; font-weight: bold;">Logout</a>
    </div>

    <div class="update-card">
        <h3 style="margin-top: 0; color: #333;">📍 Set Your Current Location</h3>
        <p style="font-size: 0.85rem; color: #666; margin-bottom: 15px;">Update this so users nearby can find and book you directly.</p>
        <form method="POST" action="update_location.php" style="display: flex; gap: 10px;">
            <input type="text" name="current_location" placeholder="Where are you now? (e.g., Alae, Dicklum, Zone 1)" required 
                   style="flex: 1; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
            <button type="submit" style="background: #4dabf7; color: white; border: none; padding: 0 25px; border-radius: 8px; cursor: pointer; font-weight: bold; font-size: 1rem;">
                Update
            </button>
        </form>
    </div>

    <div class="table-container" style="max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0;">Available Bookings</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; text-align: left;">
                    <th style="padding: 12px;">No.</th>
                    <th>Customer</th>
                    <th>Service Type</th>
                    <th>Pick-up</th>
                    <th>Drop-off</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $count = 1;
                if (mysqli_num_rows($result) > 0):
                   while ($row = mysqli_fetch_assoc($result)):
                    $orderType = (empty($row['assigned_rider'])) ? "Global" : "Direct";
                    $typeColor = ($orderType == "Direct") ? "#ff6b6b" : "#748ffc";
                ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding: 15px 12px;"><?php echo $count++; ?></td>
                    <td><strong><?php echo htmlspecialchars($row['customer_name']); ?></strong></td>
                    <td>
                        <span class="service-tag"><?php echo htmlspecialchars($row['service_type'] ?? 'General'); ?></span>
                        <br>
                        <small style="color: <?php echo $typeColor; ?>; font-weight: bold;"><?php echo $orderType; ?></small>
                    </td>
                    <td><?php echo htmlspecialchars($row['pickup_location']); ?></td>
                    <td><?php echo htmlspecialchars($row['dropoff_location']); ?></td>
                    <td>
                        <?php if (!$is_busy): ?>
                            <a href="accept_order.php?id=<?php echo $row['id']; ?>" 
                               style="background: #40c057; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: bold;">
                               Accept
                            </a>
                            <a href="decline_order.php?id=<?php echo $row['id']; ?>" 
                               onclick="return confirm('Decline this booking?')" 
                               style="color: #fa5252; font-size: 0.85rem; margin-left: 10px; text-decoration: none; font-weight: bold;">
                               Decline
                            </a>
                        <?php else: ?>
                            <span style="color: #adb5bd; font-size: 0.8rem; font-style: italic;">Finish current job</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
                <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding: 40px; color: #888;">
                        ✨ No available bookings at the moment.
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>