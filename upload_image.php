<!--
This code is to upload images uploaded by the user to the uploads directory which is in my workspace.
--->
<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $file_tmp = $_FILES['image']['tmp_name'];
    $file_name = basename($_FILES['image']['name']);
    $upload_dir = 'uploads/';
    $file_path = $upload_dir . $file_name;

    if (move_uploaded_file($file_tmp, $file_path)) {
        // Return the image wrapped in an HTML tag with a class
        $image_html = '<img src="' . $file_path . '" class="notes-image" alt="Uploaded Image">';
        echo json_encode(['success' => true, 'html' => $image_html, 'url' => $file_path]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to move uploaded file.']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request.']);
}
?>
