@extends('admin.layouts.app')

@section('title', 'Edit Session')

@section('content')
<div class="page-header">
    <h1 class="page-title">Edit Session</h1>
    <div class="page-actions">
        <a href="{{ route('admin.sessions.show', $session) }}" class="btn btn-secondary">Back to Session</a>
    </div>
</div>

<div class="content-panel">
    <form action="{{ route('admin.sessions.update', $session) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-grid">
            <div class="form-group form-grid-full">
                <label for="subject">Subject <span class="required">*</span></label>
                <input type="text" name="subject" id="subject" value="{{ old('subject', $session->subject) }}" required>
                @error('subject')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="session_date">Session Date <span class="required">*</span></label>
                <input type="date" name="session_date" id="session_date" value="{{ old('session_date', $session->session_date->format('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                @error('session_date')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="session_time">Session Time <span class="required">*</span></label>
                <input type="time" name="session_time" id="session_time" value="{{ old('session_time', $session->session_time) }}" required>
                @error('session_time')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="tutor_id">Assigned Tutor <span class="required">*</span></label>
                <select name="tutor_id" id="tutor_id" required>
                    <option value="">Select Tutor</option>
                    @foreach($tutors as $tutor)
                    <option value="{{ $tutor->id }}" {{ old('tutor_id', $session->tutor_id) == $tutor->id ? 'selected' : '' }}>
                        {{ $tutor->full_name }} ({{ $tutor->year_level }})
                    </option>
                    @endforeach
                </select>
                @error('tutor_id')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="year_level">Year Level <span class="required">*</span></label>
                <select name="year_level" id="year_level" required>
                    <option value="1st Year" {{ old('year_level', $session->year_level) === '1st Year' ? 'selected' : '' }}>1st Year</option>
                    <option value="2nd Year" {{ old('year_level', $session->year_level) === '2nd Year' ? 'selected' : '' }}>2nd Year</option>
                    <option value="3rd Year" {{ old('year_level', $session->year_level) === '3rd Year' ? 'selected' : '' }}>3rd Year</option>
                    <option value="4th Year" {{ old('year_level', $session->year_level) === '4th Year' ? 'selected' : '' }}>4th Year</option>
                    <option value="All" {{ old('year_level', $session->year_level) === 'All' ? 'selected' : '' }}>All Year Levels</option>
                </select>
                @error('year_level')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="capacity">Capacity <span class="required">*</span></label>
                <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $session->capacity) }}" min="1" max="100" required>
                @error('capacity')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="status">Status <span class="required">*</span></label>
                <select name="status" id="status" required>
                    <option value="Scheduled" {{ old('status', $session->status) === 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                    <option value="Ongoing" {{ old('status', $session->status) === 'Ongoing' ? 'selected' : '' }}>Ongoing</option>
                    <option value="Completed" {{ old('status', $session->status) === 'Completed' ? 'selected' : '' }}>Completed</option>
                    <option value="Cancelled" {{ old('status', $session->status) === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                @error('status')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group form-grid-full">
                <label for="google_meet_link">Google Meet Link <span class="required">*</span></label>
                <input type="url" name="google_meet_link" id="google_meet_link" value="{{ old('google_meet_link', $session->google_meet_link) }}" required>
                @error('google_meet_link')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group form-grid-full">
                <label for="description">Description (Optional)</label>
                <textarea name="description" id="description" rows="4" style="width: 100%; padding: 12px; border: 2px solid #dee2e6; border-radius: 8px; font-family: 'Inter', sans-serif;">{{ old('description', $session->description) }}</textarea>
                @error('description')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div style="display: flex; gap: 12px; margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Update Session</button>
            <a href="{{ route('admin.sessions.show', $session) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dateInput = document.getElementById('session_date');
    const timeInput = document.getElementById('session_time');
    
    function validateDateTime() {
        if (!dateInput.value || !timeInput.value) return;
        
        const selectedDateTime = new Date(dateInput.value + 'T' + timeInput.value);
        const now = new Date();
        
        if (selectedDateTime < now) {
            timeInput.setCustomValidity('The selected date and time has already passed. Please choose a future date and time.');
        } else {
            timeInput.setCustomValidity('');
        }
    }
    
    dateInput.addEventListener('change', validateDateTime);
    timeInput.addEventListener('change', validateDateTime);
    
    // Run validation on page load to catch any initial issues
    validateDateTime();
});
</script>
@endsection