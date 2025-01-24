<!--
This code saves the blog and everything related to it into my SQL database. It first retrieves form data
from create.php and then inserts that into the database.
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

// Retrieve form data
$title = $_POST['title'];
$content = $_POST['content'];
$thumbnail = isset($_POST['thumbnail']) ? $_POST['thumbnail'] : null; // Get thumbnail path

// Insert the blog title, content, and thumbnail into the database
$sql = "INSERT INTO blogs (title, content, thumbnail) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $title, $content, $thumbnail); // "sss" for strings: title, content, thumbnail

if ($stmt->execute()) {
    header("Location: home.php");
    exit(); // Ensure the script stops after the redirect
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
