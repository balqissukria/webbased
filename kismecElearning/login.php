<?php
session_start();

// Check if the signup success session variable is set
if (isset($_SESSION['signup_success']) && $_SESSION['signup_success'] === true) {
    echo '<script>alert("Signup successful! You can now log in.");</script>';
    // Reset the session variable to prevent showing the alert on subsequent page loads
    unset($_SESSION['signup_success']);
}
?>

<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
   
    <!-- Adding Bootstrap -->    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
 
    <title>Login</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>
    <div class="col-4 center">
        <form action="process_login.php" method="post">
            <div class="form-group">
                <label for="email">Username:</label>
                <input type="text" class="form-control shadow-sm" id="txtEmail" name="email" required>
            </div>
            <div class="form-group">
                <label for="pwd">Password:</label>
                <input type="password" class="form-control shadow-sm" id="txtPassword" name="password" required>
            </div>

            <button type="submit" class="btn btn-primary shadow" name="btnLogin">Login</button>
        </form>
    </div>
</body>
</html>
