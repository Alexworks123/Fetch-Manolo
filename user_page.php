<?php
session_start();
include('Fetch_Manolo.php'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: index.php");
    exit();
}

$user_email = $_SESSION['email']; 
$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE email = '$user_email'"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body><div class="container" style="text-align: center; font-family: sans-serif;">
    
    <h1 style="color: #333; margin-bottom: 5px;">Welcome, <span style="color: #4c6ef5;"><?php echo $user['name']; ?></span></h1>
    <p style="color: #666; font-size: 0.9rem; margin-bottom: 20px;">Find a rider and get things done.</p>

    <div style="margin-bottom: 30px; display: flex; justify-content: center; gap: 10px;">
        <a href="user_profile.php" style="background: #748ffc; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-size: 0.9rem; display: flex; align-items: center; gap: 5px;">
            👤 My Profile
        </a>
        <a href="logout.php" style="background: #748ffc; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none; font-size: 0.9rem;">
            Logout
        </a>
    </div>

    <div style="margin-bottom: 40px;">
        <a href="add.php" style="background: #4c6ef5; color: white; padding: 15px 30px; border-radius: 10px; text-decoration: none; font-weight: bold; font-size: 1.1rem; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            🛵 Request a Rider
        </a>
    </div>

    <h3 style="color: #333; margin-bottom: 20px;">Available Riders</h3>

   <div class="table-container" style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 20px;">
    <h3 style="text-align: left; color: #333; margin-bottom: 15px; font-family: sans-serif;">Available Riders</h3>
    
    <table style="width: 100%; border-collapse: collapse; font-family: sans-serif;">
        <thead>
            <tr style="border-bottom: 2px solid #eee;">
                <th style="padding: 12px; text-align: left; color: #555; font-size: 0.9rem;">No.</th>
                <th style="padding: 12px; text-align: left; color: #555; font-size: 0.9rem;">Rider Name</th>
                <th style="padding: 12px; text-align: left; color: #555; font-size: 0.9rem;">Location</th>
                <th style="padding: 12px; text-align: left; color: #555; font-size: 0.9rem;">Status</th>
                <th style="padding: 12px; text-align: left; color: #555; font-size: 0.9rem;">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $rider_query = "SELECT name, address FROM users WHERE role = 'rider'";
            $rider_result = mysqli_query($conn, $rider_query);
            $count = 1;

            if (mysqli_num_rows($rider_result) > 0) {
                while ($rider = mysqli_fetch_assoc($rider_result)) {
            ?>
                <tr>
                    <td style="padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 0.9rem;"><?php echo $count++; ?></td>
                    <td style="padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 0.9rem; font-weight: 500;"><?php echo htmlspecialchars($rider['name']); ?></td>
                    <td style="padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 0.9rem; color: #666;"><?php echo htmlspecialchars($rider['address']); ?></td>
                    <td style="padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 0.9rem;"><span style="color: #748ffc; font-weight: 500;">Online</span></td>
                    <td style="padding: 15px 12px; border-bottom: 1px solid #eee; font-size: 0.9rem;">
                        <a href="add.php?rider_name=<?php echo urlencode($rider['name']); ?>" 
                           style="background-color: #748ffc; color: white !important; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; text-decoration: none; font-weight: 500; display: inline-block;">
                           Direct Order
                        </a>
                    </td>
                </tr>
            <?php 
                }
            } else {
                echo "<tr><td colspan='5' style='text-align:center; padding: 20px; color: #888;'>No riders available at the moment.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>
</div>
</body>
</html>