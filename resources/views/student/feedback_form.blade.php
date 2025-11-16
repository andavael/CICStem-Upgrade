@extends('student.layouts.app')

@section('title', 'Give Feedback')
@section('page-title', 'Give Feedback')

@section('content')
<div class="page-header">
    <h1 class="page-title">⭐ Give Feedback</h1>
    <div class="page-actions">
        <a href="{{ route('student.feedback.index') }}" class="btn btn-secondary">Back to Feedback</a>
    </div>
</div>

<div class="content-panel">
    <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">Session Details</h2>
    
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
        <div style="display: grid; grid-template-columns: 150px 1fr; gap: 12px;">
            <div style="font-weight: 600;">Subject:</div>
            <div>{{ $session->subject }}</div>
            
            <div style="font-weight: 600;">Session Code:</div>
            <div>{{ $session->session_code }}</div>
            
            <div style="font-weight: 600;">Tutor:</div>
            <div>{{ $session->tutor ? $session->tutor->full_name : 'N/A' }}</div>
            
            <div style="font-weight: 600;">Date:</div>
            <div>{{ \Carbon\Carbon::parse($session->session_date)->format('F d, Y') }}</div>
        </div>
    </div>
    
    <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">Your Feedback</h2>
    
    <form action="{{ route('student.feedback.store', $session->id) }}" method="POST">
        @csrf
        
        <div style="background: white; border: 2px solid #dee2e6; border-radius: 12px; padding: 30px;">
            <!-- Rating -->
            <div class="form-group">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #495057; margin-bottom: 12px;">
                    Rating <span style="color: #dc3545;">*</span>
                </label>
                <div class="rating-input" style="margin-bottom: 8px;">
                    <input type="radio" name="rating" value="5" id="star5" required>
                    <label for="star5" class="star">★</label>

                    <input type="radio" name="rating" value="4" id="star4">
                    <label for="star4" class="star">★</label>

                    <input type="radio" name="rating" value="3" id="star3">
                    <label for="star3" class="star">★</label>

                    <input type="radio" name="rating" value="2" id="star2">
                    <label for="star2" class="star">★</label>

                    <input type="radio" name="rating" value="1" id="star1">
                    <label for="star1" class="star">★</label>
                </div>

                <div id="rating-text" style="font-size: 14px; color: #6c757d; margin-top: 8px;">
                    Select a rating
                </div>
                @error('rating')
                    <div style="color: #dc3545; font-size: 13px; margin-top: 6px;">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Comment -->
            <div class="form-group" style="margin-top: 24px;">
                <label style="display: block; font-size: 14px; font-weight: 600; color: #495057; margin-bottom: 12px;">
                    Comments (Optional)
                </label>
                <textarea 
                    name="comment" 
                    rows="6" 
                    class="form-control" 
                    placeholder="Share your experience about this tutoring session..."
                    style="resize: vertical;"
                >{{ old('comment') }}</textarea>
                <div style="font-size: 13px; color: #6c757d; margin-top: 6px;">
                    Maximum 1000 characters
                </div>
                @error('comment')
                    <div style="color: #dc3545; font-size: 13px; margin-top: 6px;">{{ $message }}</div>
                @enderror
            </div>
            
            <!-- Submit Button -->
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6; text-align: right;">
                <a href="{{ route('student.feedback.index') }}" class="btn btn-secondary" style="margin-right: 10px;">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    Submit Feedback
                </button>
            </div>
        </div>
    </form>
</div>

<style>
.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 15px;
    font-family: inherit;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #2d5f8d;
    box-shadow: 0 0 0 3px rgba(45, 95, 141, 0.1);
}

/* Star Rating */
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    gap: 8px;
    justify-content: flex-end;
}

.rating-input input[type="radio"] {
    display: none;
}

.rating-input .star {
    font-size: 40px;
    color: #dee2e6;
    cursor: pointer;
    transition: all 0.2s ease;
}

.rating-input .star:hover,
.rating-input .star:hover ~ .star,
.rating-input input[type="radio"]:checked ~ .star {
    color: #ffc107;
}

.rating-input .star:hover {
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .rating-input .star {
        font-size: 32px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingInputs = document.querySelectorAll('input[name="rating"]');
    const ratingText = document.getElementById('rating-text');
    
    const ratingLabels = {
        1: '⭐ Poor',
        2: '⭐⭐ Fair',
        3: '⭐⭐⭐ Good',
        4: '⭐⭐⭐⭐ Very Good',
        5: '⭐⭐⭐⭐⭐ Excellent'
    };
    
    ratingInputs.forEach(input => {
        input.addEventListener('change', function() {
            ratingText.textContent = ratingLabels[this.value];
            ratingText.style.fontWeight = '600';
            ratingText.style.color = '#2d5f8d';
        });
    });
});
</script>
@endsection