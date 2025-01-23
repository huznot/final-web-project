<!--
The following is just connection code to be able to connect my PHP scripts with my SQL server.

It also fetches the notes or referred to as "blogs" in my code for later
--->

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

// Fetch blogs from the database
$blog_sql = "SELECT * FROM blogs";
$blog_result = $conn->query($blog_sql);

if (!$blog_result) {
    die("Query failed: " . $conn->error);
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="style.css?v=4">
    <script src="https://kit.fontawesome.com/b5f4641468.js" crossorigin="anonymous"></script>  
    <style>
        .card figure {
            margin: 0;
            padding: 0;
            display: block;
        }
        /* Styling for elements with red text */
        .colored{
            color: red;
        }

        /* Ensures consistent image size for cards */
        .card-image{
            max-width: 300px;
            width: 100%;
            height: 230px;
        }

        /* Flex container for cards with spacing and padding */
        .card-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 50px;
        }

        /* Styling for each card */
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

        /* Inner box at the bottom of each card */
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

        /* Flex container for elements inside the inner box */
        .box-element-container{
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            position: absolute;
            bottom: 20px;
            max-width: 300px;
            width: 100%;
        }

        /* Adds spacing for the like button */
        .box-element-container a{
            display: flex;
            align-items: center;
            margin-left: 10px;
        }

        /* Like button positioning */
        .likes{
            position: absolute;
            align-items: center;
            display: flex;
            right: 10px;
            gap: 2px;
        }

        /* Margin for filename text inside the inner box */
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
                <a href="index.html"><figure><img id="logo" alt="Notify logo" src="images/Notify-logo.png"></figure></a>
                <!-- Navigation links -->
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
            
            <!-- Search bar and dark mode toggle -->
            <div id="split">
                <div id="search">
                    <div id="search-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <input id="search-input" type="text" placeholder="Search">
                </div>
                <a href="#" id="darkModeToggle">
                    <img class="icons" id="brightnessIcon" src="images/brightness.png" alt="Toggle Dark Mode">
                </a>
            </div>
        </nav>
    </header>
    
    <main>
        <div id="overlay"></div>
        <h1>All</h1>
        <section class="card-grid">
            <!-- Display notes dynamically fetched from database -->
            <?php
            if ($blog_result->num_rows > 0) {
                while ($row = $blog_result->fetch_assoc()) {
                    ?>
                        <div class="card" data-name="<?php echo strtolower($row['title']); ?>">
                            <figure>
                                <img src="<?php echo htmlspecialchars($row['thumbnail']); ?>" alt="<?php echo htmlspecialchars(basename($row['thumbnail'])) . " preview not available"; ?>" class="card-image">
                            </figure>
                            <div class="inner-box">
                                <div class="filename"> <?php echo htmlspecialchars($row['title']); ?> </div>
                                <div class="box-element-container">
                                    <a href="view.php?id=<?php echo $row['id']; ?>" class="button"><strong>View Notes</strong></a>
                                    <div class="likes">      
                                        <a href="#" class="like-btn" data-id="<?php echo $row['id']; ?>">
                                            <i class="fa-regular fa-heart"></i>
                                        </a>
                                        <p class="like-count"> <?php echo $row['likes']; ?> </p>
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
        document.addEventListener("DOMContentLoaded", () => {
        const darkModeToggle = document.querySelector("#darkModeToggle");
        const brightnessIcon = document.querySelector("#brightnessIcon");
        const notifyLogo = document.querySelector("#logo");
        const body = document.body;

        // Retrieve the dark mode state from localStorage
        const savedDarkMode = localStorage.getItem("darkMode") === "true";

        // Apply the saved dark mode state
        if (savedDarkMode) {
            body.classList.add("dark-mode");
            brightnessIcon.setAttribute("src", "images/night-mode.png");
            notifyLogo.setAttribute("src", "images/Notify-logo-dark.png");
        }

        darkModeToggle.addEventListener("click", (e) => {
            e.preventDefault(); // Prevent default link behavior

            // Toggle dark mode
            const isDarkMode = body.classList.toggle("dark-mode");

            // Update the brightness icon
            const brightnessImage = isDarkMode ? "images/night-mode.png" : "images/brightness.png";
            brightnessIcon.setAttribute("src", brightnessImage);

            // Update the Noteify logo
            const logoImage = isDarkMode ? "images/Notify-logo-dark.png" : "images/Notify-logo.png";
            notifyLogo.setAttribute("src", logoImage);

            // Save the dark mode state in localStorage
            localStorage.setItem("darkMode", isDarkMode);
            });
        });

        const likeButtons = document.querySelectorAll(".like-btn");

        //likes
        document.addEventListener("DOMContentLoaded", function() {

            likeButtons.forEach(button => {
                const blogId = button.getAttribute("data-id");
                const likeCountElement = button.nextElementSibling; // Select the <p> tag showing the count

                // Check if this blog post has already been liked (using localStorage)
                if (localStorage.getItem(`liked_${blogId}`)) {
                    // If already liked, set the heart to solid and red
                    button.querySelector("i").classList.remove("fa-regular");
                    button.querySelector("i").classList.add("fa-solid", "fa-heart", "colored");
                }

                button.addEventListener("click", function(event) {
                    event.preventDefault();

                    // Determine whether the user is liking or unliking
                    const isLiked = localStorage.getItem(`liked_${blogId}`);

                    let action = isLiked ? "unlike" : "like";
                    let newLikeCount;

                    // Send request to update like or unlike count
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
                            newLikeCount = data;
                            likeCountElement.textContent = newLikeCount;

                            if (action === "like") {
                                // Change heart to solid
                                button.querySelector("i").classList.remove("fa-regular");
                                button.querySelector("i").classList.add("fa-solid", "fa-heart", "colored");

                                // Mark this note as liked in localStorage
                                localStorage.setItem(`liked_${blogId}`, true);
                            } else {
                                // Change heart to empty
                                button.querySelector("i").classList.remove("fa-solid", "fa-heart", "colored");
                                button.querySelector("i").classList.add("fa-regular", "fa-heart");

                                // Remove liked status from localStorage
                                localStorage.removeItem(`liked_${blogId}`);
                            }
                        } else {
                            console.error("Error updating like/unlike action");
                        }
                    })
                    .catch(error => console.error("Error:", error));
                });
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