<?php
session_start();
$message = ''; // Initialize the $message variable

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to the login page if not logged in
    exit();
}

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["post"])) {
    // Handle the post submission
    if (isset($_POST["post_text"])) {
        $postText = $_POST["post_text"];

        // Ensure that only image files are uploaded
        if ($_FILES["post_image"]["type"] == "image/jpeg" || $_FILES["post_image"]["type"] == "image/png") {
            $uploadsDir = 'uploads/';
            $fileName = $_FILES["post_image"]["name"];
            $targetPath = $uploadsDir . $fileName;

            // Move the uploaded image to the uploads directory
            move_uploaded_file($_FILES["post_image"]["tmp_name"], $targetPath);

            // Insert the post into the database (replace with your database logic)
            $db = new mysqli("localhost", "root", "", "anoyomous acc");
            if ($db->connect_error) {
                die("Connection failed: " . $db->connect_error);
            }

            $user_id = $_SESSION['user_id'];
            $sql = "INSERT INTO posts (user_id, post_text, post_image) VALUES (?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("iss", $user_id, $postText, $fileName);

            if ($stmt->execute()) {
                $message = "Post added successfully.";
            } else {
                $message = "Error adding post: " . $db->error;
            }

            $stmt->close();
            $db->close();
        } else {
            $message = "Invalid file type. Please upload a JPEG or PNG image.";
        }
    }
    
    // Redirect to the same page to prevent form resubmission on page refresh
    header("Location: mainpage.php");
    exit();
}

// Fetch and display existing posts (replace with your database logic)
$db = new mysqli("localhost", "root", "", "anoyomous acc");
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

// Retrieve posts with upvote and downvote counts
$sql = "SELECT posts.*, 
               COALESCE(SUM(CASE WHEN post_votes.vote_type = 'up' THEN 1 ELSE 0 END), 0) AS upvotes, 
               COALESCE(SUM(CASE WHEN post_votes.vote_type = 'down' THEN 1 ELSE 0 END), 0) AS downvotes
        FROM posts
        LEFT JOIN post_votes ON posts.post_id = post_votes.post_id
        GROUP BY posts.post_id
        ORDER BY posts.post_id DESC";

$result = $db->query($sql);

$db->close();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="./assets/css/mainpage.css">
    <title>Main Page</title>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <a href="#">Home</a>
        <a href="#">About</a>
        <a href="#">Services</a>
        <a href="#">Contact</a>
    </div>
<div id="content">
    <center>
</br>
<p><?php echo $message; ?></p>
    <!-- Post Form -->
    <h2>Post Something</h2>
    <form method="post" enctype="multipart/form-data">
        <label for="post_text">Write your post:</label>
        <textarea name="post_text" id="post_text" rows="4" cols="50" required></textarea>
        <input type="file" name="post_image" id="post_image" accept="image/*" required>
        <input type="submit" name="post" value="Post">
    </form>
    </center>
    <!-- Display Posts (adjust this part based on your needs) -->
    <h2>Posts</h2>
    <?php
        while ($row = $result->fetch_assoc()) {
            echo '<div class="post-container">';
            echo '<div class="post-actions">';
            echo '<p>' . $row['post_text'] . '</p>';
            echo '<img src="uploads/' . $row['post_image'] . '" alt="Post Image" class="post-image">';
            echo '<button class="upvote-button" data-post-id="' . $row['post_id'] . '">Upvote (' . $row['upvotes'] . ')</button>';
            echo '<button class="downvote-button" data-post-id="' . $row['post_id'] . '">Downvote (' . $row['downvotes'] . ')</button>';
            echo '</div>';
            echo '<button class="upvote-button" data-post-id="' . $row['post_id'] . '">Upvote (' . $row['upvotes'] . ')</button>';
            echo '<button class="downvote-button" data-post-id="' . $row['post_id'] . '">Downvote (' . $row['downvotes'] . ')</button>';
            echo '</div>';
            echo '</div>';
        }
    ?>
</div>
</body>
</html>
