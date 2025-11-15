@extends('tutor.layouts.app')

@section('title', 'Profile')
@section('page-title', 'Profile Settings')

@section('content')
<div class="page-header">
    <h1 class="page-title">👤 Profile Settings</h1>
</div>

<!-- Personal Information -->
<div class="content-panel">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #212529;">Personal Information</h2>
    
    <form action="{{ route('tutor.profile.update') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-group">
                <label>First Name <span style="color: #dc3545;">*</span></label>
                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $tutor->first_name) }}" required>
                @error('first_name')
                    <span style="color: #dc3545; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $tutor->middle_name) }}">
                @error('middle_name')
                    <span style="color: #dc3545; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label>Last Name <span style="color: #dc3545;">*</span></label>
            <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $tutor->last_name) }}" required>
            @error('last_name')
                <span style="color: #dc3545; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>SR Code</label>
                <input type="text" class="form-control" value="{{ $tutor->sr_code }}" disabled style="background: #e9ecef; cursor: not-allowed;">
                <small style="color: #6c757d; font-size: 12px; margin-top: 4px; display: block;">SR Code cannot be changed</small>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" value="{{ $tutor->email }}" disabled style="background: #e9ecef; cursor: not-allowed;">
                <small style="color: #6c757d; font-size: 12px; margin-top: 4px; display: block;">Email cannot be changed</small>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>Year Level</label>
                <input type="text" class="form-control" value="{{ $tutor->year_level }}" disabled style="background: #e9ecef; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label>GWA <span style="color: #dc3545;">*</span></label>
                <input type="number" name="gwa" class="form-control" step="0.01" min="1.00" max="5.00" value="{{ old('gwa', $tutor->gwa) }}" required>
                @error('gwa')
                    <span style="color: #dc3545; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-group">
            <label>Course/Program <span style="color: #dc3545;">*</span></label>
            <input type="text" name="course_program" class="form-control" value="{{ old('course_program', $tutor->course_program) }}" required>
            @error('course_program')
                <span style="color: #dc3545; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label>Tutoring Preference <span style="color: #dc3545;">*</span></label>
            <select name="tutor_level_preference" class="form-control" required>
                <option value="First-Year Students" {{ $tutor->tutor_level_preference === 'First-Year Students' ? 'selected' : '' }}>First-Year Students</option>
                <option value="Second-Year Students" {{ $tutor->tutor_level_preference === 'Second-Year Students' ? 'selected' : '' }}>Second-Year Students</option>
            </select>
            @error('tutor_level_preference')
                <span style="color: #dc3545; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </div>
    </form>
</div>

<!-- Change Password -->
<div class="content-panel">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #212529;">Change Password</h2>
    
    <form action="{{ route('tutor.profile.password') }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Current Password <span style="color: #dc3545;">*</span></label>
            <input type="password" name="current_password" class="form-control" required>
            @error('current_password')
                <span style="color: #dc3545; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>New Password <span style="color: #dc3545;">*</span></label>
                <input type="password" name="password" class="form-control" required>
                @error('password')
                    <span style="color: #dc3545; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label>Confirm New Password <span style="color: #dc3545;">*</span></label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>
        </div>

        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary">Update Password</button>
        </div>
    </form>
</div>

<!-- Update Resume -->
<div class="content-panel">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #212529;">Update Resume</h2>
    
    <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; margin-bottom: 20px;">
        <strong>Current Resume:</strong> 
        @if($tutor->resume_path)
            <a href="{{ asset('storage/' . $tutor->resume_path) }}" target="_blank" style="color: #2d5f8d; text-decoration: underline; margin-left: 8px;">
                View Current Resume
            </a>
        @else
            <span style="color: #6c757d; margin-left: 8px;">No resume uploaded</span>
        @endif
    </div>

    <form action="{{ route('tutor.profile.resume') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Upload New Resume (PDF only, max 5MB) <span style="color: #dc3545;">*</span></label>
            <input type="file" name="resume" class="form-control" accept=".pdf" required>
            @error('resume')
                <span style="color: #dc3545; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
            @enderror
        </div>

        <div style="text-align: right;">
            <button type="submit" class="btn btn-primary">Upload Resume</button>
        </div>
    </form>
</div>

<!-- Account Information -->
<div class="content-panel">
    <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #212529;">Account Information</h2>
    
    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
        <div style="display: grid; gap: 12px;">
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #dee2e6;">
                <strong>Account Status:</strong>
                <span class="badge badge-{{ $tutor->is_approved ? 'success' : 'warning' }}">
                    {{ $tutor->is_approved ? 'Approved' : 'Pending' }}
                </span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #dee2e6;">
                <strong>Member Since:</strong>
                <span>{{ $tutor->created_at->format('F d, Y') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                <strong>Last Updated:</strong>
                <span>{{ $tutor->updated_at->format('F d, Y h:i A') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection