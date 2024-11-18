<?php
include 'includes/db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get the data from the AJAX request
$data = json_decode(file_get_contents("php://input"), true);

// Validate input
if (!isset($data['id']) || !is_numeric($data['id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    exit;
}

$blog_id = (int) $data['id'];

// Check if the user has already liked the post
$like_check_query = "SELECT * FROM post_likes WHERE blog_id = ? AND user_id = ?";
$like_check_stmt = $conn->prepare($like_check_query);
$like_check_stmt->bind_param("ii", $blog_id, $user_id);
$like_check_stmt->execute();
$like_result = $like_check_stmt->get_result();

if ($like_result->num_rows > 0) {
    // User has already liked the post, so unlike it
    $delete_like_query = "DELETE FROM post_likes WHERE blog_id = ? AND user_id = ?";
    $delete_like_stmt = $conn->prepare($delete_like_query);
    $delete_like_stmt->bind_param("ii", $blog_id, $user_id);
    
    if ($delete_like_stmt->execute()) {
        // Decrease the likes count
        $update_likes_query = "UPDATE blogs SET likes = likes - 1 WHERE id = ?";
        $update_stmt = $conn->prepare($update_likes_query);
        $update_stmt->bind_param("i", $blog_id);
        $update_stmt->execute();
        echo json_encode(['status' => 'success', 'action' => 'unliked']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to unlike the post']);
    }
} else {
    // User has not liked the post, so like it
    $insert_like_query = "INSERT INTO post_likes (blog_id, user_id) VALUES (?, ?)";
    $insert_like_stmt = $conn->prepare($insert_like_query);
    $insert_like_stmt->bind_param("ii", $blog_id, $user_id);
    
    if ($insert_like_stmt->execute()) {
        // Increase the likes count
        $update_likes_query = "UPDATE blogs SET likes = likes + 1 WHERE id = ?";
        $update_stmt = $conn->prepare($update_likes_query);
        $update_stmt->bind_param("i", $blog_id);
        $update_stmt->execute();
        echo json_encode(['status' => 'success', 'action' => 'liked']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to like the post']);
    }
}

$conn->close();
?>
