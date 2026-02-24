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
   <small>Table #: {{ $table }}</small>


</div>

<!-- Card -->
<div class="feedback-card">
  <form id="feedbackForm" method="POST" action="{{ route('feedback.store') }}">
    @csrf

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

    <input type="hidden" name="rating" id="starRating" required>

    <!-- Name -->
    <label>Your Name <span>*</span></label>
    <input type="text" name="customer_name" placeholder="Enter your name" required>

    <!-- Table -->
    <label>Table Number</label>
    {{-- ✅ Pass table_number from URL like /feedback?table=1 --}}
<input type="number" name="table_number" value="{{ $table }}" readonly>




    <!-- Comment -->
    <label>Additional Comments (Optional)</label>
    <textarea name="comment" placeholder="Tell us more about your experience..."></textarea>

    <!-- Submit -->
    <button class="submit-btn" type="submit">
      ✈ Submit Feedback
    </button>
  </form>
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
  const form = document.getElementById('feedbackForm');
  const modal = document.getElementById('feedbackModal');
  const modalStars = document.getElementById('modalStars');

  let selectedRating = 0;

  // ⭐ Clickable star rating
  stars.forEach(star => {
    star.addEventListener('click', () => {
      selectedRating = parseInt(star.getAttribute('data-value'), 10);
      starRatingInput.value = selectedRating;

      stars.forEach(s => {
        s.classList.toggle('active', parseInt(s.getAttribute('data-value'), 10) <= selectedRating);
      });
    });
  });

  // Submit form (show modal first)
  form.addEventListener('submit', (e) => {
    if (!selectedRating) {
      e.preventDefault();
      alert('Please select a star rating.');
      return;
    }

    // show modal quickly then submit
    e.preventDefault();
    modalStars.innerHTML = '⭐'.repeat(selectedRating);
    modal.style.display = 'flex';

    setTimeout(() => form.submit(), 600);
  });

  function closeModal() {
    modal.style.display = 'none';
  }
</script>



</body>




</html>