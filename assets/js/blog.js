document.querySelector('textarea').addEventListener('input', function() {
    const publishButton = document.querySelector('.publish-btn');
    if (this.value.trim()) {
        publishButton.removeAttribute('disabled');
    } else {
        publishButton.setAttribute('disabled', 'true');
    }
});
// Define the blog ID from a hidden input or from PHP directly
const blog_id = document.querySelector('input[name="blog_id"]').value;

const likeButton = document.querySelector('.likes');
const likeCount = document.querySelector('.like-count');
const likeHeart = document.querySelector('.like-heart');

if (likeButton) {
    likeButton.addEventListener('click', function() {
        // Disable button to prevent multiple clicks
        likeButton.disabled = true;

        let count = parseInt(likeCount.textContent);
        
        // AJAX call to update the like count in the database
        fetch('update_likes.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: blog_id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Toggle filled heart and update count based on action
                if (data.action === 'liked') {
                    likeHeart.classList.add('filled'); // Add the fill
                    count++; // Increment like count
                } else if (data.action === 'unliked') {
                    likeHeart.classList.remove('filled'); // Remove the fill
                    count--; // Decrement like count
                }
                likeCount.textContent = count; // Update the like count text
            } else {
                alert('Error updating likes: ' + data.message);
                // Optionally, you could revert the UI change if desired
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update likes. Please try again later.');
        })
        .finally(() => {
            // Re-enable the button after the AJAX call
            likeButton.disabled = false;
        });
    });
}



document.addEventListener("DOMContentLoaded", function () {
    let slides = document.querySelectorAll('.comment-slide');
    let currentSlide = 0;
    const slideDuration = 5000;  // 5 seconds

    function showSlide(index) {
        slides.forEach((slide, i) => {
            slide.classList.remove('active');
            if (i === index) {
                slide.classList.add('active');
            }
        });
    }

    function nextSlide() {
        currentSlide = (currentSlide + 1) % slides.length;
        showSlide(currentSlide);
    }

    // Show the first slide
    if (slides.length > 0) {
        showSlide(currentSlide);
        // Automatically change slides every 5 seconds
        setInterval(nextSlide, slideDuration);
    }
});
