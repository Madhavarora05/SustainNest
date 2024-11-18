<?php include 'config.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Page - FarmWise Connect</title>
    <link rel="stylesheet" href="./assets/css/blog.css"> <!-- Link to your CSS -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script> <!-- Font Awesome -->
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="blog_style"> <!-- Added blog_style class -->
        <div class="hero">
            <div class="hero-content">
                <h1>Sustainability Spotlight Stories That Matter</h1>
            </div>
        </div>
    </div>
    <div class="blog-nav-header">
        <div class="left-links">
            <a href="all_blogs.php">All Blogs</a>
            <a href="categories.php">Categories</a>
            <a href="#" onclick="handleWriteBlog(); return false;">Write a Blog</a> <!-- Call JavaScript function -->
        </div>
        <div class="right-search">
            <form class="search-bar" onsubmit="scrollToResults(event);">
                <input type="search" id="search-input" class="search-input" placeholder="Search for your blog" onkeyup="filterBlogs()">
                <button type="submit" class="search-btn">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="blog-container" id="blog-container">
    <?php
    // Fetch the top 5 blogs ordered by likes
    $query = "SELECT * FROM blogs ORDER BY likes DESC LIMIT 5";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            $title = $row['title'];
            $excerpt = $row['excerpt'];
            $image = $row['image'];
            $author = $row['author'];
            $date = $row['published_date'];
            $views = $row['views'];
            $read_time = $row['read_time']; // Assuming you store this in the database
            $likes = $row['likes'];
            $id = $row['id'];
            
            echo "
            <div class='blog-card' data-title='$title' data-author='$author'>
                <img src='./assets/images/blogs/$image' alt='$title' class='blog-img'>
                <div class='blog-content'>
                    <div class='blog-info'>
                        <div class='user-info'>
                            <i class='fas fa-user-circle fa-2x' id='user-icon'></i> <!-- User icon -->
                            <span class='author-name'>$author</span>
                        </div>
                        <div class='date-info'>
                            <span>$date</span> | <span>$read_time min read</span>
                        </div>
                    </div>
                    <h2>$title</h2>
                    <p>$excerpt</p>
                    <div class='divider'></div>
                    <div class='blog-stats'>
                        <span>$views views</span>
                        <span class='likes' data-id='$id'> <!-- Added data-id attribute -->
                            <span class='like-count'>$likes</span> <!-- Like count -->
                            <i class='like-heart far fa-heart'></i> <!-- Heart icon -->
                        </span>
                    </div>
                    <a href='blog_single.php?id=$id' class='read-more'>Read More</a>
                </div>
            </div>";
        }
    } else {
        echo "<p>No blogs found.</p>";
    }
    ?>
    </div>

    <!-- "All Blogs" Button -->
    <div class="all-blogs-button">
        <a href="all_blogs.php" class="view-all-blogs">All Blogs ⬇️</a>
    </div>

    <script>
        function handleWriteBlog() {
            <?php if (!isset($_SESSION['user_id'])) { ?>
                // User is not logged in, redirect to login page with the current page as redirect
                const currentUrl = encodeURIComponent("http://<?php echo $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>");
                window.location.href = 'users/login.php?redirect_to=' + currentUrl;
            <?php } else { 
                // User is logged in, check if they can write a blog
                $user_id = $_SESSION['user_id'];
                $query = "SELECT can_write_blog FROM users WHERE id = '$user_id'";
                $result = $conn->query($query);
                $row = $result->fetch_assoc();
                if ($row['can_write_blog'] === 'no') { ?>
                    alert('You cannot write blogs as you have chosen not to.'); 
                <?php } else { ?>
                    window.location.href = 'write_blog.php'; // Redirect to the blog writing page
                <?php } 
            } ?>
        }

        // Filter blogs based on search input
        function filterBlogs() {
            const input = document.getElementById('search-input').value.toLowerCase();
            const blogContainer = document.getElementById('blog-container');
            const blogs = blogContainer.getElementsByClassName('blog-card');

            for (let i = 0; i < blogs.length; i++) {
                const title = blogs[i].getAttribute('data-title').toLowerCase();
                const author = blogs[i].getAttribute('data-author').toLowerCase();
                if (title.includes(input) || author.includes(input)) { // Check both title and author
                    blogs[i].style.display = ''; // Show the blog card
                } else {
                    blogs[i].style.display = 'none'; // Hide the blog card
                }
            }
        }

        function scrollToResults(event) {
            // Prevent default form submission for scrolling
            event.preventDefault();

            // Smooth scroll to blog container
            const blogContainer = document.getElementById('blog-container');
            if (blogContainer) {
                blogContainer.scrollIntoView({ behavior: 'smooth' }); // Scroll smoothly to the blog container
            }

            // Call filterBlogs to apply filtering based on the search input
            filterBlogs();
        }

        // Select all like buttons
        const likeButtons = document.querySelectorAll('.likes');

        likeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const heartIcon = this.querySelector('.like-heart');
                const likeCount = this.querySelector('.like-count');
                const blogId = this.getAttribute('data-id'); // Get blog ID
                
                let count = parseInt(likeCount.textContent);

                // Toggle filled heart and increment/decrement like count
                if (heartIcon.classList.contains('filled')) {
                    heartIcon.classList.remove('filled'); // Remove the fill
                    count--; // Decrement like count
                    updateLikes(blogId, 'decrement'); // Call update like function
                } else {
                    heartIcon.classList.add('filled'); // Add the fill
                    count++; // Increment like count
                    updateLikes(blogId, 'increment'); // Call update like function
                }

                likeCount.textContent = count; // Update the like count text
            });
        });

        // Function to send AJAX request to update likes
        function updateLikes(blogId, action) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "update_likes.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (xhr.status !== 200) {
                    console.error('Error updating likes');
                }
            };
            xhr.send("blog_id=" + blogId + "&action=" + action); // Send blog ID and action
        }

    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>
