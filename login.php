<?php
// Start a session
session_start();

// Connect to the database (replace with your database credentials)
$db = new mysqli("localhost", "root", "", "anoyomous acc");

// Check the connection
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["register"])) {
        // Registration process
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password

        // Insert user data into the database
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $password);

        if ($stmt->execute()) {
            $message = "Registration successful! You can now login.";
        } else {
            $message = "Error: " . $db->error;
        }

        $stmt->close();
    } elseif (isset($_POST["login"])) {
        // Login process
        $username = $_POST['username'];
        $password = $_POST['password'];

        // Retrieve the user's data from the database
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $db->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            // Authentication successful
            $_SESSION['user_id'] = $user['id'];
           
header('Location: myaccount.php');
        } else {
            // Authentication failed
            $message = "Login failed. Please check your username and password.";
        }

        $stmt->close();
    }
}

$db->close();
?>

<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" type="text/css" href="./assets/css/login.css">

    <title>Account Page</title>
    <style>
        .hidden {
            display: none;
        }
    </style>
</head>
<body>
    <center>
    <h2>Account Page</h2>

    <!-- Registration and Login Forms -->
    <form id="registerForm" action="" method="post" class="hidden">
        <h3>Register</h3>
        <label for="regUsername">Username:</label>
        <input type="text" name="username" id="regUsername" required><br><br>

        <label for="regEmail">Email:</label>
        <input type="email" name="email" id="regEmail" required><br><br>

        <label for="regPassword">Password:</label>
        <input type="password" name="password" id="regPassword" required><br><br>

        <input type="submit" name="register" value="Register">
    </form>

    <form id="loginForm" action="" method="post">
        <h3>Login</h3>
        <label for="loginUsername">Username:</label>
        <input type="text" name="username" id="loginUsername" required><br><br>

        <label for="loginPassword">Password:</label>
        <input type="password" name="password" id="loginPassword" required><br><br>

        <input type="submit" name="login" value="Login">
    </form>

    <div id="buttonContainer" style="position: absolute; top: 10px; right: 10px;">
    <button type="button" id="registerButton" style="display: block;" onclick="toggleForm('registerForm')">Register</button>
<button type="button" id="loginButton" style="display: none;" onclick="toggleForm('loginForm')">Login</button>
</div>


<script>
    function toggleForm(formId) {
        const registerForm = document.getElementById('registerForm');
        const loginForm = document.getElementById('loginForm');
        const registerButton = document.getElementById('registerButton');
        const loginButton = document.getElementById('loginButton');

        if (formId === 'registerForm') {
            registerForm.style.display = 'block';
            loginForm.style.display = 'none';
            registerButton.style.display = 'none';
            loginButton.style.display = 'block';
        } else {
            registerForm.style.display = 'none';
            loginForm.style.display = 'block';
            registerButton.style.display = 'block';
            loginButton.style.display = 'none';
        }
    }
</script>


    <p><?php echo $message; ?></p>
    </center>
</body>
</html>
