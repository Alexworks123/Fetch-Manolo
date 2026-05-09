<?php
session_start();
include('Fetch_Manolo.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    header("Location: index.php");
    exit();
}

$rider_name = $_SESSION['name'];


$active_query = "SELECT * FROM orders WHERE assigned_rider = '$rider_name' AND status = 'Accepted' LIMIT 1";
$active_result = mysqli_query($conn, $active_query);
$active_job = mysqli_fetch_assoc($active_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Rider Profile</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="profile-container">
        <h1>Rider Profile: <?php echo htmlspecialchars($rider_name); ?></h1>
        
        <div class="active-delivery">
            <h2>📦 Current Delivery</h2>
            <?php if ($active_job): ?>
               <div class="job-card">
    <p><strong>Status:</strong> <?php echo $active_job['status']; ?></p>
    
    <?php if ($active_job['status'] == 'Accepted'): ?>
        <a href="update_status.php?id=<?php echo $active_job['id']; ?>&new_status=Picked Up" class="pickup-btn" style="background: #fcc419; color: black; padding: 10px; text-decoration: none; border-radius: 5px;">
            📦 I have Picked Up the Item
        </a>

    <?php elseif ($active_job['status'] == 'Picked Up'): ?>
        <a href="update_status.php?id=<?php echo $active_job['id']; ?>&new_status=Completed" class="complete-btn" style="background: #4fab4f; color: white; padding: 10px; text-decoration: none; border-radius: 5px;">
            ✅ Mark as Delivered
        </a>
    <?php endif; ?>

    </div>
</body>
</html>