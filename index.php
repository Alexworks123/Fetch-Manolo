<?php
session_start();


$errors = [
    'login' => $_SESSION['login_error'] ?? '',
    'register' => $_SESSION['register_error'] ?? '',
    'success' => $_SESSION['register_success'] ?? ''
];

$activeForm = $_SESSION['active_form'] ?? 'login';


unset($_SESSION['login_error']);
unset($_SESSION['register_error']);
unset($_SESSION['register_success']);
unset($_SESSION['active_form']);

function showError($error) {
    return !empty($error) ? "<p class='error-message' style='color:red;'>$error</p>" : '';
}

function showSuccess($msg) {
    return !empty($msg) ? "<p class='success-message' style='color:green;'>$msg</p>" : '';
}

function isActiveForm($formName, $activeForm) {
    return $formName === $activeForm ? 'active' : '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Register</title>
    <link rel="stylesheet" href="style.css">
</head>
<script>

function showFields(){

    let role = document.getElementById("role").value;

    let riderFields = document.getElementById("riderFields");

    if(role === "rider"){
        riderFields.style.display = "block";
    }else{
        riderFields.style.display = "none";
    }

}

</script>
<body>
    <div class="container">
        <div class="form-box <?= isActiveForm('login', $activeForm) ?>" id="login-form">
            <form action="FETCH_MANOLO.php" method="post">
                <h2>Login</h2>
                <?php 
                    echo showError($errors['login']); 
                    echo showSuccess($errors['success']); 
                ?>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit" name="login">Login</button>
                <p>Don't have an account? <a href="#" onclick="showForm('register-form')">Register</a></p>
            </form>
        </div>

        <div class="form-box <?= isActiveForm('register', $activeForm) ?>" id="register-form">
            <form action="FETCH_MANOLO.php" method="post">
                <h2>Register</h2>  
                <?php echo showError($errors['register']); ?>
                <input type="text" name="name" placeholder="Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                
                <input type="text" name="phone" placeholder="Phone Number" required>
                <input type="text" name="address" placeholder="Barangay Address" required>
          <select id="role" name="role" onchange="showFields()">
              <option value="user">Customer</option>
              <option value="rider">Rider</option>
          </select>
                   <div id="riderFields" style="display:none;">

    <input type="text"
           name="license"
           placeholder="Driver License Number">

    <input type="text"
           name="vehicle"
           placeholder="Vehicle Type">

    <input type="text"
           name="plate"
           placeholder="Plate Number">

  

</div>
                <button type="submit" name="Register">Register</button>
                <p>Already have an account? <a href="#" onclick="showForm('login-form')">Login</a></p>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>