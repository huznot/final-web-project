<!--
The following is the php code which handles likes on the backend. From the AJAX request from the javascript
it determines if there was a like or unlike and respectively adds or removes a like number from the column
--->
<?php
// Database connection
$db_host = "sql300.infinityfree.com";
$db_user = "if0_37426626";
$db_pass = "oH1R1Fth3ZW0O";
$db_name = "if0_37426626_fileuploaddownload";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the blog ID and action (like or unlike) from AJAX request
if (isset($_POST['id']) && isset($_POST['action'])) {
    $id = $_POST['id'];
    $action = $_POST['action'];

    // Determine whether to increase or decrease the like count
    if ($action == "like") {
        $update_sql = "UPDATE blogs SET likes = likes + 1 WHERE id = ?";
    } else {
        $update_sql = "UPDATE blogs SET likes = likes - 1 WHERE id = ?";
    }

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Return the updated number of likes
        $result = $conn->query("SELECT likes FROM blogs WHERE id = $id");
        $row = $result->fetch_assoc();
        echo $row['likes'];
    } else {
        echo "error";
    }

    $stmt->close();
}

$conn->close();
?>
