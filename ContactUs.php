<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - FarmWise Connect</title>
    <link rel="stylesheet" href="./assets/css/contact.css"> <!-- Link to your CSS -->
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="contact-us">
        <div class="hero">
        <!-- Hero content here -->
            <div class="hero-content">
                <h1>Contact Us</h1>
                <h2>It will be our pleasure to serve you</h2>

                <form action="contact_handling.php" method="POST"> <!-- Change to your form handling script -->
                    <div class="styled-input">
                        <input placeholder="Enter your full name" type="text" id="name" name="name" required>
                    </div>

                    <div class="styled-input">
                        <input placeholder="Enter your email" type="email" id="email" name="email" required>
                    </div>

                    <div class="styled-input">
                        <input placeholder="Enter your phone number" type="tel" id="phone" name="phone" required>
                    </div>

                    <div class="styled-input">
                        <textarea id="message" placeholder="Message" name="message" rows="5" required></textarea>
                    </div>

                    <button type="submit" class="submit-btn">SUBMIT</button>
                </form>
            </div>
        </div>
    </div>
    

    <?php include 'includes/footer.php'; ?>
</body>
</html>
