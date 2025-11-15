@extends('tutor.layouts.app')

@section('title', 'Edit Announcement')
@section('page-title', 'Edit Announcement')

@section('content')
<div class="page-header">
    <h1 class="page-title">📝 Edit Announcement</h1>
    <div class="page-actions">
        <a href="{{ route('tutor.announcements.index') }}" class="btn btn-secondary">Back to Announcements</a>
    </div>
</div>

<div class="content-panel">
    <form action="{{ route('tutor.announcements.update', $announcement->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <label for="title">Announcement Title <span class="required">*</span></label>
            <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $announcement->title) }}" required>
            @error('title')
            <div class="error-message show">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-group">
            <label for="content">Content <span class="required">*</span></label>
            <textarea name="content" id="content" class="form-control" rows="8" required>{{ old('content', $announcement->content) }}</textarea>
            @error('content')
            <div class="error-message show">{{ $message }}</div>
            @enderror
        </div>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="category">Category <span class="required">*</span></label>
                <select name="category" id="category" class="form-control" required>
                    <option value="General" {{ old('category', $announcement->category) === 'General' ? 'selected' : '' }}>General</option>
                    <option value="Event" {{ old('category', $announcement->category) === 'Event' ? 'selected' : '' }}>Event</option>
                    <option value="Maintenance" {{ old('category', $announcement->category) === 'Maintenance' ? 'selected' : '' }}>Maintenance</option>
                    <option value="Important" {{ old('category', $announcement->category) === 'Important' ? 'selected' : '' }}>Important</option>
                </select>
                @error('category')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="target_audience">Target Audience <span class="required">*</span></label>
                <select name="target_audience" id="target_audience" class="form-control" required>
                    <option value="All" {{ old('target_audience', $announcement->target_audience) === 'All' ? 'selected' : '' }}>All Users</option>
                    <option value="Students" {{ old('target_audience', $announcement->target_audience) === 'Students' ? 'selected' : '' }}>Students Only</option>
                    <option value="Tutors" {{ old('target_audience', $announcement->target_audience) === 'Tutors' ? 'selected' : '' }}>Tutors Only</option>
                </select>
                @error('target_audience')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="priority">Priority Level <span class="required">*</span></label>
                <select name="priority" id="priority" class="form-control" required>
                    <option value="Normal" {{ old('priority', $announcement->priority) === 'Normal' ? 'selected' : '' }}>Normal</option>
                    <option value="High" {{ old('priority', $announcement->priority) === 'High' ? 'selected' : '' }}>High</option>
                    <option value="Urgent" {{ old('priority', $announcement->priority) === 'Urgent' ? 'selected' : '' }}>Urgent</option>
                </select>
                @error('priority')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <div style="background: #e3f2fd; border-left: 4px solid #2d5f8d; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
            <strong>ℹ️ Info:</strong> Changes will be visible immediately after updating. The announcement was originally created on {{ $announcement->created_at->format('F d, Y \a\t h:i A') }}.
        </div>
        
        <div style="display: flex; gap: 12px; margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Update Announcement</button>
            <a href="{{ route('tutor.announcements.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
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
@endsection