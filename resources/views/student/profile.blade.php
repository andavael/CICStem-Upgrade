@extends('student.layouts.app')

@section('title', 'Profile')
@section('page-title', 'Profile')

@section('content')
<div class="page-header">
    <h1 class="page-title">Profile Settings</h1>
</div>

<!-- Profile Information -->
<div class="content-panel">
    <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px; color: #212529;">Profile Information</h2>
    
    <form action="{{ route('student.profile.update') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-grid">
            <div class="form-group">
                <label>First Name <span style="color: #dc3545;">*</span></label>
                <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $student->first_name) }}" required>
                @error('first_name')
                    <div style="color: #dc3545; font-size: 13px; margin-top: 6px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Middle Name</label>
                <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $student->middle_name) }}">
                @error('middle_name')
                    <div style="color: #dc3545; font-size: 13px; margin-top: 6px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>Last Name <span style="color: #dc3545;">*</span></label>
                <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $student->last_name) }}" required>
                @error('last_name')
                    <div style="color: #dc3545; font-size: 13px; margin-top: 6px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>SR Code</label>
                <input type="text" class="form-control" value="{{ $student->sr_code }}" disabled>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" value="{{ $student->email }}" disabled>
            </div>

            <div class="form-group">
                <label>Year Level</label>
                <input type="text" class="form-control" value="{{ $student->year_level }}" disabled>
            </div>

            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Course Program <span style="color: #dc3545;">*</span></label>
                <input type="text" name="course_program" class="form-control" value="{{ old('course_program', $student->course_program) }}" required>
                @error('course_program')
                    <div style="color: #dc3545; font-size: 13px; margin-top: 6px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="text-align: right; margin-top: 24px;">
            <button type="submit" class="btn btn-primary">Update Profile</button>
        </div>
    </form>
</div>

<!-- Change Password -->
<div class="content-panel">
    <h2 style="font-size: 20px; font-weight: 600; margin-bottom: 20px; color: #212529;">Change Password</h2>
    
    <form action="{{ route('student.profile.password') }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="form-grid">
            <div class="form-group" style="grid-column: 1 / -1;">
                <label>Current Password <span style="color: #dc3545;">*</span></label>
                <input type="password" name="current_password" class="form-control" required>
                @error('current_password')
                    <div style="color: #dc3545; font-size: 13px; margin-top: 6px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label>New Password <span style="color: #dc3545;">*</span></label>
                <input type="password" name="password" class="form-control" required>
                <div style="font-size: 13px; color: #6c757d; margin-top: 6px;">
                    Minimum 8 characters
                </div>
                @error('password')
                    <div style="color: #dc3545; font-size: 13px; margin-top: 6px;">{{ $message }}</div>
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
