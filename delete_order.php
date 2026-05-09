<?php
session_start();
include('Fetch_Manolo.php');

// 1. Check if ID exists in URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("❌ Error: No ID sent from the previous page. Check your link!");
}

$id = mysqli_real_escape_string($conn, $_GET['id']);
echo "🔍 Debug: Attempting to delete Order ID #$id <br>";

// 2. Try the query
$sql = "DELETE FROM orders WHERE id = '$id'";

if (mysqli_query($conn, $sql)) {
    if (mysqli_affected_rows($conn) > 0) {
        echo "✅ Success! Record deleted. Redirecting...";
        header("Refresh: 2; url=user_page.php");
    } else {
        echo "⚠️ The query ran, but NO rows were deleted. Are you sure ID #$id exists in the database?";
    }
} else {
    echo "❌ SQL Error: " . mysqli_error($conn);
}
?>