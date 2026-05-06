<?php
<<<<<<< HEAD

session_start();
require_once 'config.php';

if (isset($_POST['register'])) {
=======
session_start();
require_once 'config.php';

// --- REGISTRATION LOGIC ---
if (isset($_POST['Register'])) { // Capital 'R' to match your HTML button
>>>>>>> 110cb44 (Fixing the the connection to database and Successfull)
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

<<<<<<< HEAD
    $checkEmail = $conn->query("SELECT email FROM users WHERE email = '$email'");
    if ($checkEmail->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered!';
        $_SESSION['active_form'] = 'register';
    } else {
        $conn->query("INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', '$role')");
    }

    header("Location: index.php");
    exit();
}
=======
    // 1. Check if email already exists
    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered!';
        $_SESSION['active_form'] = 'register';
    } else {
        // 2. Insert new user
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $password, $role);
        
        if ($stmt->execute()) {
            $_SESSION['register_success'] = "Registration successful! Please login.";
            $_SESSION['active_form'] = 'login';
        } else {
            $_SESSION['register_error'] = "Database error. Please try again.";
            $_SESSION['active_form'] = 'register';
        }
        $stmt->close();
    }
    
    header("Location: index.php");
    exit();
}

// --- LOGIN LOGIC ---
>>>>>>> 110cb44 (Fixing the the connection to database and Successfull)
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

<<<<<<< HEAD
    $result = $conn->query("SELECT * FROM users WHERE email = '$email'");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];

            if ($user['role'] === 'rider') {
=======
    // Securely check for user
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verify password hash
        if (password_verify($password, $user['password'])) {
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_page.php");
            } elseif ($user['role'] === 'rider') {
>>>>>>> 110cb44 (Fixing the the connection to database and Successfull)
                header("Location: rider_page.php");
            } else {
                header("Location: user_page.php");
            }
            exit();
        }
    }

<<<<<<< HEAD
=======
    // If login fails
>>>>>>> 110cb44 (Fixing the the connection to database and Successfull)
    $_SESSION['login_error'] = 'Incorrect email or password';
    $_SESSION['active_form'] = 'login';
    header("Location: index.php");
    exit();
}
<<<<<<< HEAD

=======
>>>>>>> 110cb44 (Fixing the the connection to database and Successfull)
?>