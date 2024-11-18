<?php
// Include database connection and start session
include 'includes/db_connect.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    $current_url = urlencode("write_blog.php");
    header("Location: users/login.php?redirect_to=$current_url");
    exit;
}

// Function to validate and handle image upload
function handleImageUpload($file) {
    // Define allowed file types
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Validation checks
    if (!isset($file['error']) || is_array($file['error'])) {
        throw new RuntimeException('Invalid parameters.');
    }
    
    // Check for upload errors
    switch ($file['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('No file uploaded.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('File size exceeded.');
        default:
            throw new RuntimeException('Unknown error occurred.');
    }
    
    // Check file size
    if ($file['size'] > $max_size) {
        throw new RuntimeException('File size exceeded limit of 5MB.');
    }
    
    // Check MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime_type = $finfo->file($file['tmp_name']);
    
    if (!in_array($mime_type, $allowed_types)) {
        throw new RuntimeException('Invalid file format. Only JPEG, PNG and GIF allowed.');
    }
    
    // Generate unique filename
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $unique_filename = uniqid() . '_' . time() . '.' . $extension;
    
    // Define upload path - physical path where file will be stored
    $upload_dir = 'assets/images/blogs/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            throw new RuntimeException('Failed to create upload directory.');
        }
        chmod($upload_dir, 0777); // Ensure directory is writable
    }
    
    // Debug directory permissions
    error_log("Upload directory: " . $upload_dir);
    error_log("Directory exists: " . (file_exists($upload_dir) ? 'yes' : 'no'));
    error_log("Directory writable: " . (is_writable($upload_dir) ? 'yes' : 'no'));
    
    // Full path for storing the file
    $destination = $upload_dir . $unique_filename;
    
    // Move file to destination
    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $error = error_get_last();
        throw new RuntimeException('Failed to move uploaded file. Error: ' . ($error['message'] ?? 'Unknown error'));
    }
    
    // Return only the filename for database storage
    return $unique_filename;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Debug information
        error_log("POST request received");
        error_log("Files array: " . print_r($_FILES, true));
        
        // Validate and sanitize text inputs
        $title = $conn->real_escape_string(trim($_POST['title']));
        $excerpt = $conn->real_escape_string(trim($_POST['excerpt']));
        $author = $conn->real_escape_string(trim($_POST['author']));
        $content = $conn->real_escape_string(trim($_POST['content']));
        
        // Validate required fields
        if (empty($title) || empty($excerpt) || empty($author) || empty($content)) {
            throw new RuntimeException('All fields are required.');
        }
        
        // Handle image upload
        if (!isset($_FILES['image'])) {
            throw new RuntimeException('No image uploaded.');
        }
        
        // Get only the filename from the upload handler
        $image_filename = handleImageUpload($_FILES['image']);
        
        // Calculate read time
        $word_count = str_word_count(strip_tags($content));
        $read_time = max(1, ceil($word_count / 200));
        
        // Insert blog post into database
        $sql = "INSERT INTO blogs (title, image, excerpt, author, content, published_date, views, likes, read_time) 
                VALUES (?, ?, ?, ?, ?, NOW(), 0, 0, ?)";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $title, $image_filename, $excerpt, $author, $content, $read_time);
        
        if ($stmt->execute()) {
            $last_id = $stmt->insert_id;
            echo "<script>alert('Blog post published successfully!'); window.location.href = 'blog_single.php?id=$last_id';</script>";
        } else {
            throw new RuntimeException('Database error occurred.');
        }
        
    } catch (RuntimeException $e) {
        error_log("Blog upload error: " . $e->getMessage());
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write a Blog - SustainNest</title>
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .blog-container {
            width: 80%;
            max-width: 800px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .blog-container h1 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #333;
        }
        .blog-container input, .blog-container textarea {
            width: 100%;
            padding: 10px;
            margin-top: 8px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 16px;
        }
        #editor {
            height: 200px;
            margin-top: 8px;
        }
        .blog-container button {
            margin-top: 15px;
            padding: 10px 15px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .blog-container button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="blog-container">
        <h1>Write Your Blog</h1>
        <form action="write_blog.php" method="POST" enctype="multipart/form-data" onsubmit="saveContent()">
            <!-- Title Field -->
            <input type="text" name="title" placeholder="Add a Catchy Title" required>

            <!-- Image Upload Field -->
            <input type="file" name="image" accept="image/*" required>

            <!-- Opening Line / Excerpt -->
            <input type="text" name="excerpt" placeholder="Enter an opening line that is shown on your card" required>

            <!-- Author Field -->
            <input type="text" name="author" placeholder="Author Name" required>

            <!-- Quill Editor for Content -->
            <div id="editor"></div>
            
            <!-- Hidden input to hold the editor's HTML content -->
            <input type="hidden" name="content" id="content">
            
            <button type="submit">Publish Post</button>
        </form>
    </div>

    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>
    <script>
        // Initialize Quill editor with text color and background color options
        var quill = new Quill('#editor', {
            theme: 'snow',
            placeholder: 'Write something amazing...',
            modules: {
                toolbar: [
                    [{ 'header': [2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline'],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    [{ 'align': [] }],
                    [{ 'color': [] }, { 'background': [] }],
                ]
            }
        });

        // Save Quill editor content to hidden input before form submission
        function saveContent() {
            document.getElementById('content').value = quill.root.innerHTML;
        }
    </script>
</body>
</html>
