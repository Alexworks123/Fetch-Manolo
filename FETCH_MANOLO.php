
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config.php';


if (isset($_POST['Register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $phone = $_POST['phone'] ?? ''; 
    $address = $_POST['address'] ?? '';

   
    $checkEmail = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmail->bind_param("s", $email);
    $checkEmail->execute();
    $result = $checkEmail->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['register_error'] = 'Email is already registered!';
        $_SESSION['active_form'] = 'register';
    } else {
     
        $stmt = $conn->prepare("INSERT INTO users (name, email, password, role, phone, address) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $password, $role, $phone, $address);

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


if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
      
        if (password_verify($password, $user['password'])) {
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['phone'] = $user['phone'] ?? '09677315738';
            $_SESSION['address'] = $user['address'] ?? '';

       
            if ($user['role'] === 'rider') {
                header("Location: rider_page.php");
            } elseif ($user['role'] === 'user') {
                header("Location: user_page.php");
            } else {
                header("Location: index.php");
            }
            exit();
        }
    }

  
    $_SESSION['login_error'] = 'Incorrect email or password';
    $_SESSION['active_form'] = 'login';
    header("Location: index.php");
    exit();
}
?>