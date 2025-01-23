<?php
// Database connection details
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "fileuploaddownload";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a thumbnail was uploaded
if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = "uploads/thumbnails/";

    // Ensure the upload directory exists
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Get the original filename
    $originalFileName = basename($_FILES['thumbnail']['name']);
    $fileTmpPath = $_FILES['thumbnail']['tmp_name'];
    
    // Use the original filename for saving
    $fileDest = $uploadDir . $originalFileName;

    // Move the uploaded file to the destination directory
    if (move_uploaded_file($fileTmpPath, $fileDest)) {
        // Return the file path and original filename for saving in the main form
        echo json_encode(['success' => true, 'url' => $fileDest, 'name' => $originalFileName]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to upload thumbnail.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No file uploaded or there was an error.']);
}

$conn->close();
?>