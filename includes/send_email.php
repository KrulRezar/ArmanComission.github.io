<?php
// Database connection settings
$host = 'localhost';   // Database server address
$db   = 'uy_records';  // Database name
$user = 'root';        // Database username
$pass = '';            // Database password

// Create connection to MySQL database
$conn = new mysqli($host, $user, $pass, $db);

// Check if the connection was successful
if ($conn->connect_error) {
    // If connection failed, stop script and show error message
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST and the 'message' field exists
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['message'])) {
    // Sanitize user inputs to prevent SQL injection and remove extra spaces
    $name_company = $conn->real_escape_string(trim($_POST['name-company'] ?? ''));
    $email = $conn->real_escape_string(trim($_POST['e-mail'] ?? ''));
    $message = $conn->real_escape_string(trim($_POST['message'] ?? ''));

    // Simple validation: check if message is empty
    if (empty($message)) {
        echo "Message is required.";  // Inform the user that message is mandatory
        exit;  // Stop the script execution
    }

    // Prepare SQL query to insert data into the Messages table
    $sql = "INSERT INTO Messages (name_company, email, message) 
            VALUES ('$name_company', '$email', '$message')";

    // Execute the query and check if it was successful
    if ($conn->query($sql) === TRUE) {
        // If insert successful, show confirmation and redirect user back after 500ms
        echo "<p>Message sent successfully! Redirecting you back...</p>";
        echo "<script>
            setTimeout(function() {
                window.history.back();
            }, 500);
        </script>";
    } else {
        // If error occurred during insert, show the error message
        echo "Error: " . $conn->error;
    }
} else {
    // If request method is not POST or message is missing, show invalid request
    echo "Invalid request.";
}

// Close the database connection to free resources
$conn->close();
?>
