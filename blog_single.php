<?php
// Include database connection and start session
include 'includes/db_connect.php';
session_start();  // Start the session to access logged-in user data

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Store the current blog URL in the redirect_to query parameter
    $current_url = urlencode("http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
    header("Location: users/login.php?redirect_to=$current_url");
    exit;
}

// Get the logged-in user ID
$user_id = $_SESSION['user_id'];

// Get the blog ID from the URL
if (isset($_GET['id'])) {
    $blog_id = $_GET['id'];
    
    // Fetch the blog details from the database
    $query = "SELECT * FROM blogs WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $blog_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $blog = $result->fetch_assoc();
    
    // If no blog is found, redirect to the blog home page
    if (!$blog) {
        header("Location: blogs_home.php");
        exit;
    }
    
    // Check if the user has already viewed the blog post
    if ($user_id) {
        // Check if this user has already viewed this blog post
        $view_check_query = "SELECT * FROM post_views WHERE blog_id = ? AND user_id = ?";
        $view_check_stmt = $conn->prepare($view_check_query);
        $view_check_stmt->bind_param("ii", $blog_id, $user_id);
        $view_check_stmt->execute();
        $view_result = $view_check_stmt->get_result();
        
        // If no previous view record exists for this user and blog, update the views
        if ($view_result->num_rows === 0) {
            // Insert a new record into post_views to track this view
            $insert_view_query = "INSERT INTO post_views (blog_id, user_id) VALUES (?, ?)";
            $insert_view_stmt = $conn->prepare($insert_view_query);
            $insert_view_stmt->bind_param("ii", $blog_id, $user_id);
            $insert_view_stmt->execute();

            // Now update the blog's views count
            $update_views_query = "UPDATE blogs SET views = views + 1 WHERE id = ?";
            $update_stmt = $conn->prepare($update_views_query);
            $update_stmt->bind_param("i", $blog_id);
            $update_stmt->execute();
        }
    }

    // Check if the user has already liked the blog post
    $like_check_query = "SELECT * FROM post_likes WHERE blog_id = ? AND user_id = ?";
    $like_check_stmt = $conn->prepare($like_check_query);
    $like_check_stmt->bind_param("ii", $blog_id, $user_id);
    $like_check_stmt->execute();
    $like_result = $like_check_stmt->get_result();

    $user_has_liked = $like_result->num_rows > 0;  // Boolean flag to track if the user has liked the post
    
    // Fetch the comments for the blog post
    $comments_query = "SELECT * FROM comments WHERE blog_id = ? ORDER BY comment_date DESC";
    $comments_stmt = $conn->prepare($comments_query);
    $comments_stmt->bind_param("i", $blog_id);
    $comments_stmt->execute();
    $comments_result = $comments_stmt->get_result();

} else {
    // If no ID is provided, redirect to the blog home page
    header("Location: blogs_home.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $blog['title']; ?></title>
    <link rel="stylesheet" href="assets/css/blog.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="blog_style">
        <div class="hero">
            <div class="hero-content">
                <h1>Sustainability Spotlight Stories That Matter</h1>
            </div>
        </div>
    </div>

    <div class="blog-single-container">
        <div class="blog-header">
            <img src="assets/images/blogs/<?php echo $blog['image']; ?>" alt="<?php echo $blog['title']; ?>" class="blog-single-image">
            <h1><?php echo $blog['title']; ?></h1>
            <p class="blog-meta">By <?php echo $blog['author']; ?> | <?php echo date("F j, Y", strtotime($blog['published_date'])); ?> | <?php echo $blog['read_time']; ?> min read</p>
        </div>

        <div class="blog-content">
            <p><?php echo $blog['content']; ?></p>
        </div>

        <div class="blog-footer">
            <div class="blog-interactions">
                <span class='likes'>
                    <span class='like-count'><?php echo $blog['likes']; ?></span> Likes
                    <i class='like-heart <?php echo $user_has_liked ? "filled" : "far"; ?> fa-heart'></i>
                </span>
                <span><?php echo $blog['views']; ?> Views</span>
            </div>
            <a href="blog_home.php" class="back-to-blogs">Back to Blogs</a>
        </div>
    </div>

    <div class="comments-section">
        <h2 class="comments-heading">Comments</h2>
        <div class="slideshow-container">
            <?php if ($comments_result->num_rows > 0): ?>
                <?php while($comment = $comments_result->fetch_assoc()): ?>
                    <div class="comment-slide">
                        <p><strong><?php echo $comment['user_name']; ?></strong> on <?php echo date("F j, Y, g:i a", strtotime($comment['comment_date'])); ?></p>
                        <p><?php echo $comment['comment_text']; ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>No comments yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="comment-box">
        <h3>Leave your Comments</h3>
        <form action="submit_comment.php" method="POST" class="comment-form">
            <input type="hidden" name="blog_id" value="<?php echo $blog_id; ?>">
            <div class="comment-input">
                <input type="text" name="user_name" placeholder="Your Name" required>
                <textarea name="comment_text" placeholder="Write a comment..." required></textarea>
            </div>
            <div class="comment-buttons">
                <button type="submit" class="publish-btn" disabled>Publish</button>
            </div>
        </form>
    </div>

    <script src="assets/js/blog.js"></script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
