<?php


session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'rider') {
    header("Location: index.php");
    exit();
}
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

    <h2>Welcome, <span><?= $_SESSION['name']; ?></span></h2>

    <p>Rider Service Dashboard</p>

    <div class="top-buttons">

        <button class="request-btn">
            📦 Service Requests
        </button>

        <button onclick="window.location.href='logout.php'" class="logout-btn">
            Logout
        </button>

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

                <tr>
                    <td>1</td>
                    <td>Juan Dela Cruz</td>
                    <td>Parcel Delivery</td>
                    <td>Manolo Fortich</td>
                    <td>Malaybalay</td>
                    <td><span class="pending">Pending</span></td>

                    <td>
                        <button class="accept-btn">
                            Accept
                        </button>
                    </td>
                </tr>

                <tr>
                    <td>2</td>
                    <td>Maria Santos</td>
                    <td>Food Delivery</td>
                    <td>Tankulan</td>
                    <td>Camp Philips</td>
                    <td><span class="pending">Pending</span></td>

                    <td>
                        <button class="accept-btn">
                            Accept
                        </button>
                    </td>
                </tr>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>