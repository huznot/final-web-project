<!--
The following is just connection code to be able to connect my PHP scripts with my SQL server.

It fetches notes or reffered to as "blogs" in my code but only those that have likes over zero.
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

// Fetch only liked blogs (where likes > 0)
$liked_blog_sql = "SELECT * FROM blogs WHERE likes > 0";
$liked_blog_result = $conn->query($liked_blog_sql);

if (!$liked_blog_result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liked Notes</title>
    <link rel="stylesheet" href="style.css?v=18">
    <script src="https://kit.fontawesome.com/b5f4641468.js" crossorigin="anonymous"></script>  
    <style>
        /* Styles for colored text */
        .colored {
            color: red;
        }
        /* Ensures consistent image size for cards */
        .card-image{
            width: 300px;
            height: 230px;
        }
        /* Grid layout for displaying note cards */
        .card-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 50px;
        }
        /* Individual card styling */
        .card {
            position: relative; 
            text-align: center;
            width: 100%; 
            max-width: 300px;
            height: 350px;
            background-color: rgb(236, 235, 235);
            border: 2px solid black;
            font-size: 2rem;
        }
        /* Inner box inside card for additional content */
        .inner-box{
            height: 120px;
            background-color: white;
            position: absolute;
            bottom: 0;
            max-width: 300px;
            width: 100%;
            border-top: 2px solid black;
        }
        .inner-box h4{
            margin-top: 10px;
            font-size: 2.5rem;
        }
        /* Container for elements inside the inner box */
        .box-element-container{
            display: flex;
            align-items: center;
            position: absolute;
            bottom: 20px;
            width: 300px;
        }
        .box-element-container a{
            display: flex;
            align-items: center;
            margin-left: 10px;
        }
        /* Styling for like button section */
        .likes{
            position: absolute;
            align-items: center;
            display: flex;
            right: 10px;
            gap: 2px;
        }
        .filename{
            margin-top: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div id="nav-left">
                <!-- Logo linking to homepage -->
                <a href="index.html"><figure><img id="logo" src="images/Notify-logo.png"></figure></a>               
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
                <!-- Search bar for notes -->
                <div id="search">
                    <div id="search-icon">
                        <i class="fas fa-search"></i>
                    </div>                 
                    <input id="search-input" type="text" placeholder="Search">
                </div>
                <a href="#" id="darkModeToggle"><img class="icons" id="brightnessIcon" src="images/brightness.png" alt="Toggle Dark Mode"></a>
            </div>
        </nav>
    </header>
    
    <main>
        <div id="overlay"></div>
        <h1>Liked Notes</h1>
        <section class="card-grid">
            <!-- Display liked notes -->
            <?php
            if ($liked_blog_result->num_rows > 0) {
                while ($row = $liked_blog_result->fetch_assoc()) {
                    ?>
                    <div class="card" data-name="<?php echo strtolower($row['title']); ?>" data-id="<?php echo $row['id']; ?>">
                        <img src="<?php echo htmlspecialchars($row['thumbnail']); ?>" alt="<?php echo htmlspecialchars(basename($row['thumbnail'])) . " preview not available"; ?>" class="card-image">
                        <div class="inner-box">
                            <div class="filename"><?php echo htmlspecialchars($row['title']); ?></div>
                            <div class="box-element-container">
                                <a href="view.php?id=<?php echo $row['id']; ?>" class="button">View Notes</a>
                                <div class="likes">      
                                    <a href="#" class="like-btn" data-id="<?php echo $row['id']; ?>">
                                        <i class="fa-solid fa-heart colored"></i>
                                    </a>
                                    <p class="like-count"><?php echo $row['likes']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
        </section>
    </main>

    <script>
        /*
        The following javascript is for the dark mode functionality, it works by declaring the elements involved as variables
        then toggling the dark-mode ID to the body if the button is clicked. It also changes the toggle image and noteify logo.

        Secondly, we have a long likes functionality piece. This part basically handles the logic behind liking and unliking a blog post.
        It checks if the user has already liked the post using localStorage and updates the UI accordingly. When the user clicks the "like"
        button, it sends a request to the server to either
        add or remove a like for the post, updates the like count, and changes the heart icon to reflect the new status.
        The action is saved locally so the UI remembers the user's preference even if they refresh the page.     
        
        Lastly we have the search bar functionality. This simply gets all cards and the attribute that holds their names and checks the string
        in the search bar and displays only those cards with that in their names.
        */
        document.addEventListener("DOMContentLoaded", function() {
            const likeButtons = document.querySelectorAll(".like-btn");

            likeButtons.forEach(button => {
                const blogId = button.getAttribute("data-id");
                const likeCountElement = button.nextElementSibling; // <p> element with the like count
                const cardElement = button.closest(".card"); // Entire card container

                // Check if this blog post has already been liked (using localStorage)
                if (localStorage.getItem(`liked_${blogId}`)) {
                    // If already liked, set the heart to solid and red
                    button.querySelector("i").classList.add("fa-heart", "colored");
                    button.querySelector("i").classList.remove("fa-regular");
                }

                button.addEventListener("click", function(event) {
                    event.preventDefault();

                    const isLiked = localStorage.getItem(`liked_${blogId}`);
                    let action = isLiked ? "unlike" : "like";

                    // Send request to update like/unlike on the server
                    fetch("like.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded",
                        },
                        body: `id=${blogId}&action=${action}`,
                    })
                    .then(response => response.text())
                    .then(data => {
                        if (!isNaN(data)) {
                            const newLikeCount = data; // Updated like count from server
                            likeCountElement.textContent = newLikeCount;

                            if (action === "like") {
                                // Change heart to solid
                                button.querySelector("i").classList.add("fa-heart", "colored");
                                button.querySelector("i").classList.remove("fa-regular");

                                // Mark as liked in localStorage
                                localStorage.setItem(`liked_${blogId}`, true);
                            } else {
                                // Change heart to outline
                                button.querySelector("i").classList.remove("fa-heart", "colored");
                                button.querySelector("i").classList.add("fa-regular");

                                // Remove liked status from localStorage
                                localStorage.removeItem(`liked_${blogId}`);

                                // Remove the card from the liked list
                                cardElement.remove();
                            }
                        } else {
                            console.error("Error updating like/unlike action");
                        }
                    })
                    .catch(error => console.error("Error:", error));
                });
            });
        });


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

        document.getElementById('search-input').addEventListener('input', function() {
            let searchTerm = this.value.toLowerCase();
            let cards = document.querySelectorAll('.card');

            cards.forEach(function(card) {
                let cardName = card.getAttribute('data-name');
                if (cardName.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html>
