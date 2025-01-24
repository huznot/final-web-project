<!--
This code uploads thumbnails assosciated to each note.
It fetches the thumbnail uploaded from create.php and inserts that into the uploads/thumbnails
directory
--->
<?php
// Database connection details
$db_host = "sql300.infinityfree.com";
$db_user = "if0_37426626";
$db_pass = "oH1R1Fth3ZW0O";
$db_name = "if0_37426626_fileuploaddownload";

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
