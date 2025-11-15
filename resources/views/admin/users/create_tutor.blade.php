@extends('admin.layouts.app')

@section('title', 'Add New Tutor')

@section('content')
<div class="page-header">
    <h1 class="page-title">Add New Tutor</h1>
    <div class="page-actions">
        <a href="{{ route('admin.users.index', ['tab' => 'tutors']) }}" class="btn btn-secondary">Back to List</a>
    </div>
</div>

<div class="content-panel">
    <form action="{{ route('admin.tutors.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 20px;">Personal Information</h3>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="first_name">First Name <span class="required">*</span></label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required>
                @error('first_name')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="middle_name">Middle Name</label>
                <input type="text" name="middle_name" id="middle_name" value="{{ old('middle_name') }}">
            </div>
            
            <div class="form-group">
                <label for="last_name">Last Name <span class="required">*</span></label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required>
                @error('last_name')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="sr_code">SR Code <span class="required">*</span></label>
                <input type="text" name="sr_code" id="sr_code" placeholder="XX-XXXXX" value="{{ old('sr_code') }}" required>
                @error('sr_code')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group form-grid-full">
                <label for="email">Email <span class="required">*</span></label>
                <input type="email" name="email" id="email" placeholder="XX-XXXXX@g.batstate-u.edu.ph" value="{{ old('email') }}" required>
                @error('email')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <input type="password" name="password" id="password" required>
                @error('password')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="password_confirmation">Confirm Password <span class="required">*</span></label>
                <input type="password" name="password_confirmation" id="password_confirmation" required>
            </div>
        </div>
        
        <h3 style="font-size: 18px; font-weight: 600; margin: 30px 0 20px;">Academic Information</h3>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="year_level">Year Level <span class="required">*</span></label>
                <select name="year_level" id="year_level" required>
                    <option value="">Select Year Level</option>
                    <option value="1st Year">1st Year</option>
                    <option value="2nd Year">2nd Year</option>
                    <option value="3rd Year">3rd Year</option>
                    <option value="4th Year">4th Year</option>
                </select>
                @error('year_level')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="course_program">Course Program <span class="required">*</span></label>
                <input type="text" name="course_program" id="course_program" value="{{ old('course_program') }}" required>
                @error('course_program')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="gwa">GWA <span class="required">*</span></label>
                <input type="number" step="0.01" min="1.00" max="5.00" name="gwa" id="gwa" value="{{ old('gwa') }}" required>
                @error('gwa')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group">
                <label for="tutor_level_preference">Tutor Level Preference <span class="required">*</span></label>
                <select name="tutor_level_preference" id="tutor_level_preference" required>
                    <option value="">Select Preference</option>
                    <option value="Lower Year">Lower Year</option>
                    <option value="Same Year">Same Year</option>
                    <option value="All">All</option>
                </select>
                @error('tutor_level_preference')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="form-group form-grid-full">
                <label for="resume">Resume (PDF)</label>
                <input type="file" name="resume" id="resume" accept=".pdf">
                @error('resume')
                <div class="error-message show">{{ $message }}</div>
                @enderror
            </div>
        </div>
        
        <h3 style="font-size: 18px; font-weight: 600; margin: 30px 0 20px;">Subjects <span class="required">*</span></h3>
        
        <div class="subjects-grid">
            @foreach($subjects as $subject)
            <div class="checkbox-item">
                <input type="checkbox" name="subjects[]" value="{{ $subject->id }}" id="subject_{{ $subject->id }}">
                <label for="subject_{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</label>
            </div>
            @endforeach
        </div>
        @error('subjects')
        <div class="error-message show">{{ $message }}</div>
        @enderror
        
        <h3 style="font-size: 18px; font-weight: 600; margin: 30px 0 20px;">Status Settings</h3>
        
        <div class="form-grid">
            <div class="form-group">
                <label for="status">Status <span class="required">*</span></label>
                <select name="status" id="status" required>
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="is_approved">Approval Status <span class="required">*</span></label>
                <select name="is_approved" id="is_approved" required>
                    <option value="1">Approved</option>
                    <option value="0">Pending</option>
                </select>
            </div>
        </div>
        
        <div style="display: flex; gap: 12px; margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Create Tutor</button>
            <a href="{{ route('admin.users.index', ['tab' => 'tutors']) }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection