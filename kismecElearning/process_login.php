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

// Login logic
if (isset($_POST['btnLogin'])) {
    $conn = connectDB();

    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT user_id, email, password, login_time, enrollment_status FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        if (password_verify($password, $hashedPassword)) {
            // Update login_time
            $updateLoginTimeQuery = "UPDATE users SET login_time = ? WHERE user_id = ?";
            $updateLoginTimeStmt = $conn->prepare($updateLoginTimeQuery);
            $currentTime = time();
            $updateLoginTimeStmt->bind_param("ii", $currentTime, $row['user_id']);
            $updateLoginTimeStmt->execute();

            // Check if the user has already enrolled
            if ($row['enrollment_status'] == 1) {
                // User has already enrolled, skip the enrollment process
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user_email'] = $row['email'];

                // Redirect to the appropriate page (e.g., dashboard)
                header("Location: elearningkismec.php"); // Adjust the URL accordingly
                exit();
            }

            // Check if the user has access to enroll within the allowed timeframe
            if (checkEnrollmentTime($currentTime)) {
                // Allow the user to enroll (add your enrollment logic here)
                $_SESSION['user_id'] = $row['user_id'];
                $_SESSION['user_email'] = $row['email'];

                // Redirect to the enrollment page or perform the enrollment logic
                header("Location: enrollment.php"); // Adjust the URL accordingly
                exit();
            } else {
                echo "Enrollment is no longer available. Please enroll within 7 days of login.";
            }
        } else {
            echo "Invalid email or password";
        }
    } else {
        echo "Invalid email or password";
    }

    $stmt->close();
    $updateLoginTimeStmt->close(); // Close the statement for updating login_time
    $conn->close();
}
?>