<?php
session_start();

// Include the file with the database connection function
include 'db_connection.php';

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

// Example usage
$conn = connectDB();

// Now $conn is a valid database connection object that you can use for executing queries.
// ...

// Don't forget to close the connection when you're done.
$conn->close();
?>
