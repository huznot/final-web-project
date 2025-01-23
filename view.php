<?php
// Database connection details
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "fileuploaddownload";
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check if the database connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure 'id' parameter exists and is numeric to prevent SQL injection
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $blog_id = (int)$_GET['id']; // Cast to integer for added security
} else {
    die("Invalid blog ID.");
}

// Fetch the blog post from the database
$blog_result = $conn->query("SELECT * FROM blogs WHERE id = $blog_id");

if ($blog_result && $blog_result->num_rows > 0) {
    $blog = $blog_result->fetch_assoc();
} else {
    die("Blog not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/b5f4641468.js" crossorigin="anonymous"></script>
    <title><?php echo htmlspecialchars($blog['title']); ?></title>
    <link rel="stylesheet" href="style.css?v=3">
    <style>
        /* Styling for the blog container */
        #blog-container {
            background-color: rgb(255, 255, 255);
            padding: 20px;
            border-radius: 8px;
            border: 1px solid black;
            max-width: 50vw;
            width: 100%;
            float: left;
            margin-bottom: 30px;
        }
        
        /* Ensuring images are responsive */
        #content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 20px 0;
        }
        
        #content {
            font-size: 2rem;
        }
        
        /* Styling for comments container */
        #comments-container {
            border: 1px solid black;
            padding: 20px;
            border-radius: 8px;
            max-width: 30vw;
            width: 100%;
            min-height: 150px;
            float: right;
            background-color: rgb(255, 255, 255);
            font-size: 1.6rem;
            margin-bottom: 50px;
        }
        
        #comments p {
            margin: 10px 0;
        }
        
        /* Input and textarea styling */
        #comments-input, textarea {
            width: 100%;
            padding: 8px 3px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        h1 {
            margin-top: 100px;
        }
        
        /* Dark mode styles */
        body.dark-mode #blog-container, body.dark-mode #comments-container {
            background-color: black;
            border: 1px solid white;
        }
        body.dark-mode #comments-input, body.dark-mode textarea {
            background-color: black;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div id="nav-left">
                <a href="index.html"><figure><img id="logo" alt="Notify logo" src="images/Notify-logo.png"></figure></a>
                <div id="nav-list">
                    <a href="home.php" class="nav-item" id="one">
                        <i class="fas fa-home" alt="home"></i>
                        <p>Home</p>
                    </a>
                    <a href="liked_notes.php" class="nav-item">
                        <i class="fa-heart fa-solid" alt="heart"></i>
                        <p>Liked</p>
                    </a>
                    <a href="create.php" class="nav-item" id="two">
                        <i class="fa-solid fa-circle-plus" alt="plus"></i>
                        <p>Create</p>
                    </a>
                </div>
            </div>
            
            <div id="split">
                <!-- Toggle for dark mode -->
                <a href="#" id="darkModeToggle"><img class="icons" id="brightnessIcon" src="images/brightness.png" alt="Toggle Dark Mode"></a>
            </div>
        </nav>
    </header>
    <main>
        <div id="top">
            <h1><a href="home.php"><i class="fa-solid fa-arrow-left"></i></a> <?php echo ($blog['title']); ?></h1>
        </div>
        
        <section id="page">
            <div id="blog-container">
                <?php $blog_content = str_replace('<img', '<img alt="Image not available"', $blog['content']);?>
                <div id="content"> <?php echo ($blog_content); ?> </div>
            </div>
        </section>
        <div id="comments-container">
            <h2>Comments</h2>
            <?php
            /*
            This PHP code attains author name and their comment from the comments column relative to each note in
            my SQL database.
            */
            $comment_result = $conn->query("SELECT * FROM comments WHERE blog_id = $blog_id ORDER BY created_at DESC");
            if ($comment_result && $comment_result->num_rows > 0) {
                while ($comment = $comment_result->fetch_assoc()) {
                    echo "<p><strong>" . htmlspecialchars($comment['author']) . ":</strong> " . htmlspecialchars($comment['comment']) . "</p>";
                    echo "<small>Posted on: " . $comment['created_at'] . "</small><hr>";
                }
            } else {
                echo "<p>No comments yet.</p>";
            }
            ?>
            <!--Form for posting comments-->
            <form action="" method="POST">
                <fieldset>
                    <label for="author">Name:</label><br>
                    <input id="comments-input" type="text" id="author" name="author" required><br><br>

                    <label for="comment">Comment:</label><br>
                    <textarea id="comment" name="comment" rows="4" required></textarea><br><br>
                    <button type="submit" class="button" name="submit_comment">Submit Comment</button>
                </fieldset>
            </form>
        </div>
    </main>



    <?php
    /*
    This PHP code manages comments. It takes the author field and the comment field and inserts 
    those into columns relative to the note they were posted to inside my SQL database
    */
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_comment'])) {
        $author = $conn->real_escape_string($_POST['author']);
        $comment = $conn->real_escape_string($_POST['comment']);

        if (!empty($author) && !empty($comment)) {
            $sql = "INSERT INTO comments (blog_id, author, comment) VALUES ($blog_id, '$author', '$comment')";
            if ($conn->query($sql)) {
                header("Refresh:0"); // Refresh the page to show the new comment
            } else {
                echo "<p>Error: " . $conn->error . "</p>";
            }
        }
    }
    ?>

    
    <script>
        /*
        The following javascript is for the dark mode functionality, it works by declaring the elements involved as variables
        then toggling the dark-mode ID to the body if the button is clicked. It also changes the toggle image and noteify logo.
        */
        document.addEventListener("DOMContentLoaded", () => {
        const darkModeToggle = document.querySelector(".icons");
        const body = document.body;
        const logo = document.querySelector("nav img"); // Select the Noteify logo

        // Retrieve the dark mode state from localStorage
        const savedDarkMode = localStorage.getItem("darkMode") === "true";

        // Apply the saved dark mode state
        if (savedDarkMode) {
            body.classList.add("dark-mode");
            darkModeToggle.setAttribute("src", "images/night-mode.png");
            logo.setAttribute("src", "images/Notify-logo-dark.png");
        }

        darkModeToggle.addEventListener("click", () => {
            // Toggle dark mode styles
            const isDarkMode = body.classList.toggle("dark-mode");

            // Update the brightness image
            const brightnessImage = isDarkMode ? "images/night-mode.png" : "images/brightness.png";
            darkModeToggle.setAttribute("src", brightnessImage);

            // Update the Noteify logo
            const logoImage = isDarkMode ? "images/Notify-logo-dark.png" : "images/Notify-logo.png";
            logo.setAttribute("src", logoImage);

            // Save the dark mode state in localStorage
            localStorage.setItem("darkMode", isDarkMode);
        });
    });
    </script>
</body>
</html>

<?php
$conn->close();
?>
