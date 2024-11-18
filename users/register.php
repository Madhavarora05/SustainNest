<?php
include('../config.php');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $conn->real_escape_string($_POST['fullname']);
    $username = $conn->real_escape_string($_POST['username']);
    $email = $conn->real_escape_string($_POST['email']);
    $phone = $conn->real_escape_string($_POST['phone']);
    $gender = $conn->real_escape_string($_POST['gender']);
    $write_blog = $conn->real_escape_string($_POST['write_blog']);
    $password = $conn->real_escape_string($_POST['password']);
    $confirm_password = $conn->real_escape_string($_POST['confirm_password']);
    
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        $check_email = "SELECT * FROM users WHERE email='$email'";
        $result = $conn->query($check_email);

        if ($result === false) {
            $error_message = "Error checking email: " . $conn->error;
        } elseif ($result->num_rows > 0) {
            $error_message = "Email is already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (fullname, username, email, phone, gender, password, can_write_blog) 
                VALUES ('$fullname', '$username', '$email', '$phone', '$gender', '$hashed_password', '$write_blog')";
    
            if ($conn->query($sql) === TRUE) {
                $success_message = "Registration successful!";
                header("Location: login.php"); 
                exit;
            } else {
                $error_message = "Error: " . $sql . "<br>" . $conn->error;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FarmWise Connect</title>
    <link rel="stylesheet" href="../assets/css/forms.css">
</head>
<body>
    <div class="container">
        <a href="../index.php" class="back-to-home">🔙</a>

        <form action="register.php" method="POST">
            <h2>Registration</h2>
            <?php if (!empty($error_message)) { echo "<p class='error'>$error_message</p>"; } ?>
            <?php if (!empty($success_message)) { echo "<p class='success'>$success_message</p>"; } ?>

            <div class="form-group">
                <div class="form-field">
                    <label for="fullname">Full Name:</label>
                    <input type="text" name="fullname" id="fullname" required>
                </div>
                <div class="form-field">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" required>
                </div>
            </div>

            <div class="form-group">
                <div class="form-field">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="form-field">
                    <label for="phone">Phone Number:</label>
                    <input type="tel" name="phone" id="phone" required>
                </div>
            </div>

            <div class="form-group">
                <div class="form-field">
                    <label for="password">Password:</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="form-field">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                </div>
            </div>

            <div class="form-group full-width">
                <label>Gender:</label>
                <label><input type="radio" name="gender" value="male" required> Male</label>
                <label><input type="radio" name="gender" value="female"> Female</label>
                <label><input type="radio" name="gender" value="prefer_not_to_say"> Prefer not to say</label>
            </div>

            <div class="form-group full-width">
                <label for="write_blog">Do you want to write blogs?</label>
                    <label><input type="radio" id="yes" name="write_blog" value="yes" required>Yes</label>
                    <label><input type="radio" id="no" name="write_blog" value="no" required>No</label>
            </div>


            <button type="submit">Register</button>

            <p class="link-text">Already have an account? <a href="login.php">Login</a></p>
        </form>
    </div>
</body>
</html>
