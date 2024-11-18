<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'includes/db_connect.php';  // Make sure this path is correct

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $blog_id = $_POST['blog_id'];
    $user_name = $_POST['user_name'];
    $comment_text = $_POST['comment_text'];

    // Basic validation to check for missing fields
    if (empty($blog_id) || empty($user_name) || empty($comment_text)) {
        die("All fields are required.");
    }

    // Prepare the insert query
    $insert_comment_query = "INSERT INTO comments (blog_id, user_name, comment_text) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($insert_comment_query);

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("iss", $blog_id, $user_name, $comment_text);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect back to the blog post after successful comment insertion
        header("Location: blog_single.php?id=$blog_id");
    } else {
        // Show an error message if execution fails
        die("Error executing query: " . $stmt->error);
    }
}
?>

