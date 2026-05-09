<?php
session_start();
include('Fetch_Manolo.php'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}


$user_email = $_SESSION['email']; 
$query = "SELECT * FROM users WHERE email = '$user_email'";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);


$full_name = $user['name'];
$initial = strtoupper(substr($full_name, 0, 1)); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Page</title>
 <link rel="stylesheet" href="style.css">
</head>
<body><div class="container">
    
    
    <h1>Welcome, <span><?php echo $full_name; ?></span></h1>
    <p>Manage your account information</p>

    <div class="profile-container">
        
        <div class="profile-header">
        
            <div class="profile-pic"><?php echo $initial; ?></div>
            <div>
                <h2>User Profile</h2>
              
            </div>
        </div>

        <div class="profile-info-grid">
            <div class="info-item">
                <label>Full Name</label>
               
                <p><?php echo $full_name; ?></p>
            </div>
            <div class="info-item">
                <label>Email Address</label>
             
                <p><?php echo $user['email']; ?></p>
            </div>
            <div class="info-item">
                <label>Phone Number</label>
        
          <?php echo isset($_SESSION['phone']) ? $_SESSION['phone'] : 'No phone number'; ?>
            </div>
            <div class="info-item">
                <label>Location</label>
              
                <p><?php echo $user['address']; ?></p>
            </div>
        </div>
        
     

        <div class="profile-actions">
            <a href="edit_profile.php" style="flex: 1; text-align: center;">Edit Profile</a>
          <a href="user_page.php" style="flex: 1; text-align: center; background: #339af0; color: white; padding: 10px; text-decoration: none; border-radius: 4px; font-weight: 500;">Back to Dashboard</a>
    
    <a href="logout.php" style="flex: 1; text-align: center; background: #e03131; color: white; padding: 10px; text-decoration: none; border-radius: 4px; font-weight: 500;">Logout</a>

        </div>
    </div>

</div>

</body>
</html>