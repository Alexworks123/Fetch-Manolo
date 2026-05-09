<?php
session_start();
include('Fetch_Manolo.php'); // Ensure this file has your $conn connection

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    header("Location: index.php");
    exit();
}

// Fetch only 'Pending' orders to show on the dashboard
$query = "SELECT * FROM orders WHERE status = 'Pending' ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rider Page</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="dashboard">
    <h1>FETCH MANOLO</h1>
    <h2>Welcome, <span><?= htmlspecialchars($_SESSION['name']); ?></span></h2>
    <p>Rider Service Dashboard</p>

    <div class="top-buttons">
        <button class="request-btn">📦 Service Requests</button>
        <button onclick="window.location.href='logout.php'" class="logout-btn">Logout</button>
    </div>

    <div class="table-container">
        <h3>Available Bookings</h3>
        <table>
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Customer</th>
                    <th>Service</th>
                    <th>Pick-up</th>
                    <th>Drop-off</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $count = 1;
                if (mysqli_num_rows($result) > 0):
                    while($row = mysqli_fetch_assoc($result)): 
                ?>
                <tr>
                    <td><?= $count++; ?></td>
                    <td><?= htmlspecialchars($row['customer_name']); ?></td>
                    <td><?= htmlspecialchars($row['service_type']); ?></td>
                    <td><?= htmlspecialchars($row['pickup_location']); ?></td>
                    <td><?= htmlspecialchars($row['dropoff_location']); ?></td>
                    <td><span class="pending"><?= htmlspecialchars($row['status']); ?></span></td>
                    <td>
                        <button class="accept-btn" onclick="window.location.href='accept_order.php?id=<?= $row['id']; ?>'">
                            Accept
                        </button>
                    </td>
                </tr>
                <?php 
                    endwhile; 
                else: 
                ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding: 20px;">No available bookings at the moment.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>