<?php


session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
 <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">
    
    <h1>Welcome, <span>pol</span></h1>
    <p>Manage your account and service requests</p>

    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-pic">P</div>
            <div>
                <h2>User Profile</h2>
                <p style="text-align: left;">Member since May 2026</p>
            </div>
        </div>

        <div class="profile-info-grid">
            <div class="info-item">
                <label>Full Name</label>
                <p>Pol Manolo</p>
            </div>
            <div class="info-item">
                <label>Email Address</label>
                <p>20241516@nbsc.edu.ph</p>
            </div>
            <div class="info-item">
                <label>Phone Number</label>
                <p>09123456789</p>
            </div>
            <div class="info-item">
                <label>Location</label>
                <p>Manolo Fortich</p>
            </div>
        </div>

        <div class="profile-actions">
            <a href="edit_profile.php" style="flex: 1; text-align: center;">Edit Profile</a>
            <a href="add.php" style="flex: 1; text-align: center;">+ Add Service</a>
            <a href="logout.php" style="flex: 1; text-align: center; background: #e03131;">Logout</a>
        </div>
    </div>

</div>

</body>
</html>