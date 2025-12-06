@extends('tutor.layouts.app')

@section('title', 'Profile')
@section('page-title', 'Profile Settings')

@section('content')
<div class="page-header">
    <h1 class="page-title">Profile Settings</h1>
</div>

<!-- Personal Information -->
<div class="content-panel" style="background: #ffffff; border: 2px solid #dee2e6; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <div style="background: linear-gradient(135deg, #2d5f8d 0%, #1a4366 100%); padding: 20px; border-radius: 8px 8px 0 0; margin: -1px -1px 24px -1px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0; color: #ffffff;">Personal Information</h2>
    </div>
    
    <div style="padding: 0 24px 24px 24px;">
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

                <div class="form-group">
                    <label>Last Name <span style="color: #dc3545;">*</span></label>
                    <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $tutor->last_name) }}" required>
                    @error('last_name')
                        <span style="color: #dc3545; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

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

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Course/Program <span style="color: #dc3545;">*</span></label>
                    <input type="text" name="course_program" class="form-control" value="{{ old('course_program', $tutor->course_program) }}" required>
                    @error('course_program')
                        <span style="color: #dc3545; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Tutoring Preference <span style="color: #dc3545;">*</span></label>
                    <select name="tutor_level_preference" class="form-control" required>
                        <option value="First-Year Students" {{ $tutor->tutor_level_preference === 'First-Year Students' ? 'selected' : '' }}>First-Year Students</option>
                        <option value="Second-Year Students" {{ $tutor->tutor_level_preference === 'Second-Year Students' ? 'selected' : '' }}>Second-Year Students</option>
                    </select>
                    @error('tutor_level_preference')
                        <span style="color: #dc3545; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="text-align: right; margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Update Profile</button>
            </div>
        </form>
    </div>
</div>

<!-- Change Password -->
<div class="content-panel" style="background: #ffffff; border: 2px solid #dee2e6; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <div style="background: linear-gradient(135deg, #2d5f8d 0%, #1a4366 100%); padding: 20px; border-radius: 8px 8px 0 0; margin: -1px -1px 24px -1px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0; color: #ffffff;">Change Password</h2>
    </div>
    
    <div style="padding: 0 24px 24px 24px;">
        <form action="{{ route('tutor.profile.password') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-group" style="grid-column: 1 / -1;">
                    <label>Current Password <span style="color: #dc3545;">*</span></label>
                    <input type="password" name="current_password" class="form-control" required>
                    @error('current_password')
                        <span style="color: #dc3545; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>New Password <span style="color: #dc3545;">*</span></label>
                    <input type="password" name="password" class="form-control" required>
                    <small style="color: #6c757d; font-size: 12px; margin-top: 4px; display: block;">Minimum 8 characters</small>
                    @error('password')
                        <span style="color: #dc3545; font-size: 13px; margin-top: 4px; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label>Confirm New Password <span style="color: #dc3545;">*</span></label>
                    <input type="password" name="password_confirmation" class="form-control" required>
                </div>
            </div>

            <div style="text-align: right; margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Change Password</button>
            </div>
        </form>
    </div>
</div>

<!-- Update Resume -->
<div class="content-panel" style="background: #ffffff; border: 2px solid #dee2e6; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <div style="background: linear-gradient(135deg, #2d5f8d 0%, #1a4366 100%); padding: 20px; border-radius: 8px 8px 0 0; margin: -1px -1px 24px -1px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0; color: #ffffff;">Update Resume</h2>
    </div>
    
    <div style="padding: 0 24px 24px 24px;">
        <div style="background: #f8f9fa; padding: 16px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #e9ecef;">
            <strong style="color: #212529;">Current Resume:</strong> 
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

            <div style="text-align: right; margin-top: 24px;">
                <button type="submit" class="btn btn-primary">Upload Resume</button>
            </div>
        </form>
    </div>
</div>

<!-- Account Information -->
<div class="content-panel" style="background: #ffffff; border: 2px solid #dee2e6; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
    <div style="background: linear-gradient(135deg, #2d5f8d 0%, #1a4366 100%); padding: 20px; border-radius: 8px 8px 0 0; margin: -1px -1px 24px -1px;">
        <h2 style="font-size: 22px; font-weight: 700; margin: 0; color: #ffffff;">Account Information</h2>
    </div>
    
    <div style="padding: 0 24px 24px 24px;">
        <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; border: 1px solid #e9ecef;">
            <div style="display: grid; gap: 12px;">
                <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #dee2e6;">
                    <strong style="color: #495057;">Account Status:</strong>
                    <span class="badge badge-{{ $tutor->is_approved ? 'success' : 'warning' }}">
                        {{ $tutor->is_approved ? 'Approved' : 'Pending' }}
                    </span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid #dee2e6;">
                    <strong style="color: #495057;">Member Since:</strong>
                    <span style="color: #212529;">{{ $tutor->created_at->format('F d, Y') }}</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 12px 0;">
                    <strong style="color: #495057;">Last Updated:</strong>
                    <span style="color: #212529;">{{ $tutor->updated_at->format('F d, Y h:i A') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 14px;
    font-weight: 600;
    color: #212529;
    margin-bottom: 8px;
}

.form-control {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 15px;
    transition: all 0.3s ease;
}

.form-control:focus {
    outline: none;
    border-color: #2d5f8d;
    box-shadow: 0 0 0 3px rgba(45, 95, 141, 0.1);
}

.form-control:disabled {
    background: #e9ecef;
    cursor: not-allowed;
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection