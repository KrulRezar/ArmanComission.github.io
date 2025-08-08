<?php
// ---------------------------
// DATABASE CONNECTION
// ---------------------------

// Database credentials
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'uy_records';

// Create MySQL database connection
$conn = new mysqli($host, $user, $password, $database);

// Check if connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ---------------------------
// HELPER FUNCTION: File Upload
// ---------------------------

function uploadFile($fileKey, $uploadDir = 'uploads/') {
    // Check if the file is uploaded with no error
    if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {

        // Create upload directory if it does not exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Get temporary file path and original file name
        $fileTmpPath = $_FILES[$fileKey]['tmp_name'];
        $fileName = basename($_FILES[$fileKey]['name']);

        // Extract file extension and check if allowed
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedExts = ['jpg', 'jpeg', 'png', 'gif', 'mp3', 'wav']; // Allowed file types

        // Validate file extension
        if (!in_array($fileExt, $allowedExts)) {
            die("Invalid file type for $fileKey. Allowed types: " . implode(", ", $allowedExts));
        }

        // Generate a unique file name to avoid overwriting
        $newFileName = uniqid($fileKey . '_', true) . '.' . $fileExt;
        $destPath = $uploadDir . $newFileName;

        // Move uploaded file to destination folder
        if (move_uploaded_file($fileTmpPath, $destPath)) {
            return $destPath; // Return final file path
        } else {
            die("Failed to move uploaded file for $fileKey.");
        }
    } else {
        die("File $fileKey is required.");
    }
}

// ---------------------------
// FORM SUBMISSION HANDLING
// ---------------------------

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and fetch form inputs
    $category = isset($_POST['category']) ? $conn->real_escape_string($_POST['category']) : '';
    $firstname = isset($_POST['firstname']) ? $conn->real_escape_string(trim($_POST['firstname'])) : '';
    $middleInitial = isset($_POST['middleInitial']) ? substr(trim($_POST['middleInitial']), 0, 1) : '';
    $lastname = isset($_POST['lastname']) ? $conn->real_escape_string(trim($_POST['lastname'])) : '';
    $email = isset($_POST['email']) ? $conn->real_escape_string(trim($_POST['email'])) : '';
    $nationality = isset($_POST['nationality']) ? $conn->real_escape_string(trim($_POST['nationality'])) : '';
    $dob = isset($_POST['dob']) ? $conn->real_escape_string($_POST['dob']) : '';
    $gender = isset($_POST['gender']) ? $conn->real_escape_string(trim($_POST['gender'])) : '';
    $height = isset($_POST['height']) ? (int) $_POST['height'] : 0;
    $weight = isset($_POST['weight']) ? (int) $_POST['weight'] : 0;
    $country_code = isset($_POST['country_code']) ? $conn->real_escape_string(trim($_POST['country_code'])) : '';
    $mobile = isset($_POST['mobile']) ? $conn->real_escape_string(trim($_POST['mobile'])) : '';
    $social_link = isset($_POST['social_link']) ? $conn->real_escape_string(trim($_POST['social_link'])) : '';

    // Upload required media files using helper function
    $profile1Path = uploadFile('profile1');
    $profile2Path = uploadFile('profile2');
    $profile3Path = uploadFile('profile3');
    $vocalSamplePath = uploadFile('vocal_sample');
    $performanceSamplePath = uploadFile('performance_sample');

    // ---------------------------
    // SQL INSERT PREPARED STATEMENT
    // ---------------------------

    // SQL statement to insert audition form data
    $sql = "INSERT INTO auditions (
                category, firstname, middleInitial, lastname, email, nationality,
                dob, gender, height, weight, country_code, mobile, social_link,
                profile1, profile2, profile3, vocal_sample, performance_sample
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Prepare SQL statement
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind form data to SQL statement
    $stmt->bind_param(
        "ssssssssddssssssss",
        $category,
        $firstname,
        $middleInitial,
        $lastname,
        $email,
        $nationality,
        $dob,
        $gender,
        $height,
        $weight,
        $country_code,
        $mobile,
        $social_link,
        $profile1Path,
        $profile2Path,
        $profile3Path,
        $vocalSamplePath,
        $performanceSamplePath
    );

    // Execute the SQL statement
    if ($stmt->execute()) {
        // Success message and redirect
        echo "Audition submitted successfully! Redirecting back in 1 seconds...";
        echo '<meta http-equiv="refresh" content="1;url=' . htmlspecialchars($_SERVER["HTTP_REFERER"]) . '">';
    } else {
        // Show SQL error if insert fails
        echo "Error: " . $stmt->error;
    }

    // Close statement and connection
    $stmt->close();
    $conn->close();
} else {
    // If no POST data received, show error message
    echo "No form data submitted.";
}
?>
