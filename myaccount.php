<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Redirect to the login page if not logged in
    exit();
}

// Connect to the database (replace with your database credentials)
$db = new mysqli("localhost", "root", "", "anoyomous acc");

// Check the connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$user_id = $_SESSION['user_id'];

// Fetch the current user's information
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $db->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$message = ''; // Initialize a message variable

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["updateUsername"])) {
        // Handle username update
        if (isset($_POST["newUsername"])) {
            $newUsername = $_POST["newUsername"];
            // Update the username in the database
            // Add your database update logic here
            // Example: $db->query("UPDATE users SET username='$newUsername' WHERE id=$_SESSION[user_id]");
            $message = "Username updated successfully.";
        }
    }
    
    if (isset($_POST["updateEmail"])) {
        // Handle email update
        if (isset($_POST["newEmail"])) {
            $newEmail = $_POST["newEmail"];
            // Update the email in the database
            // Add your database update logic here
            // Example: $db->query("UPDATE users SET email='$newEmail' WHERE id=$_SESSION[user_id]");
            $message = "Email updated successfully.";
        }
    }

    if (isset($_POST["updatePassword"])) {
        // Handle password update
        if (isset($_POST["newPassword"])) {
            $newPassword = password_hash($_POST['newPassword'], PASSWORD_BCRYPT); // Hash the new password
            // Update the password in the database
            // Add your database update logic here
            // Example: $db->query("UPDATE users SET password='$newPassword' WHERE id=$_SESSION[user_id]");
            $message = "Password updated successfully.";
        }
    }
}

$db->close();
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="./assets/css/myaccount.css">
    <title>My Account</title>
</head>
<body>
        <!-- Navbar -->
<div class="navbar">
    <a href="mainpage.php">Home</a>
    <a href="logout.php">Logout</a>
</div>
<div class="content">
</hr>
<h2>Welcome, <?php echo $user['username']; ?></h2>
    <h3>Current User Information:</h3>
    <p>Username: <?php echo $user['username']; ?></p>
    <p>Email: <?php echo $user['email']; ?></p>
    <p><b>log:</b><?php echo $message; ?></p>

    <h2>Update Username</h2>
<form method="post">
    <input type="text" name="newUsername" placeholder="New Username" required>
    <input type="submit" name="updateUsername" id="updateUsernameButton" value="Update">
</form>

<h2>Update Email</h2>
<form method="post">
    <input type="email" name="newEmail" placeholder="New Email" required>
    <input type="submit" name="updateEmail" id="updateEmailButton" value="Update">
</form>

<h2>Update Password</h2>
<form method="post">
    <input type="password" name="newPassword" placeholder="New Password" required>
    <input type="submit" name="updatePassword" id="updatePasswordButton" value="Update">
</form>

</div>
</body>
</html>
