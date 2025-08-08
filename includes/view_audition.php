<?php
// ---------------------------
// DATABASE CONNECTION
// ---------------------------

// Database credentials
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'uy_records';

// Create a new MySQL connection
$conn = new mysqli($host, $user, $password, $database);

// Check if connection to the database failed
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ---------------------------
// VALIDATE REQUEST PARAMETER
// ---------------------------

// Check if 'id' parameter is provided via GET
if (!isset($_GET['id'])) {
    die("No audition ID provided."); // Stop execution if ID is missing
}

// Cast ID to integer to prevent SQL injection
$id = (int) $_GET['id'];

// ---------------------------
// FETCH AUDITION DATA
// ---------------------------

// Prepare SQL query to fetch audition details based on ID
$sql = "SELECT * FROM auditions WHERE id = ?";
$stmt = $conn->prepare($sql);

// Bind the ID parameter to the query
$stmt->bind_param("i", $id);

// Execute the prepared statement
$stmt->execute();

// Retrieve the result of the query
$result = $stmt->get_result();

// Check if any record was returned
if ($result->num_rows === 0) {
    die("No audition found."); // Display error if no matching record is found
}

// Fetch the result row as an associative array
$data = $result->fetch_assoc();

// Close the statement and database connection
$stmt->close();
$conn->close();
?>
