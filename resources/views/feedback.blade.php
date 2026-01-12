<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <title>Document</title>
</head>
<body class="feedback-body">

<!-- Back Icon -->
<button class="back-icon" onclick="window.history.back()">← back</button>

<!-- Header -->
<div class="feedback-header">
    <h2>Customer Feedback</h2>
    <small>Table #: 1</small>
</div>

<!-- Card -->
<div class="feedback-card">
    <h3>Share Your Experience</h3>
    <p class="subtitle">Your feedback helps us improve our service</p>

    <p class="rating-label">How would you rate your experience?</p>

    <!-- Stars -->
  <div class="star-rating">
    <span class="star" data-value="1">★</span>
    <span class="star" data-value="2">★</span>
    <span class="star" data-value="3">★</span>
    <span class="star" data-value="4">★</span>
    <span class="star" data-value="5">★</span>
</div>

<input type="hidden" name="star_rating" id="starRating" required>


    <!-- Name -->
    <label>Your Name <span>*</span></label>
    <input type="text" placeholder="Enter your name">

    <!-- Table -->
    <label>Table Number</label>
    <input type="text" value="Table 1" readonly>

    <!-- Comment -->
    <label>Additional Comments (Optional)</label>
    <textarea placeholder="Tell us more about your experience..."></textarea>

    <!-- Submit -->
    <button class="submit-btn">
        ✈ Submit Feedback
    </button>
</div>

<!-- Success Modal -->
<div id="feedbackModal" class="modal-overlay">
    <div class="modal-box">
       <div class="modal-icon" id="modalStars"></div>
        <h3>Thank You!</h3>
        <p>Your feedback has been submitted successfully.</p>
        <button class="modal-btn" onclick="closeModal()">OK</button>
    </div>
</div>



<script>
    const stars = document.querySelectorAll('.star');
    const starRatingInput = document.getElementById('starRating');
    const submitBtn = document.querySelector('.submit-btn');
    const modal = document.getElementById('feedbackModal');
    const modalStars = document.getElementById('modalStars');

    let selectedRating = 0;

    // ⭐ Clickable star rating
    stars.forEach(star => {
        star.addEventListener('click', () => {
            selectedRating = star.getAttribute('data-value');
            starRatingInput.value = selectedRating;

            stars.forEach(s => {
                s.classList.toggle(
                    'active',
                    s.getAttribute('data-value') <= selectedRating
                );
            });
        });
    });

    // Show modal with selected stars
    submitBtn.addEventListener('click', (e) => {
        e.preventDefault(); // stop form submit (optional)

        modalStars.innerHTML = '⭐'.repeat(selectedRating || 1);
        modal.style.display = 'flex';

        // Submit after delay
        // setTimeout(() => document.getElementById('feedbackForm').submit(), 1500);
    });

    function closeModal() {
        modal.style.display = 'none';
    }
</script>


</body>




</html>