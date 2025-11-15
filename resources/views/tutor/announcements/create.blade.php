@extends('tutor.layouts.app')

@section('title', 'Create Announcement')
@section('page-title', 'Create Announcement')

@section('content')
<div class="page-header">
    <h1 class="page-title">📢 Create New Announcement</h1>
    <div class="page-actions">
        <a href="{{ route('tutor.announcements.index') }}" class="btn btn-secondary">Back to Announcements</a>
    </div>
</div>

<div class="content-panel">
    <form action="{{ route('tutor.announcements.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <label for="title">Announcement Title <span class="required">*</span></label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="Enter announcement title" required>
            @error('title')
            <div class="error-message show">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="content">Content <span class="required">*</span></label>
            <textarea name="content" id="content" class="form-control" rows="8" placeholder="Enter announcement content..." required>{{ old('content') }}</textarea>
            @error('content')
            <div class="error-message show">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="category">Category <span class="required">*</span></label>
                <select name="category" id="category" class="form-control" required>
                    <option value="">Select Category</option>
                    <option value="General" {{ old('category') === 'General' ? 'selected' : '' }}>General</option>
                    <option value="Event" {{ old('category') === 'Event' ? 'selected' : '' }}>Event</option>
                    <option value="Maintenance" {{ old('category') === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="Important" {{ old('category') === 'Important' ? 'selected' : '' }}>Important</option>
                </select>
                @error('category')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="target_audience">Target Audience <span class="required">*</span></label>
                <select name="target_audience" id="target_audience" class="form-control" required>
                    <option value="">Select Audience</option>
                    <option value="All" {{ old('target_audience') === 'All' ? 'selected' : '' }}>All Users</option>
                    <option value="Students" {{ old('target_audience') === 'Students' ? 'selected' : '' }}>Students Only</option>
                    <option value="Tutors" {{ old('target_audience') === 'Tutors' ? 'selected' : '' }}>Tutors Only</option>
                </select>
                @error('target_audience')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="priority">Priority Level <span class="required">*</span></label>
                <select name="priority" id="priority" class="form-control" required>
                    <option value="">Select Priority</option>
                    <option value="Normal" {{ old('priority') === 'Normal' ? 'selected' : '' }}>Normal</option>
                    <option value="High" {{ old('priority') === 'High' ? 'selected' : '' }}>High</option>
                    <option value="Urgent" {{ old('priority') === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
                @error('priority')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div style="display: flex; gap: 12px; margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Post Announcement</button>
            <a href="{{ route('tutor.announcements.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<!-- Preview -->
<div class="content-panel">
    <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px;">Preview</h2>
    
    <div style="border-left: 4px solid #2d5f8d; padding: 20px; background: #f8f9fa; border-radius: 8px;">
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px;" id="preview-title">Announcement Title</h3>
        <p style="margin: 0; color: #495057; line-height: 1.6; white-space: pre-wrap;" id="preview-content">Your announcement content will appear here...</p>
        <div style="margin-top: 12px; display: flex; gap: 8px;" id="preview-badges">
            <span class="badge badge-info">Category</span>
            <span class="badge badge-secondary">Audience</span>
            <span class="badge badge-secondary">Priority</span>
        </div>
    </div>
</div>

<style>
.required {
    color: #dc3545;
}

.error-message {
    color: #dc3545;
    font-size: 13px;
    margin-top: 4px;
    display: none;
}

.error-message.show {
    display: block;
}
</style>

@push('scripts')
<script>
// Live preview
document.getElementById('title').addEventListener('input', function() {
    document.getElementById('preview-title').textContent = this.value || 'Announcement Title';
});

document.getElementById('content').addEventListener('input', function() {
    document.getElementById('preview-content').textContent = this.value || 'Your announcement content will appear here...';
});

function updatePreview() {
    const category = document.getElementById('category').value;
    const audience = document.getElementById('target_audience').value;
    const priority = document.getElementById('priority').value;
    
    let badgeHTML = '';
    if (category) {
        const categoryClass = category === 'Important' ? 'badge-danger' : 'badge-info';
        badgeHTML += `<span class="badge ${categoryClass}">${category}</span>`;
    }
    if (audience) {
        badgeHTML += `<span class="badge badge-secondary">${audience}</span>`;
    }
    if (priority) {
        const priorityClass = priority === 'Urgent' ? 'badge-danger' : priority === 'High' ? 'badge-warning' : 'badge-secondary';
        badgeHTML += `<span class="badge ${priorityClass}">${priority}</span>`;
    }
    
    document.getElementById('preview-badges').innerHTML = badgeHTML || '<span class="badge badge-secondary">No category selected</span>';
}

document.getElementById('category').addEventListener('change', updatePreview);
document.getElementById('target_audience').addEventListener('change', updatePreview);
document.getElementById('priority').addEventListener('change', updatePreview);
</script>
@endpush
@endsection