<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Notes</title>
    <script src="https://kit.fontawesome.com/b5f4641468.js" crossorigin="anonymous"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css?v=4">
    <style>
        /* Editor Container Styling */
        #editor-container {
            height: 400px;
            background-color: rgb(255, 255, 255);
            color: black;
            margin-bottom: 20px;
            padding: 10px;
            border: none;
            font-size: 2rem;
        }
        

        /* Styling for Quill Toolbar */
        .ql-toolbar.ql-snow {
            border-left: none;
            border-right: none;
            border-color: rgb(0, 0, 0);
            background-color: rgb(255, 255, 255);
        }

        /* Form Container Styling */
        #form-container {
            background-color: rgb(255, 255, 255);
            border-radius: 8px;
            border: 1px solid black;
            max-width: 800px;
            width: 100%;
            float: left;
            margin-bottom: 50px;
        }

        /* Thumbnail Container Styling */
        #thumbnail-container {
            border: 1px solid black;
            padding: 20px;
            border-radius: 8px;
            max-width: 30vw;
            width: 100%;
            min-height: 150px;
            max-height: 300px;
            float: right;
            background-color: rgb(255, 255, 255);
            font-size: 1.6rem;
            text-align: center;
            margin-left: auto;
        }

        /* Thumbnail Preview Styling */
        #thumbnail-preview {
            max-width: 100%;
            max-height: 150px;
            margin-top: 20px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Title Styling */
        #title {
            width: 100%;
            max-width: 800px;
            font-size: 4rem;
            border: none;
            border-bottom: 3px solid black;
            margin-top: 20px;
            margin-bottom: 30px;
        }

        /* Focus Styling for Title */
        #title:focus {
            outline: none;
        }

        /* Button Styling */
        .button {
            margin-bottom: 20px;
        }

        /* Label Styling */
        label{
            font-size: 30px;
        }

        /* Custom File Upload Button Styling */
        .custom-file-upload {
            display: inline-block;
            padding: 12px 25px;
            font-size: 1.8rem;
            font-weight: bold;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-align: center;
            width: 100%;
            max-width: 300px;
        }

        /* Hover and Active States for File Upload Button */
        .custom-file-upload:hover {
            background-color: #0056b3;
        }

        .custom-file-upload:active {
            background-color: #004080;
        }

        #button-container{
            text-align: center;
        }
        /* Wrapper for form and thumbnail containers */
        #form-wrapper {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        /* Adjust form container to take full width on small screens */
        #form-container {
            flex: 1;
            min-width: 300px; /* ensures it doesn't get too narrow */
        }

        /* Adjust thumbnail container styling to match the flex layout */
        #thumbnail-container {
            flex: 1;
            min-width: 300px; /* ensures it doesn't get too narrow */
            max-width: 30vw;
        }

        /* Adjust layout on smaller screens */
        @media (max-width: 768px) {
            #thumbnail-container {
                max-width: 100%;
            }
        }


        /* Dark Mode Styles */
        body.dark-mode #editor-container{
            background-color: black;
            color: white;
        }

        body.dark-mode #thumbnail-container, body.dark-mode #form-container{
            background-color: black;
            border: 1px solid white;
            color: white;
        }

        body.dark-mode .ql-toolbar.ql-snow, body.dark-mode #title{
            background-color: black;
            border-color: white;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Header Section with Navigation -->
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
                <a href="#" id="darkModeToggle"><img class="icons" id="brightnessIcon" src="images/brightness.png" alt="Toggle Dark Mode"></a>
            </div>
        </nav>
    </header>

    <main>
        <h1>Create New Notes</h1>
        <!-- Wrapper for Form and Thumbnail -->
        <div id="form-wrapper">
            <!-- Form to Upload Notes -->
            <div id="form-container">
                <form id="blogForm" action="save.php" method="POST" enctype="multipart/form-data">
                    <fieldset>
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" placeholder="Notes Title" required>
                        <label for="content">Content</label>
                        <input type="hidden" name="content" id="content">
                        <div id="editor-container"></div>

                        <input type="hidden" name="thumbnail" id="thumbnail">
                        <div id="button-container">
                            <a href="home.php"><button type="submit" class="button">Upload Notes</button></a>
                        </div>
                    </fieldset>
                </form>
            </div>

            <!-- Thumbnail Selection Section -->
            <div id="thumbnail-container">
                <h2>Select a thumbnail</h2>
                <label for="thumbnailInput" class="button">Choose Thumbnail</label>
                <input type="file" name="thumbnail" id="thumbnailInput" accept="image/*" hidden>

                <img id="thumbnail-preview" alt="Thumbnail Preview" style="display: none;">
            </div>
        </div>
    </main>


    <!-- Quill.js and Custom Scripts -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
        /* 
        This JavaScript code initializes a Quill editor with a custom toolbar that allows users to add headers, bold, italic, underline text, 
        and insert images or code blocks. It includes functionality to select and upload an image from the user's local system, then insert the 
        uploaded image into the editor. The code also handles uploading a thumbnail image, displaying a preview of the selected thumbnail, and 
        saving the thumbnail URL in a hidden form field. Additionally, it manages saving the content of the editor to a hidden form field upon 
        form submission. Finally, it implements the dark mode toggle, which switches between light and dark themes, saves the theme state in 
        localStorage, and updates the page's appearance (including the logo and dark mode button) based on the stored theme preference.
        */

        // Initialize Quill editor with custom toolbar
        var quill = new Quill('#editor-container', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline'],
                    ['image', 'code-block']
                ]
            }
        });

        // Handle image upload from toolbar
        quill.getModule('toolbar').addHandler('image', function() {
            selectLocalImage();
        });

        /* 
        Function to select an image from local system 
        and upload it to the server
        */
        function selectLocalImage() {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = () => {
                const file = input.files[0];
                if (file) {
                    const formData = new FormData();
                    formData.append('image', file);

                    fetch('upload_image.php', {
                        method: 'POST',
                        body: formData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            insertToEditor(data.url);
                        } else {
                            console.error('Error uploading image:', data.error);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                }
            };
        }

        // Function to insert uploaded image URL into Quill editor
        function insertToEditor(url) {
            const range = quill.getSelection();
            quill.insertEmbed(range.index, 'image', url);
        }

        /* 
        Function to handle thumbnail preview and upload
        */
        const thumbnailInput = document.getElementById('thumbnailInput');
        const thumbnailPreview = document.getElementById('thumbnail-preview');
        const thumbnailField = document.getElementById('thumbnail');

        thumbnailInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (event) => {
                thumbnailPreview.src = event.target.result;
                thumbnailPreview.style.display = 'block';
            };
            reader.readAsDataURL(file);

            // Upload thumbnail
            const formData = new FormData();
            formData.append('thumbnail', file);

            fetch('upload_thumbnail.php', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    thumbnailField.value = data.url; // Save path to hidden field
                    originalFilename = data.name; // Store the filename if needed
                } else {
                    console.error('Error uploading thumbnail:', data.error);
                }
            })
            .catch(error => console.error('Error:', error));
            }
        });

        // Saving content to hidden field on form submit
        document.getElementById('blogForm').onsubmit = function() {
            var content = document.querySelector('input[name=content]');
            content.value = quill.root.innerHTML;
        };

        // Dark Mode Toggle Functionality
        document.addEventListener("DOMContentLoaded", () => {
            const darkModeToggle = document.querySelector(".icons");
            const body = document.body;
            const logo = document.querySelector("nav img");
    
            // Retrieve the dark mode state from localStorage
            const savedDarkMode = localStorage.getItem("darkMode") === "true";
    
            // Apply the saved dark mode state
            if (savedDarkMode) {
                body.classList.add("dark-mode");
                darkModeToggle.setAttribute("src", "images/night-mode.png");
                logo.setAttribute("src", "images/Notify-logo-dark.png");
            }
    
            // Toggle dark mode on button click
            darkModeToggle.addEventListener("click", () => {
                const isDarkMode = body.classList.toggle("dark-mode");
    
                const brightnessImage = isDarkMode ? "images/night-mode.png" : "images/brightness.png";
                darkModeToggle.setAttribute("src", brightnessImage);
    
                const logoImage = isDarkMode ? "images/Notify-logo-dark.png" : "images/Notify-logo.png";
                logo.setAttribute("src", logoImage);
    
                localStorage.setItem("darkMode", isDarkMode); // Store the state
            });
        });
    </script>
</body>
</html>
