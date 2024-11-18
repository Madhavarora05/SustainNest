<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $conn->real_escape_string(trim($_POST['name']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $phone = $conn->real_escape_string(trim($_POST['phone']));
    $message = $conn->real_escape_string(trim($_POST['message']));

    if (empty($name) || empty($email) || empty($phone) || empty($message)) {
        echo "<script>alert('Please fill in all fields.'); window.location.href = 'ContactUs.php';</script>";
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Invalid email format.'); window.location.href = 'ContactUs.php';</script>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO contact_us (name, email, phone, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $phone, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Your message has been sent successfully!'); window.location.href = 'ContactUs.php';</script>";
    } else {
        echo "<script>alert('Error: " . $stmt->error . "'); window.location.href = 'ContactUs.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>
