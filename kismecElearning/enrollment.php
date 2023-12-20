<?php
session_start();

// Function to establish a database connection
function connectDB() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "webbased";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Function to sanitize user input
function sanitizeInput($input) {
    return htmlspecialchars(stripslashes(trim($input)));
}

// Function to check if the user has access to enroll within the allowed timeframe
function checkEnrollmentTime($lastLoginTime) {
    // Set the allowed enrollment duration (7 days)
    $allowedEnrollmentDuration = 7 * 24 * 60 * 60; // 7 days in seconds

    // Calculate the time difference between the current time and the last login time
    $currentTime = time();
    $timeDifference = $currentTime - $lastLoginTime;

    return $timeDifference <= $allowedEnrollmentDuration;
}

// Function to update the enrollment status in the database
function updateEnrollmentStatus($userId) {
    $conn = connectDB();

    // Update the user's enrollment status
    $updateEnrollmentStatusQuery = "UPDATE users SET enrollment_status = 1 WHERE user_id = ?";
    $updateEnrollmentStatusStmt = $conn->prepare($updateEnrollmentStatusQuery);
    $updateEnrollmentStatusStmt->bind_param("i", $userId);

    if ($updateEnrollmentStatusStmt->execute()) {
        // Update successful
        $updateEnrollmentStatusStmt->close();
        $conn->close();
        return true;
    } else {
        // Update failed
        $updateEnrollmentStatusStmt->close();
        $conn->close();
        return false;
    }
}

// Enrollment logic
if (isset($_POST['btnEnroll'])) {
    $userId = $_SESSION['user_id'];

    // Check if the user has already enrolled
    if (checkEnrollmentStatus($userId)) {
        // User has already enrolled, set the session flags and expiration time
        $_SESSION['enrollment_status'] = 1;
        $_SESSION['enrollment_expiration'] = time() + (7 * 24 * 60 * 60); // 7 days in seconds

        // Redirect to the next page
        header("Location: elearningkismec.php");
        exit();
    }

    // Place your additional enrollment logic here, if needed

    // Assuming the enrollment is successful, update the enrollment status
    if (updateEnrollmentStatus($userId)) {
        // Update the session flags and expiration time
        $_SESSION['enrollment_status'] = 1;
        $_SESSION['enrollment_expiration'] = time() + (7 * 24 * 60 * 60); // 7 days in seconds

        // Redirect to the next page
        header("Location: elearningkismec.php");
        exit();
    } else {
        echo "Enrollment failed. Please try again.";
    }
}

// Function to check the enrollment status in the database
function checkEnrollmentStatus($userId) {
    $conn = connectDB();

    // Implement your database query here to check if the user is enrolled
    $checkEnrollmentQuery = "SELECT enrollment_status FROM users WHERE user_id = ?";
    $checkEnrollmentStmt = $conn->prepare($checkEnrollmentQuery);
    $checkEnrollmentStmt->bind_param("i", $userId);
    $checkEnrollmentStmt->execute();
    $result = $checkEnrollmentStmt->get_result();

    // Return true if enrolled, false otherwise
    if ($result->num_rows > 0) {
        $enrollmentStatus = $result->fetch_assoc()['enrollment_status'];
        $checkEnrollmentStmt->close();
        $conn->close();
        return $enrollmentStatus == 1;
    } else {
        $checkEnrollmentStmt->close();
        $conn->close();
        return false;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h2 {
            color: #333;
        }

        form {
            margin-top: 20px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        p {
            margin-top: 20px;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Course Enrollment</h2>
    <p>Welcome, <?php echo $_SESSION['user_email']; ?>!</p>
    <?php
    // If the user has already enrolled, display a message
    if ($_SESSION['enrollment_status'] === 1) {
        echo '<p>You are already enrolled. Redirecting to the next page...</p>';
        echo '<meta http-equiv="refresh" content="2;url=elearningkismec.php">'; // Redirect after 2 seconds
    } else {
        // If not enrolled, display the enrollment form
    ?>

<form method="post" action="">
        <?php
        // Add any additional form fields or customization here
        ?>
        <input type="submit" name="btnEnroll" value="Enroll">
    </form>
<?php
    }
    ?>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>