<?php
// Include database connection file
include('../config.php');
session_start();

// If there is a "redirect_to" in the query string, store it in the session to remember it for later
if (isset($_GET['redirect_to'])) {
    $_SESSION['redirect_to'] = $_GET['redirect_to'];
}

if (isset($_SESSION['user_id'])) {
    $redirect_to = isset($_SESSION['redirect_to']) ? urldecode($_SESSION['redirect_to']) : '../index.php';
    header("Location: $redirect_to");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $conn->real_escape_string($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            $redirect_to = isset($_SESSION['redirect_to']) ? urldecode($_SESSION['redirect_to']) : '../index.php';
            unset($_SESSION['redirect_to']);
            header("Location: $redirect_to");
            exit;
        } else {
            $error_message = "Incorrect password.";
        }
    } else {
        $error_message = "No account found with that email.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FarmWise Connect</title>
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>
<body>
    <div class="container">
        <!-- Back to Home link -->
        <a href="../index.php" class="back-to-home">🔙</a>

        <form class="login-form" action="login.php<?php echo isset($_GET['redirect_to']) ? '?redirect_to=' . urlencode($_GET['redirect_to']) : ''; ?>" method="POST">
            <h2>Login</h2>
            <?php if (!empty($error_message)) { echo "<p class='error'>$error_message</p>"; } ?>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" required>

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>

            <div class="link-text">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>

            <button type="submit">Login</button>

            <div class="link-text">
                <p>Don't have an account? <a href="register.php<?php echo isset($_GET['redirect_to']) ? '?redirect_to=' . urlencode($_GET['redirect_to']) : ''; ?>">Register</a></p>
            </div>
        </form>
    </div>
</body>
</html>
