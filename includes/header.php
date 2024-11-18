<?php session_start(); // Start the session ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SustainNest</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"> <!-- Ensure Font Awesome is included -->
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <img src="./assets/images/logo.png" alt="SustainNest Logo">
            </div>
            <div class="nav-links">
                <ul class="menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="blog_home.php">Blogs</a></li>
                    <li><a href="ContactUs.php">Contact Us</a></li>
                    <?php if (isset($_SESSION['username'])): ?>
                        <!-- User is logged in -->
                        <li>
                            <div class="user-profile">
                                <div class="dropdown">
                                    <i class="fas fa-user-circle fa-2x" id="user-icon"></i> <!-- User icon -->
                                    <div class="dropdown-content">
                                        <p>Hello, <?php echo $_SESSION['username']; ?></p>
                                        <a href="users/logout.php" class="dropdown-btn">Sign Out</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    <?php else: ?>
                        <!-- User is not logged in -->
                        <li><a href="users/login.php" class="donate-button">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="burger">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </div>
        </nav>
    </header>

    <script>
        const burger = document.querySelector('.burger');
        const navLinks = document.querySelector('.nav-links');
        const userIcon = document.querySelector('#user-icon');
        const dropdownContent = document.querySelector('.dropdown-content');

        // Event listener for burger menu
        burger.addEventListener('click', () => {
            navLinks.classList.toggle('nav-active'); // Toggle the menu display
        });

        // Toggle dropdown menu for user profile
        userIcon.addEventListener('click', () => {
            dropdownContent.classList.toggle('show');
        });

        // Optional: Close dropdown when clicking outside
        window.addEventListener('click', (event) => {
            if (!userIcon.contains(event.target) && !dropdownContent.contains(event.target)) {
                dropdownContent.classList.remove('show');
            }
        });
    </script>
</body>
</html>
