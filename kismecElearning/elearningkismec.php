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
    <title>Kismec - Robotic and Automation</title>
    <style>
        body {
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            align-items: center;
        }

        header {
            text-align: center;
            padding: 20px;
            background-color: #4285f4;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        nav ul {
            list-style: none;
            padding: 0;
            text-align: center;
            background-color: #333;
            /* Background color for the navigation bar */
        }

        nav li {
            display: inline;
            margin: 10px;
        }

        nav a {
            text-decoration: none;
            color: white;
            font-size: 18px;
            transition: color 0.3s ease;
            padding: 8px 16px;
            /* Add padding to the anchor links */
            border-radius: 4px;
            /* Add border radius for a button-like appearance */
            background-color: #4285f4;
            /* Background color for the anchor links */
        }

        nav a:hover {
            color: #e0e0e0;
            background-color: #2a4d8e;
            /* Background color on hover */
        }

        section {
            text-align: center;
            padding: 20px;
        }

        footer {
            text-align: center;
            padding: 10px;
            background-color: #4285f4;
            color: white;
            margin-top: auto;
            box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1);
        }

        .subject-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        #plcVideos,
        #pneumaticsVideos,
        #sensorVideos,
        #robotVideos {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            margin-top: 20px;
        }

        #plcVideos iframe,
        #pneumaticsVideos iframe,
        #sensorVideos iframe,
        #robotVideos iframe {
            width: 400px;
            height: 225px;
            margin: 10px;
        }
    </style>


</head>

<body>
<header>
<h2>eLearning Dashboard</h2>
    <p>Welcome, <?php echo $_SESSION['user_email']; ?>!</p>
    <p>Your enrollment will expire in <?php echo gmdate("H:i:s", $remainingTime); ?>.</p>
    <!-- Add the rest of your content here -->
    <p><a href="logout.php">Logout</a></p>
    </header>
   
   
    <section id="plcSection">
        <!-- First Subject: Programmable Logic Controllers (PLC) -->
        <h2 class="subject-title">Programmable Logic Controllers (PLC)</h2>

        <!-- Additional PLC Videos -->
        <div id="plcVideos">
            <div class="video-container">
                <iframe width="560" height="315"
                    src="https://www.youtube.com/embed/playlist?list=PLYod-QpuZCuBrPyL2AQIARwFtPZewCKFh" frameborder="0"
                    allowfullscreen></iframe>
                <p class="video-name">CODESYS</p>
            </div>
            <div class="video-container">
                <iframe width="560" height="315"
                    src="https://www.youtube.com/embed/videoseries?list=PL9WmWBFcp6S_0-LSXsV4cIOXhvkWAbEGh"
                    frameborder="0" allowfullscreen></iframe>
                <p class="video-name">Allen-Bradley PLC - Micro800 Simulator</p>
            </div>
            <div class="video-container">
                <iframe width="560" height="315"
                    src="https://www.youtube.com/embed/videoseries?list=PLxp8oh1tmMqaYSI_8uhuklUa5notsNCpT"
                    frameborder="0" allowfullscreen></iframe>
                <p class="video-name">Allen-Bradley PLC - Studio 5000 Logix Designer</p>
            </div>
        </div>


    </section>

    <section  id="pneumaticsSection">
        <!-- Second Subject: Pneumatics -->
        <h2 class="subject-title">Pneumatics</h2>

        <!-- Additional Pneumatics Videos -->
        <div id="pneumaticsVideos">
            <div class="video-container">
                <iframe width="560" height="315"
                    src="https://www.youtube.com/embed/playlist?list=PLYod-QpuZCuCuButKUsSthkJXMBsxd7pW" frameborder="0"
                    allowfullscreen></iframe>
                <p class="video-name">Pneumatics Playlist</p>
            </div>
            <!-- Add more videos as needed -->
        </div>

       
    </section>

    <section id="sensorSection">
        <!-- Third Subject: Sensor -->
        <h2 class="subject-title">Sensor Technology</h2>

        <!-- Additional Sensor Videos -->
        <div id="sensorVideos">
            <div class="video-container">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/h4B9zKqfG1c" frameborder="0"
                    allowfullscreen></iframe>
                <p class="video-name">Proximity Inductive Sensor</p>
            </div>

            <div class="video-container">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/PN9DTgrPIFI" frameborder="0"
                    allowfullscreen></iframe>
                <p class="video-name">Proximity Capacitive Sensor</p>
            </div>

        </div>


    </section>
    <section id="robotSection" >
        <!-- forth Subject: Robot -->
        <h2 class="subject-title">Robot</h2>

        <!-- Additional Robot Videos -->
        <div id="robotVideos">
            <div class="video-container">
                <iframe width="560" height="315" src="https://www.youtube.com/embed/videoseries?list=PLYod-QpuZCuB0Avg3XYJ_DGjsItro2xPJ" frameborder="0" allowfullscreen></iframe>
                <p class="video-name">APAS Pick and Place</p>
            </div>
            <!-- Add more videos as needed -->
        </div>


    </section>

    <footer>
        <p>Robotic and Automation Department</p>
    </footer>
</body>

</html>